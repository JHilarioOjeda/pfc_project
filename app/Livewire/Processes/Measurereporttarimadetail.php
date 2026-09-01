<?php

namespace App\Livewire\Processes;

use Livewire\Component;
use App\Models\Tarima;
use App\Models\MeditionsReport;
use App\Models\MeditionsReportFolio;
use App\Models\MeditionsreportObservation;
use App\Models\Proccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class Measurereporttarimadetail extends Component
{
    public $idtarima;
    public $tarima;
    public $reports = [];
    public $selectedReports = [];
    public $selectAll = false;
    public $folio;

    // Indica si el folio es obligatorio para poder imprimir: solo lo es
    // cuando la combinación exacta de reportes seleccionados nunca se ha
    // impreso junta antes. Si ya existe un folio con exactamente esos mismos
    // reportes (ni más ni menos), se trata de una reimpresión y no se vuelve
    // a pedir folio ni se toca nada en la base de datos.
    public $requiresFolio = false;

    // Último folio registrado en el sistema (de cualquier tarima), como
    // referencia para que el usuario sepa qué folio sigue al capturar uno
    // nuevo.
    public $lastFolioUsed;

    public function mount($id)
    {
        $this->idtarima = $id;

        $this->tarima = Tarima::with('customer')->findOrFail($id);

        $this->loadReports();
        $this->loadLastFolioUsed();
    }

    protected function loadLastFolioUsed(): void
    {
        $this->lastFolioUsed = MeditionsReportFolio::query()
            ->orderByDesc('id')
            ->value('folio');
    }

    protected function loadReports(): void
    {
        $reports = MeditionsReport::query()
            ->select('meditions_report.*')
            ->join('proccess', 'proccess.id', '=', 'meditions_report.id_proccess')
            ->join('tarima_nps', 'tarima_nps.id', '=', 'proccess.id_tarima_np')
            ->where('tarima_nps.id_tarima', $this->idtarima)
            ->with(['observations', 'proccess.tarimaNp.numberPart', 'folios'])
            ->orderBy('meditions_report.register_date', 'desc')
            ->get();

        $this->reports = $reports->map(function ($report) {
            return [
                'id' => $report->id,
                // Folio más reciente del reporte (según a qué lotes ha sido
                // impreso), no la columna vieja `folio`, que ya no se
                // actualiza y quedaría congelada.
                'folio' => $report->folios->sortByDesc('id')->first()?->folio,
                'requirement' => $report->requirement,
                'method' => $report->method,
                'register_date' => $report->register_date,
                'process_id' => $report->id_proccess,
                'partnumber' => optional(optional(optional($report->proccess)->tarimaNp)->numberPart)->partnumber,
                'oc' => optional(optional($report->proccess)->tarimaNp)->oc,
                'of' => optional(optional($report->proccess)->tarimaNp)->of,
                'observations' => $report->observations->map(function ($obs) {
                    return [
                        'id' => $obs->id,
                        'thickness_in_microns' => $obs->thickness_in_microns,
                        'visual_appearance' => $obs->visual_appearance,
                    ];
                })->toArray(),
            ];
        })->toArray();
    }

    public function updatedSelectAll($value): void
    {
        $this->selectedReports = $value
            ? collect($this->reports)->pluck('id')->toArray()
            : [];

        $this->refreshFolioRequirement();
    }

    public function updatedSelectedReports(): void
    {
        $this->selectAll = count($this->selectedReports) === count($this->reports) && count($this->reports) > 0;

        $this->refreshFolioRequirement();
    }

    // Ids únicos y normalizados a entero de los reportes seleccionados.
    protected function normalizedSelectedIds()
    {
        return collect($this->selectedReports)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }

    // Busca un folio ya existente cuya combinación de reportes enlazados sea
    // EXACTAMENTE igual al conjunto de ids dado (mismo tamaño y mismos
    // miembros, no un subconjunto ni un superconjunto). Si existe, esta
    // selección ya se imprimió junta antes y se trata de una reimpresión.
    protected function findExactFolioForSelection($selectedIds): ?MeditionsReportFolio
    {
        if ($selectedIds->isEmpty()) {
            return null;
        }

        $count = $selectedIds->count();

        return MeditionsReportFolio::query()
            ->whereHas('reports', function ($query) use ($selectedIds) {
                $query->whereIn('meditions_report.id', $selectedIds->all());
            }, '=', $count)
            ->has('reports', '=', $count)
            ->first();
    }

    // Recalcula si el folio sigue siendo obligatorio según la selección
    // actual: solo se pide cuando esta combinación exacta de reportes nunca
    // se ha impreso junta antes.
    protected function refreshFolioRequirement(): void
    {
        $selectedIds = $this->normalizedSelectedIds();

        $this->requiresFolio = $selectedIds->isNotEmpty()
            && $this->findExactFolioForSelection($selectedIds) === null;
    }

    public function printSelected()
    {
        if (empty($this->selectedReports)) {
            LivewireAlert::title('Selecciona al menos un reporte para imprimir.')
                ->warning()
                ->show();
            return;
        }

        $selectedIds = $this->normalizedSelectedIds();
        $existingFolio = $this->findExactFolioForSelection($selectedIds);

        if ($existingFolio) {
            // Reimpresión: esta combinación exacta ya tiene folio. No se
            // pide nada nuevo ni se toca la base de datos.
            $this->requiresFolio = false;
            $folioValue = $existingFolio->folio;
        } else {
            $this->requiresFolio = true;

            $this->validate([
                'folio' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('meditions_report_folios', 'folio'),
                ],
            ], [
                'folio.required' => 'Esta combinación de reportes nunca se ha impreso junta. Ingresa un folio nuevo para poder imprimir.',
                'folio.unique' => 'Ese folio ya fue utilizado. Ingresa uno diferente.',
            ], [
                'folio' => 'Folio',
            ]);

            // Se crea un folio nuevo y se enlaza a los reportes seleccionados.
            // Nunca se actualiza ni se borra un folio existente: los folios
            // que ya tenían estos u otros reportes quedan intactos.
            $newFolio = DB::transaction(function () use ($selectedIds) {
                $folio = MeditionsReportFolio::create(['folio' => $this->folio]);
                $folio->reports()->attach($selectedIds->all());

                return $folio;
            });

            $folioValue = $newFolio->folio;
            $this->folio = null;
            $this->lastFolioUsed = $folioValue;
        }

        $ids = $selectedIds->implode(',');

        return redirect()->route('reporttarimas.print', [
            'tarima' => $this->idtarima,
            'reports' => $ids,
            'folio' => $folioValue,
        ]);
    }

    public function render()
    {
        return view('livewire.processes.measurereporttarimadetail');
    }
}
