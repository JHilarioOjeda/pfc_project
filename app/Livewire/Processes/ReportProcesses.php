<?php

namespace App\Livewire\Processes;

use App\Models\Proccess;
use App\Models\StartChecklist;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReportProcesses extends Component
{
    public $date;
    public $leader_id;

    public $leaders = [];
    public $processes = [];
    public $checklist = null;
    public $deadtimeByType = [];
    public $totalDeadtime = 0.0;

    public function mount(): void
    {
        $this->date = now()->toDateString();

        // Carga líderes y administradores para el dropdown 
        $this->leaders = User::query()
            ->whereIn('user_type', [1, 3])
            ->orderBy('name')
            ->get();

        $authUser = Auth::user();
        if ($authUser && in_array((int) ($authUser->user_type ?? 0), [1, 3])) {
            $this->leader_id = $authUser->id;
        } elseif ($this->leaders->isNotEmpty()) {
            $this->leader_id = $this->leaders->first()->id;
        }

        $this->loadData();
    }

    public function loadData(): void
    {
        $this->processes = [];
        $this->checklist = null;
        $this->deadtimeByType = [];
        $this->totalDeadtime = 0.0;

        if (!$this->leader_id || !$this->date) {
            return;
        }

        $this->checklist = StartChecklist::query()
            ->where('id_user', $this->leader_id)
            ->whereDate('register_date', $this->date)
            ->first();

        $this->processes = Proccess::query()
            ->with(['tarimaNp.tarima.customer', 'tarimaNp.numberPart', 'whomade', 'line', 'charges.timeouts'])
            ->where('who_made', $this->leader_id)
            ->whereDate('start_date', $this->date)
            ->orderBy('id')
            ->get();

        foreach ($this->processes as $process) {
            foreach ($process->charges as $charge) {
                foreach ($charge->timeouts as $timeout) {
                    $label = (string) $timeout->type;
                    $hours = (float) $timeout->hours;

                    if (!isset($this->deadtimeByType[$label])) {
                        $this->deadtimeByType[$label] = 0.0;
                    }

                    $this->deadtimeByType[$label] += $hours;
                    $this->totalDeadtime += $hours;
                }
            }
        }
    }

    public function render(){
        return view('livewire.processes.report-processes');
    }
}
