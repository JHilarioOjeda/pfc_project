<?php

namespace App\Livewire\Processes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tarima;

class Measurereporttarimas extends Component
{
    use WithPagination;

    public $search = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $tarimas = Tarima::with('customer')
            ->withCount([
                'tarimaNps as finished_processes_with_report_count' => function ($query) {
                    $query->join('proccess', 'proccess.id_tarima_np', '=', 'tarima_nps.id')
                        ->join('meditions_report', 'meditions_report.id_proccess', '=', 'proccess.id')
                        ->where('proccess.status', 'finished');
                },
            ])
            ->having('finished_processes_with_report_count', '>', 0)
            ->when($this->search, function ($query) {
                $query->where('serial_number', 'LIKE', "%{$this->search}%")
                    ->orWhereHas('customer', function ($q) {
                        $q->where('name', 'LIKE', "%{$this->search}%");
                    });
            })
            ->orderBy('id', 'DESC')
            ->paginate(25);

        return view('livewire.processes.measurereporttarimas', [
            'tarimas' => $tarimas,
        ]);
    }
}
