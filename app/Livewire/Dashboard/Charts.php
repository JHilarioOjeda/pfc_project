<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Tarima as TarimaModel;

class Charts extends Component
{
    public string $fromDate;
    public string $toDate;

    public array $tarimasChart = [
        'labels' => [],
        'data' => [],
    ];

    public array $processesByStatusChart = [
        'labels' => [],
        'data' => [],
    ];

    public function mount(): void
    {
        $this->toDate = now()->toDateString();
        $this->fromDate = now()->subDays(6)->toDateString();

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

    protected function normalizeDateRange(): array
    {
        $from = Carbon::parse($this->fromDate)->startOfDay();
        $to = Carbon::parse($this->toDate)->endOfDay();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }

    protected function buildTarimasByDayChart(Carbon $from, Carbon $to): array
    {
        $rows = TarimaModel::query()
            ->whereBetween('register_date', [$from, $to])
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

    protected function buildProcessesByStatusChart(Carbon $from, Carbon $to): array
    {
        $rows = DB::table('proccess')
            ->join('tarima_nps', 'proccess.id_tarima_np', '=', 'tarima_nps.id')
            ->join('tarima', 'tarima_nps.id_tarima', '=', 'tarima.id')
            ->whereBetween('tarima.register_date', [$from, $to])
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

        return [
            'labels' => array_values($baseStatuses),
            'data' => array_values($counts),
        ];
    }

    public function refreshCharts(bool $dispatch = true): void
    {
        [$from, $to] = $this->normalizeDateRange();

        $this->tarimasChart = $this->buildTarimasByDayChart($from, $to);
        $this->processesByStatusChart = $this->buildProcessesByStatusChart($from, $to);

        if ($dispatch) {
            $this->dispatch('dashboard-charts-updated',
                tarimas: $this->tarimasChart,
                processes: $this->processesByStatusChart,
            );
        }
    }

    public function render()
    {
        return view('livewire.dashboard.charts');
    }
}
