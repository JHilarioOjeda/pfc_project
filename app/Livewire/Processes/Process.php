<?php

namespace App\Livewire\Processes;

use Livewire\Component;
use DB;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage as StorageDisk;
use Livewire\WithFileUploads;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use RealRashid\SweetAlert\Facades\Alert;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Auth;
use Throwable;
use Log;
use App\Models\Tarima as TarimaModel;
use App\Models\Customer;
use App\Models\NumberPart;
use App\Models\TarimaNp;
use App\Models\User;
use App\Models\Proccess;
use App\Models\WorkLine;
use App\Models\Timeout;

class Process extends Component
{
    use WithFileUploads;
    use WithPagination;

    public  $idprocess, $process_selected;
    public $lines = [], $id_line;
    public $operator_name, $quantity_processed;

    public $deadtime, $deadtime_hours;
    public $deadTimesList = [];

    public function mount($idprocess){
        $this->process_selected = Proccess::find($this->idprocess);
        $this->lines = WorkLine::all();

        // Cargar valores iniciales del proceso
        if ($this->process_selected) {
            $this->id_line = $this->process_selected->id_line;
            $this->operator_name = $this->process_selected->operator_name;
            $this->quantity_processed = $this->process_selected->pieces_alreadyproccess;

            // Cargar tiempos muertos existentes desde BD
            $existingTimeouts = Timeout::where('id_proccess', $this->process_selected->id)->get();
            foreach ($existingTimeouts as $timeout) {
                $this->deadTimesList[] = [
                    'id_timeout' => $timeout->id,
                    'type' => $timeout->type,
                    'hours' => $timeout->hours,
                ];
            }
        }

        if($this->process_selected->start_date == null){
            $this->process_selected->start_date = now();
            $this->process_selected->who_made = Auth::user()->id;
            $this->process_selected->status = 'inprocess';
            $this->process_selected->save();
        }
    }

    public function render(){

        return view('livewire.processes.process');
    }

    protected function validateProcessFields(): void
    {
        if (!$this->process_selected) {
            return;
        }

        $maxPieces = optional(optional($this->process_selected)->tarimaNp)->quantity;

        $rules = [
            'id_line' => 'required|exists:work_lines,id',
            'quantity_processed' => 'required|numeric|min:0',
        ];

        if ($maxPieces !== null) {
            $rules['quantity_processed'] .= '|max:' . $maxPieces;
        }

        $this->validate($rules, [], [
            'id_line' => 'Línea',
            'operator_name' => 'Nombre(s) de operador(es)',
            'quantity_processed' => 'Cantidad de piezas procesadas',
        ]);
    }

    protected function persistProcessBasicData(): void
    {
        if (!$this->process_selected) {
            return;
        }

        $this->process_selected->id_line = $this->id_line;
        $this->process_selected->operator_name = $this->operator_name;
        $this->process_selected->pieces_alreadyproccess = $this->quantity_processed;
        $this->process_selected->save();
    }

    public function updateProcessData()
    {
        $this->validateProcessFields();

        $this->persistProcessBasicData();

        LivewireAlert::title('Datos del proceso actualizados correctamente.')
            ->success()
            ->show();
    }

    public function finishProcess()
    {
        if (!$this->process_selected) {
            return;
        }

        $this->validateProcessFields();

        $maxPieces = optional(optional($this->process_selected)->tarimaNp)->quantity;

        if ($maxPieces !== null && (float) $this->quantity_processed !== (float) $maxPieces) {
            LivewireAlert::title('La cantidad de piezas procesadas debe ser igual a la cantidad a procesar para terminar el proceso.')
                ->warning()
                ->show();
            return;
        }

        DB::beginTransaction();

        try {
            $this->persistProcessBasicData();

            $this->process_selected->status = 'finished';
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

    protected function getDeadtimeOptions(): array
    {
        return [
            '1' => 'Secado deficiente',
            '2' => 'Mesa llena de piezas',
            '3' => 'Falta de material',
            '4' => 'Falta de personal',
            '5' => 'Reproceso de piezas',
            '6' => 'Tiempo excesivo desengrase',
            '7' => 'Ajuste de soluciones',
            '8' => 'Cambio de soluciones',
            '9' => 'Evento social',
            '10' => 'Falla de equipo',
            '11' => 'Falta de energía eléctrica',
            '12' => 'Llenado de tinas',
            '13' => 'Limpieza de piezas',
            '14' => 'Cambio de tina y/o ganchos',
        ];
    }

    protected function getDeadtimeLabel($value): string
    {
        $options = $this->getDeadtimeOptions();
        return $options[(string) $value] ?? (string) $value;
    }

    public function addDeadtime()
    {
        $this->validate([
            'deadtime' => 'required',
            'deadtime_hours' => 'required|numeric|min:0.01',
        ], [], [
            'deadtime' => 'Razón de tiempo muerto',
            'deadtime_hours' => 'Tiempo en horas',
        ]);

        if (!$this->process_selected) {
            return;
        }

        $label = $this->getDeadtimeLabel($this->deadtime);

        // Guardar inmediatamente en BD
        $timeout = Timeout::create([
            'id_proccess' => $this->process_selected->id,
            'type' => $label,
            'hours' => $this->deadtime_hours,
        ]);

        $this->deadTimesList[] = [
            'id_timeout' => $timeout->id,
            'type' => $timeout->type,
            'hours' => $timeout->hours,
        ];

        $this->reset(['deadtime', 'deadtime_hours']);
    }

    public function removeDeadtime($index)
    {
        if (!isset($this->deadTimesList[$index])) {
            return;
        }

        $item = $this->deadTimesList[$index];

        if (!empty($item['id_timeout'])) {
            $timeout = Timeout::find($item['id_timeout']);
            if ($timeout) {
                $timeout->delete();
            }
        }

        unset($this->deadTimesList[$index]);
        $this->deadTimesList = array_values($this->deadTimesList);
    }
}
