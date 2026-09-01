<?php

namespace App\Livewire\Processes;

use App\Models\TarimaNp;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class ReportEntries extends Component
{
    use WithPagination;

    public string $fromDate;
    public string $toDate;
    public string $search = '';

    public int $totalRows = 0;

    // Columnas del formato original "CONTROL DE ENTRADAS Y SALIDAS".
    // Las que no tienen dato de origen en el sistema se dejan vacías en la tabla y el CSV.
    public const HEADERS = [
        'folio'              => 'Folio (ID de proceso)',
        'estatus'            => 'Estatus',
        'fecha_recepcion'    => 'Fecha de recepción',
        'hora_recepcion'     => 'Hora de recepción',
        'cliente'            => 'Cliente',
        'oc'                 => 'OC',
        'of'                 => 'OF',
        'urgencia'           => 'Urgencia',
        'tarima'             => 'Tarima',
        'numero_parte'       => 'N° de parte',
        'cantidad_pzas'      => 'Cantidad de pzas',
        'acabado'            => 'Acabado',
        'personal_recibe'    => 'Personal que recibe',
        'fecha_produccion'   => 'Fecha de producción',
        'personal_libera_pt' => 'Personal que libera PT',
        'reporte_calidad'    => 'Reporte de calidad',
        'fecha_compromiso'   => 'Fecha compromiso',
        'fecha_salida'       => 'Fecha de salida',
        'numero_remision'    => 'N° de remisión',
        'numero_factura'     => 'N° factura',
        'personal_entrega'   => 'Personal que entrega',
        'observaciones'      => 'Observaciones',
    ];

    // Columnas del formato original que no se capturan en ningún lugar del sistema.
    public const UNAVAILABLE_COLUMNS = [
        'urgencia', 'fecha_compromiso', 'fecha_salida',
        'numero_remision', 'numero_factura', 'personal_entrega',
    ];

    public function mount(): void
    {
        $this->toDate = now()->toDateString();
        $this->fromDate = now()->startOfMonth()->toDateString();
    }

    public function updatingFromDate(): void
    {
        $this->resetPage();
    }

    public function updatingToDate(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    protected function normalizeDateRange(): array
    {
        $from = Carbon::parse($this->fromDate)->startOfDay();
        $to = Carbon::parse($this->toDate)->endOfDay();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }

    protected function baseQuery()
    {
        [$from, $to] = $this->normalizeDateRange();
        $term = trim($this->search);

        return TarimaNp::query()
            ->with([
                'tarima.customer',
                'tarima.registeredBy',
                'numberPart',
                'proccesses.charges.whoFree',
                'proccesses.meditionsReports.folios',
            ])
            ->whereHas('tarima', function ($q) use ($from, $to) {
                $q->whereBetween('register_date', [$from, $to]);
            })
            ->when($term !== '', function ($q) use ($term) {
                $like = "%{$term}%";
                $q->where(function ($qq) use ($like) {
                    $qq->whereHas('tarima', function ($t) use ($like) {
                            $t->where('serial_number', 'LIKE', $like)
                                ->orWhereHas('customer', fn ($c) => $c->where('name', 'LIKE', $like));
                        })
                        ->orWhereHas('numberPart', fn ($n) => $n->where('partnumber', 'LIKE', $like))
                        ->orWhere('oc', 'LIKE', $like)
                        ->orWhere('of', 'LIKE', $like);
                });
            })
            ->orderByDesc('id');
    }

    protected function transformRow(TarimaNp $tarimaNp): array
    {
        $tarima = $tarimaNp->tarima;
        $customer = $tarima?->customer;
        $numberPart = $tarimaNp->numberPart;
        $process = $tarimaNp->proccesses->sortByDesc('id')->first();

        $lastFreedCharge = $process
            ? $process->charges->whereNotNull('who_free')->sortByDesc('free_date')->first()
            : null;

        $latestReport = $process
            ? $process->meditionsReports->sortByDesc('id')->first()
            : null;

        $estatus = match (true) {
            !$process => 'Pendiente de proceso',
            $process->status === 'pending' => 'Pendiente',
            $process->status === 'inprocess' => 'En proceso',
            $process->status === 'finished' => 'Terminado',
            default => ucfirst((string) $process->status),
        };

        return [
            'folio'              => $process?->id,
            'estatus'            => $estatus,
            'fecha_recepcion'    => $tarima?->register_date?->format('d/m/Y'),
            'hora_recepcion'     => $tarima?->register_date?->format('H:i'),
            'cliente'            => $customer?->name,
            'oc'                 => $tarimaNp->oc,
            'of'                 => $tarimaNp->of,
            'urgencia'           => null,
            'tarima'             => $tarima?->serial_number,
            'numero_parte'       => $numberPart?->partnumber,
            'cantidad_pzas'      => $tarimaNp->quantity !== null ? round((float) $tarimaNp->quantity, 2) : null,
            'acabado'            => $numberPart?->process,
            'personal_recibe'    => $tarima?->registeredBy?->name,
            'fecha_produccion'   => $process?->start_date?->format('d/m/Y'),
            'personal_libera_pt' => $lastFreedCharge?->whoFree?->name,
            'reporte_calidad'    => $latestReport?->folios->sortByDesc('id')->first()?->folio,
            'fecha_compromiso'   => null,
            'fecha_salida'       => null,
            'numero_remision'    => null,
            'numero_factura'     => null,
            'personal_entrega'   => null,
            'observaciones'      => $latestReport?->notes,
        ];
    }

    /**
     * @return Collection<int, array>
     */
    protected function transformRows(iterable $tarimaNps): Collection
    {
        return collect($tarimaNps)->map(fn (TarimaNp $tarimaNp) => $this->transformRow($tarimaNp));
    }

    public function exportCsv()
    {
        $rows = $this->transformRows($this->baseQuery()->get());
        $filename = 'control_entradas_salidas_' . $this->fromDate . '_a_' . $this->toDate . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            // BOM para que Excel detecte UTF-8 y no rompa acentos/ñ.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, array_values(self::HEADERS));

            foreach ($rows as $row) {
                fputcsv($handle, array_values($row));
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function render()
    {
        $paginated = $this->baseQuery()->paginate(25);
        $this->totalRows = $paginated->total();

        $rows = $this->transformRows($paginated->getCollection());

        return view('livewire.processes.report-entries', [
            'paginated' => $paginated,
            'rows' => $rows,
        ]);
    }
}
