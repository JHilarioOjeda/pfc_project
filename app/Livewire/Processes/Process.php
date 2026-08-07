<?php

namespace App\Livewire\Processes;

use Livewire\Component;
use DB;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Auth;
use Throwable;
use Log;
use App\Models\Proccess;
use App\Models\WorkLine;
use App\Models\Timeout;
use App\Models\Charge;

class Process extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $idprocess, $process_selected;
    public $lines = [], $id_line;
    public $operator_name;

    // Cargas
    public $chargesList = [];
    public $new_charge_quantity;

    // Tiempos muertos por carga (indexados por charge index)
    public $deadtime = [], $deadtime_hours = [];
    // Ã­ndice de carga activo para tiempos muertos (null = ninguno expandido)
    public $deadtimeOpenCharge = null;

    public function mount($idprocess)
    {
        $this->idprocess = $idprocess;
        $this->process_selected = Proccess::with(['tarimaNp.numberPart', 'tarimaNp.tarima.customer'])->find($this->idprocess);
        $this->lines = WorkLine::all();

        if ($this->process_selected) {
            $this->id_line = $this->process_selected->id_line;
            $this->operator_name = $this->process_selected->operator_name;

            if (blank($this->operator_name)) {
                $defaultOperatorName = $this->resolveDefaultOperatorNameByTarima();
                if (filled($defaultOperatorName)) {
                    $this->operator_name = $defaultOperatorName;
                    $this->process_selected->operator_name = $defaultOperatorName;
                }
            }

            $this->loadCharges();
        }

        if ($this->process_selected && $this->process_selected->start_date == null) {
            $this->process_selected->start_date = now();
            $this->process_selected->who_made = Auth::user()->id;
            $this->process_selected->status = 'inprocess';
            $this->process_selected->save();
        }
    }

    protected function resolveDefaultOperatorNameByTarima(): ?string
    {
        if (!$this->process_selected || !$this->process_selected->tarimaNp) {
            return null;
        }

        $tarimaId = $this->process_selected->tarimaNp->id_tarima;

        if (!$tarimaId) {
            return null;
        }

        $sourceProcess = Proccess::query()
            ->whereHas('tarimaNp', function ($query) use ($tarimaId) {
                $query->where('id_tarima', $tarimaId);
            })
            ->whereNotNull('start_date')
            ->whereNotNull('operator_name')
            ->whereRaw("TRIM(operator_name) <> ''")
            ->orderBy('start_date', 'asc')
            ->orderBy('id', 'asc')
            ->first();

        return $sourceProcess?->operator_name;
    }

    protected function loadCharges(): void
    {
        $this->chargesList = [];
        $charges = Charge::with('timeouts')
            ->where('id_proccess', $this->process_selected->id)
            ->orderBy('id')
            ->get();

        foreach ($charges as $charge) {
            $timeouts = [];
            foreach ($charge->timeouts as $t) {
                $timeouts[] = [
                    'id_timeout' => $t->id,
                    'type'       => $t->type,
                    'hours'      => $t->hours,
                ];
            }
            $this->chargesList[] = [
                'id_charge'        => $charge->id,
                'quantity_pieces'  => $charge->quantity_pieces,
                'status'           => $charge->status,
                'timeouts'         => $timeouts ?? [],
            ];
        }
    }

    public function render()
    {
        $totalPiezasCargas = collect($this->chargesList)->sum('quantity_pieces');
        $piezasTotales     = (float) optional(optional($this->process_selected)->tarimaNp)->quantity;
        $piezasRestantes   = max(0, $piezasTotales - $totalPiezasCargas);

        return view('livewire.processes.process', compact('piezasRestantes', 'piezasTotales'));
    }

    // Proceso general
    public function updateProcessData()
    {
        $this->validate([
            'id_line' => 'required|exists:work_lines,id',
        ], [], [
            'id_line' => 'Línea',
        ]);

        if (!$this->process_selected) return;

        $this->process_selected->id_line = $this->id_line;
        $this->process_selected->operator_name = $this->operator_name;
        $this->process_selected->save();

        LivewireAlert::title('Datos del proceso actualizados correctamente.')
            ->success()
            ->show();
    }

    public function finishProcess()
    {
        if (!$this->process_selected) return;

        $totalPiezasCargas = collect($this->chargesList)->sum('quantity_pieces');
        $piezasTotales     = (float) optional(optional($this->process_selected)->tarimaNp)->quantity;

        if ($totalPiezasCargas < $piezasTotales) {
            LivewireAlert::title('Aún quedan piezas restantes por asignar a cargas.')
                ->warning()
                ->show();
            return;
        }

        $pendientes = collect($this->chargesList)->filter(fn($c) => $c['status'] !== 'confirmed')->count();
        if ($pendientes > 0) {
            LivewireAlert::title('Todas las cargas deben estar confirmadas antes de terminar el proceso.')
                ->warning()
                ->show();
            return;
        }

        DB::beginTransaction();
        try {
            $this->process_selected->id_line       = $this->id_line;
            $this->process_selected->operator_name = $this->operator_name;
            $this->process_selected->pieces_alreadyproccess = (int) $totalPiezasCargas;
            $this->process_selected->status        = 'finished';
            $this->process_selected->finished_date = now();
            $this->process_selected->save();

            DB::commit();

            LivewireAlert::title('Proceso terminado correctamente.')
                ->success()
                ->show();

            return redirect()->route('processes');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al terminar el proceso: ' . $e->getMessage());
            LivewireAlert::title('Error al terminar el proceso.')
                ->error()
                ->show();
        }
    }

    // Cargas
    public function addCharge()
    {
        $this->validate([
            'new_charge_quantity' => 'required|numeric|min:1',
        ], [], [
            'new_charge_quantity' => 'Cantidad de piezas',
        ]);

        $totalPiezasCargas = collect($this->chargesList)->sum('quantity_pieces');
        $piezasTotales     = (float) optional(optional($this->process_selected)->tarimaNp)->quantity;
        $restantes         = $piezasTotales - $totalPiezasCargas;

        if ((float) $this->new_charge_quantity > $restantes) {
            $this->addError('new_charge_quantity', "No pueden asignarse más piezas de las restantes ({$restantes}).");
            return;
        }

        $charge = Charge::create([
            'id_proccess'     => $this->process_selected->id,
            'quantity_pieces' => $this->new_charge_quantity,
            'status'          => 'created',
            'who_made'        => Auth::user()->id,
            'made_date'       => now(),
        ]);

        $this->chargesList[] = [
            'id_charge'       => $charge->id,
            'quantity_pieces' => $charge->quantity_pieces,
            'status'          => $charge->status,
            'timeouts'        => [],
        ];
        $this->reset('new_charge_quantity');
    }

    public function liberateCharge($index)
    {
        $item = $this->chargesList[$index] ?? null;
        if (!$item || $item['status'] !== 'created') return;

        Charge::where('id', $item['id_charge'])->update([
            'status'    => 'liberated',
            'who_free'  => Auth::user()->id,
            'free_date' => now(),
        ]);

        $this->chargesList[$index]['status'] = 'liberated';
    }

    public function confirmCharge($index)
    {
        $item = $this->chargesList[$index] ?? null;
        if (!$item || $item['status'] !== 'liberated') return;

        Charge::where('id', $item['id_charge'])->update([
            'status'       => 'confirmed',
            'who_confirms' => Auth::user()->id,
            'confirm_date' => now(),
        ]);

        $this->chargesList[$index]['status'] = 'confirmed';
    }

    public function deleteCharge($index)
    {
        $item = $this->chargesList[$index] ?? null;
        if (!$item) return;

        Charge::find($item['id_charge'])?->delete();

        unset($this->chargesList[$index]);
        $this->chargesList = array_values($this->chargesList);

        if ($this->deadtimeOpenCharge === $index) {
            $this->deadtimeOpenCharge = null;
        }
    }

    public function toggleDeadtimePanel($index)
    {
        $this->deadtimeOpenCharge = ($this->deadtimeOpenCharge === $index) ? null : $index;
    }

    protected function getDeadtimeOptions(): array
    {
        return [
            '1'  => 'Secado deficiente',
            '2'  => 'Mesa llena de piezas',
            '3'  => 'Falta de material',
            '4'  => 'Falta de personal',
            '5'  => 'Reproceso de piezas',
            '6'  => 'Tiempo excesivo desengrase',
            '7'  => 'Ajuste de soluciones',
            '8'  => 'Cambio de soluciones',
            '9'  => 'Evento social',
            '10' => 'Falla de equipo',
            '11' => 'Falta de energía eléctrica',
            '12' => 'Llenado de tinas',
            '13' => 'Limpieza de piezas',
            '14' => 'Cambio de tina y/o ganchos',
            '15' => 'Desprendimiento',
            '16' => 'Bajo micraje',
            '17' => 'Cobre expuesto',
            '18' => 'Mancha',
        ];
    }

    protected function getDeadtimeLabel($value): string
    {
        return $this->getDeadtimeOptions()[(string) $value] ?? (string) $value;
    }

    public function addDeadtime($chargeIndex)
    {
        $this->validate([
            "deadtime.{$chargeIndex}"       => 'required',
            "deadtime_hours.{$chargeIndex}" => 'required|numeric|min:0.01',
        ], [], [
            "deadtime.{$chargeIndex}"       => 'Razón de tiempo muerto',
            "deadtime_hours.{$chargeIndex}" => 'Tiempo en horas',
        ]);

        $item = $this->chargesList[$chargeIndex] ?? null;
        if (!$item) return;

        $label = $this->getDeadtimeLabel($this->deadtime[$chargeIndex]);

        $timeout = Timeout::create([
            'id_charge' => $item['id_charge'],
            'type'      => $label,
            'hours'     => $this->deadtime_hours[$chargeIndex],
        ]);

        $this->chargesList[$chargeIndex]['timeouts'][] = [
            'id_timeout' => $timeout->id,
            'type'       => $timeout->type,
            'hours'      => $timeout->hours,
        ];

        $this->deadtime[$chargeIndex]       = null;
        $this->deadtime_hours[$chargeIndex] = null;
    }

    public function removeDeadtime($chargeIndex, $timeoutIndex)
    {
        $item = $this->chargesList[$chargeIndex]['timeouts'][$timeoutIndex] ?? null;
        if (!$item) return;

        if (!empty($item['id_timeout'])) {
            Timeout::find($item['id_timeout'])?->delete();
        }

        unset($this->chargesList[$chargeIndex]['timeouts'][$timeoutIndex]);
        $this->chargesList[$chargeIndex]['timeouts'] = array_values($this->chargesList[$chargeIndex]['timeouts']);
    }
}
