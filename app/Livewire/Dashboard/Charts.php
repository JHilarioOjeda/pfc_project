<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Tarima as TarimaModel;
use App\Models\WorkLine;

class Charts extends Component
{
    public string $fromDate;
    public string $toDate;
    public string $lineId = 'all';

    public $lines = [];

    public array $tarimasChart = [
        'labels' => [],
        'data' => [],
    ];

    public array $processesByStatusChart = [
        'labels' => [],
        'data' => [],
    ];

    public array $piecesDecimetersChart = [
        'labels' => [],
        'pieces' => [],
        'decimeters' => [],
    ];

    public int $totalTarimas = 0;
    public int $totalProcesses = 0;
    public float $totalPieces = 0.0;
    public float $totalDecimeters = 0.0;

    public function mount(): void
    {
        $this->toDate = now()->toDateString();
        $this->fromDate = now()->subDays(6)->toDateString();
        $this->lines = WorkLine::orderBy('name')->get();

        $this->refreshCharts(false);
    }

    public function updatedFromDate(): void
    {
        $this->refreshCharts();
    }

    public function updatedToDate(): void
    {
        $this->refreshCharts();
    }

    public function updatedLineId(): void
    {
        $this->refreshCharts();
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

    protected function normalizeLineId(): ?int
    {
        return $this->lineId !== 'all' && $this->lineId !== '' ? (int) $this->lineId : null;
    }

    protected function buildTarimasByDayChart(Carbon $from, Carbon $to, ?int $lineId): array
    {
        $query = TarimaModel::query()
            ->whereBetween('register_date', [$from, $to]);

        if ($lineId) {
            $query->whereIn('id', function ($sub) use ($lineId) {
                $sub->select('tarima_nps.id_tarima')
                    ->from('tarima_nps')
                    ->join('proccess', 'proccess.id_tarima_np', '=', 'tarima_nps.id')
                    ->where('proccess.id_line', $lineId);
            });
        }

        $rows = $query
            ->selectRaw('DATE(register_date) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $days = [];
        for ($cursor = $from->copy()->startOfDay(); $cursor->lte($to); $cursor->addDay()) {
            $days[$cursor->toDateString()] = 0;
        }

        foreach ($rows as $row) {
            $day = Carbon::parse($row->day)->toDateString();
            $days[$day] = (int) $row->total;
        }

        return [
            'labels' => array_keys($days),
            'data' => array_values($days),
        ];
    }

    protected function buildProcessesByStatusChart(Carbon $from, Carbon $to, ?int $lineId): array
    {
        $rows = DB::table('proccess')
            ->whereBetween('proccess.created_at', [$from, $to])
            ->when($lineId, fn ($q) => $q->where('proccess.id_line', $lineId))
            ->select('proccess.status', DB::raw('COUNT(*) as total'))
            ->groupBy('proccess.status')
            ->get();

        $baseStatuses = [
            'pending' => 'Pendiente',
            'inprocess' => 'En proceso',
            'finished' => 'Terminado',
        ];

        $counts = [];
        foreach ($baseStatuses as $status => $label) {
            $counts[$status] = 0;
        }

        foreach ($rows as $row) {
            $status = (string) ($row->status ?? '');
            if (array_key_exists($status, $counts)) {
                $counts[$status] = (int) $row->total;
            } elseif ($status !== '') {
                $baseStatuses[$status] = $status;
                $counts[$status] = (int) $row->total;
            }
        }

        $colors = [
            'pending'   => '#E5E7EB',
            'inprocess' => '#FDE68A',
            'finished'  => '#BBF7D0',
        ];

        $data = [
            'labels' => array_values($baseStatuses),
            'data' => array_values($counts),
            'colors' => array_values($colors),
        ];

        //dd($data);

        return $data;
    }

    protected function buildPiecesAndDecimetersByDayChart(Carbon $from, Carbon $to, ?int $lineId): array
    {
        $rows = DB::table('charges')
            ->join('proccess', 'proccess.id', '=', 'charges.id_proccess')
            ->join('tarima_nps', 'tarima_nps.id', '=', 'proccess.id_tarima_np')
            ->join('number_parts', 'number_parts.id', '=', 'tarima_nps.id_np')
            ->whereBetween('charges.made_date', [$from, $to])
            ->when($lineId, fn ($q) => $q->where('proccess.id_line', $lineId))
            ->selectRaw('DATE(charges.made_date) as day, SUM(charges.quantity_pieces) as pieces, SUM(charges.quantity_pieces * COALESCE(number_parts.decimeters, 0)) as decimeters')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $days = [];
        for ($cursor = $from->copy()->startOfDay(); $cursor->lte($to); $cursor->addDay()) {
            $days[$cursor->toDateString()] = ['pieces' => 0.0, 'decimeters' => 0.0];
        }

        foreach ($rows as $row) {
            $day = Carbon::parse($row->day)->toDateString();
            $days[$day] = [
                'pieces' => (float) $row->pieces,
                'decimeters' => round((float) $row->decimeters, 2),
            ];
        }

        return [
            'labels' => array_keys($days),
            'pieces' => array_map(fn ($d) => $d['pieces'], array_values($days)),
            'decimeters' => array_map(fn ($d) => $d['decimeters'], array_values($days)),
        ];
    }

    public function refreshCharts(bool $dispatch = true): void
    {
        [$from, $to] = $this->normalizeDateRange();
        $lineId = $this->normalizeLineId();

        $this->tarimasChart = $this->buildTarimasByDayChart($from, $to, $lineId);
        $this->processesByStatusChart = $this->buildProcessesByStatusChart($from, $to, $lineId);
        $this->piecesDecimetersChart = $this->buildPiecesAndDecimetersByDayChart($from, $to, $lineId);

        $this->totalTarimas = (int) array_sum($this->tarimasChart['data']);
        $this->totalProcesses = (int) array_sum($this->processesByStatusChart['data']);
        $this->totalPieces = (float) array_sum($this->piecesDecimetersChart['pieces']);
        $this->totalDecimeters = round((float) array_sum($this->piecesDecimetersChart['decimeters']), 2);

        if ($dispatch) {
            $this->dispatch('dashboard-charts-updated',
                tarimas: $this->tarimasChart,
                processes: $this->processesByStatusChart,
                piecesDecimeters: $this->piecesDecimetersChart,
            );
        }
    }

    public function render()
    {
        return view('livewire.dashboard.charts');
    }
}
