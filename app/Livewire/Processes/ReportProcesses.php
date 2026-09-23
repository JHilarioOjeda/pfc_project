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
    public $availableDates = [];
    public $deadtimeByType = [];
    public $totalDeadtime = 0.0;
    public $totalPieces = 0.0;
    public $totalDecimeters = 0.0;

    public function mount(): void
    {
        $this->date = now()->toDateString();

        // Carga líderes y administradores para el dropdown 
        $this->leaders = User::query()
            ->whereIn('user_type', [1, 3, 6]) // 1: Admin, 3: Líder, 6: Coordinador
            ->orderBy('name')
            ->get();

        $authUser = Auth::user();
        if ($authUser && in_array((int) ($authUser->user_type ?? 0), [1, 3, 6])) {
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
        $this->totalPieces = 0.0;
        $this->totalDecimeters = 0.0;

        $this->loadAvailableDates();

        if (!$this->leader_id || !$this->date) {
            return;
        }

        // El modo "todos los líderes" se computa en render()
        if ((string) $this->leader_id === 'all') {
            return;
        }

        $this->checklist = StartChecklist::query()
            ->where('id_user', $this->leader_id)
            ->whereDate('register_date', $this->date)
            ->first();

        $this->processes = Proccess::query()
            ->with([
                'tarimaNp.tarima.customer',
                'tarimaNp.numberPart',
                'whomade',
                'line',
                'charges' => fn ($q) => $q->whereDate('made_date', $this->date)->with('timeouts'),
            ])
            ->where('who_made', $this->leader_id)
            ->where($this->dayActivityFilter())
            ->orderBy('id')
            ->get();

        foreach ($this->processes as $process) {
            $decimeters = $process->tarimaNp->numberPart->decimeters ?? 0;

            foreach ($process->charges as $charge) {
                $pieces = (float) $charge->quantity_pieces;
                $this->totalPieces += $pieces;
                $this->totalDecimeters += $pieces * $decimeters;

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

    /**
     * Un proceso cuenta para un día si inició ese día y aún no tiene ninguna
     * carga (para no perder de vista lo recién iniciado), o si tiene al menos
     * una carga hecha ese día (made_date), sin importar cuándo inició.
     */
    protected function dayActivityFilter(): \Closure
    {
        $date = $this->date;

        return function ($query) use ($date) {
            $query->where(function ($q) use ($date) {
                $q->whereDate('start_date', $date)->whereDoesntHave('charges');
            })->orWhereHas('charges', function ($q) use ($date) {
                $q->whereDate('made_date', $date);
            });
        };
    }

    public function loadAvailableDates(): void
    {
        if (!$this->leader_id) {
            $this->availableDates = [];
            return;
        }

        $leaderIds = (string) $this->leader_id === 'all'
            ? collect($this->leaders)->pluck('id')
            : collect([$this->leader_id]);

        $startDatesQuery = Proccess::query()
            ->whereIn('who_made', $leaderIds)
            ->whereDoesntHave('charges')
            ->selectRaw('DATE(start_date) as process_date');

        $chargeDatesQuery = Proccess::query()
            ->join('charges', 'charges.id_proccess', '=', 'proccess.id')
            ->whereIn('proccess.who_made', $leaderIds)
            ->selectRaw('DATE(charges.made_date) as process_date');

        $this->availableDates = $startDatesQuery
            ->unionAll($chargeDatesQuery)
            ->get()
            ->groupBy('process_date')
            ->map(fn ($rows, $date) => ['date' => $date, 'total' => $rows->count()])
            ->sortByDesc('date')
            ->take(30)
            ->values()
            ->toArray();

        $this->dispatch('dates-updated', dates: array_column($this->availableDates, 'date'));
    }

    public function selectDate(string $date): void
    {
        $this->date = $date;
        $this->loadData();
    }

    public function render(){
        $processesGrouped = [];
        $totalDeadtimeAll = 0.0;
        $processCountAll  = 0;
        $totalPiecesAll = 0.0;
        $totalDecimetersAll = 0.0;

        if ((string) $this->leader_id === 'all' && $this->date) {
            $leaderIds = collect($this->leaders)->pluck('id');

            $allProcesses = Proccess::query()
                ->with([
                    'tarimaNp.tarima.customer',
                    'tarimaNp.numberPart',
                    'line',
                    'charges' => fn ($q) => $q->whereDate('made_date', $this->date)->with('timeouts'),
                ])
                ->whereIn('who_made', $leaderIds)
                ->where($this->dayActivityFilter())
                ->orderBy('who_made')
                ->orderBy('id')
                ->get();

            $processCountAll = $allProcesses->count();

            $checklists = StartChecklist::whereIn('id_user', $leaderIds)
                ->whereDate('register_date', $this->date)
                ->get()
                ->keyBy('id_user');

            foreach (collect($this->leaders) as $leader) {
                $lps = $allProcesses->where('who_made', $leader->id)->values();
                if ($lps->isEmpty()) {
                    continue;
                }
                $ldt = 0.0;
                $lpieces = 0.0;
                $ldecimeters = 0.0;
                foreach ($lps as $p) {
                    $decimeters = $p->tarimaNp->numberPart->decimeters ?? 0;
                    foreach ($p->charges as $c) {
                        $pieces = (float) $c->quantity_pieces;
                        $lpieces += $pieces;
                        $ldecimeters += $pieces * $decimeters;
                        foreach ($c->timeouts as $t) {
                            $ldt += (float) $t->hours;
                        }
                    }
                }
                $totalDeadtimeAll += $ldt;
                $totalPiecesAll += $lpieces;
                $totalDecimetersAll += $ldecimeters;
                $processesGrouped[] = [
                    'leader_name' => $leader->name,
                    'processes'   => $lps,
                    'checklist'   => $checklists->get($leader->id),
                    'deadtime'    => $ldt,
                    'pieces'      => $lpieces,
                    'decimeters'  => $ldecimeters,
                ];
            }
        }

        return view('livewire.processes.report-processes', compact(
            'processesGrouped', 'totalDeadtimeAll', 'processCountAll', 'totalPiecesAll', 'totalDecimetersAll'
        ));
    }
}
