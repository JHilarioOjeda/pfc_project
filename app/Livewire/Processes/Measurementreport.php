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
use App\Models\MeditionsReport;
use App\Models\MeditionsreportObservation;

class Measurementreport extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $idprocess, $process_selected;

    public $method, $requirement;
    public $visual_appearance, $thickness_in_microns;

    // Lista de mediciones registradas (cada elemento: id_observation, thickness_in_microns, visual_appearance)
    public $deadTimesList = [];

    // Se usa únicamente para la condición del botón "Terminar proceso" en la vista
    public $quantity_processed;

    // Indica si ya existe un reporte de medición para este proceso
    public $reportExists = false;

    public function mount($idprocess)
    {
        $this->idprocess = $idprocess;
        $this->process_selected = Proccess::find($idprocess);

        if ($this->process_selected) {
            $this->quantity_processed = $this->process_selected->pieces_alreadyproccess;

            $report = MeditionsReport::where('id_proccess', $this->process_selected->id)->first();

            if ($report) {
                $this->reportExists = true;
                $this->method = $report->method;
                $this->requirement = $report->requirement;

                $observations = MeditionsreportObservation::where('id_medition_report', $report->id)->get();
                foreach ($observations as $observation) {
                    $this->deadTimesList[] = [
                        'id_observation' => $observation->id,
                        'thickness_in_microns' => $observation->thickness_in_microns,
                        'visual_appearance' => $observation->visual_appearance,
                    ];
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.processes.measurementreport');
    }

    protected function validateReportFields(): void
    {
        $this->validate([
            'method' => 'required|string|max:255',
            'requirement' => 'nullable|string',
        ], [], [
            'method' => 'Método',
            'requirement' => 'Requisito',
        ]);
    }

    protected function getVisualAppearanceOptions(): array
    {
        return [
            '1' => 'Aceptable',
            '2' => 'Denegado',
        ];
    }

    protected function getVisualAppearanceLabel($value): string
    {
        $options = $this->getVisualAppearanceOptions();
        return $options[(string) $value] ?? (string) $value;
    }

    protected function getOrCreateReport(): ?MeditionsReport
    {
        if (!$this->process_selected) {
            return null;
        }

        $report = MeditionsReport::firstOrCreate(
            ['id_proccess' => $this->process_selected->id],
            [
                'method' => $this->method,
                'requirement' => $this->requirement,
                'register_date' => now(),
            ]
        );

        if ($report->wasRecentlyCreated) {
            $this->reportExists = true;
        } else {
            $report->method = $this->method;
            $report->requirement = $this->requirement;

            if (!$report->register_date) {
                $report->register_date = now();
            }

            $report->save();

            $this->reportExists = true;
        }

        return $report;
    }

    public function saveReport()
    {
        if (!$this->process_selected) {
            return;
        }

        $this->validateReportFields();

        DB::beginTransaction();

        try {
            $this->getOrCreateReport();

            DB::commit();

            LivewireAlert::title('Reporte de medición guardado correctamente.')
                ->success()
                ->show();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al guardar el reporte de medición: ' . $e->getMessage());

            LivewireAlert::title('Error al guardar el reporte de medición.')
                ->error()
                ->show();
        }
    }

    public function addMeasurement()
    {
        $this->validate([
            'visual_appearance' => 'required',
            'thickness_in_microns' => 'required|numeric|min:0',
        ], [], [
            'visual_appearance' => 'Apariencia visual',
            'thickness_in_microns' => 'Espesor en micras',
        ]);

        if (!$this->process_selected) {
            return;
        }

        DB::beginTransaction();

        try {
            // Aseguramos que exista el registro principal del reporte
            $report = $this->getOrCreateReport();

            $observation = MeditionsreportObservation::create([
                'id_medition_report' => $report->id,
                'thickness_in_microns' => $this->thickness_in_microns,
                'visual_appearance' => $this->getVisualAppearanceLabel($this->visual_appearance),
            ]);

            $this->deadTimesList[] = [
                'id_observation' => $observation->id,
                'thickness_in_microns' => $observation->thickness_in_microns,
                'visual_appearance' => $observation->visual_appearance,
            ];

            $this->reset(['visual_appearance', 'thickness_in_microns']);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al agregar medición al reporte: ' . $e->getMessage());

            LivewireAlert::title('Error al agregar la medición.')
                ->error()
                ->show();
        }
    }

    public function removeMeasurement($index)
    {
        if (!isset($this->deadTimesList[$index])) {
            return;
        }

        $item = $this->deadTimesList[$index];

        if (!empty($item['id_observation'])) {
            $observation = MeditionsreportObservation::find($item['id_observation']);
            if ($observation) {
                $observation->delete();
            }
        }

        unset($this->deadTimesList[$index]);
        $this->deadTimesList = array_values($this->deadTimesList);
    }

    public function finishProcessWithReport()
    {
        if (!$this->process_selected) {
            return;
        }

        $maxPieces = optional(optional($this->process_selected)->tarimaNp)->quantity;

        if ($maxPieces !== null && (float) $this->process_selected->pieces_alreadyproccess !== (float) $maxPieces) {
            LivewireAlert::title('La cantidad de piezas procesadas debe ser igual a la cantidad a procesar para terminar el proceso.')
                ->warning()
                ->show();
            return;
        }

        // Opcional: exigir al menos una medición antes de terminar el proceso
        if (count($this->deadTimesList) === 0) {
            LivewireAlert::title('Debes registrar al menos una medición antes de terminar el proceso.')
                ->warning()
                ->show();
            return;
        }

        DB::beginTransaction();

        try {
            // Nos aseguramos de que el reporte esté guardado
            $this->validateReportFields();
            $this->getOrCreateReport();

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
            Log::error('Error al terminar el proceso desde reporte de medición: ' . $e->getMessage());

            LivewireAlert::title('Error al terminar el proceso.')
                ->error()
                ->show();
        }
    }
}
