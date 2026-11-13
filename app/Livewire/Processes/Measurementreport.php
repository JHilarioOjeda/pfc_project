<?php

namespace App\Livewire\Processes;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Throwable;
use Illuminate\Support\Facades\Log;
use App\Models\Proccess;
use App\Models\MeditionsReport;
use App\Models\MeditionsreportObservation;
use App\Models\MeditionsReportFolio;
use Illuminate\Validation\Rule;

class Measurementreport extends Component
{
    public $idprocess, $process_selected;

    public $method, $requirement, $status, $notes = '*Se recomienda mantener el material en un lugar seco libre de humedad o líquidos (agua, soluciones, aceites, etc.) o solventes que emanen vapores.';
    public $visual_appearance, $thickness_in_microns;

    // Lista de mediciones registradas (cada elemento: id_observation, thickness_in_microns, visual_appearance)
    public $measurementsList = [];

    // Indica si ya existe un reporte de medición para este proceso
    public $reportExists = false;

    // Folio para imprimir el reporte individual. Igual que en la impresión por
    // tarima: solo se pide cuando este reporte nunca se ha impreso solo; si ya
    // existe un folio que contiene únicamente este reporte, se reutiliza.
    public $folio;
    public $requiresFolio = false;
    public $lastFolioUsed;
    
    public function mount($idprocess)
    {
        $this->idprocess = $idprocess;
        $this->process_selected = Proccess::find($idprocess);

        if ($this->process_selected) {
            $this->requirement = $this->calculateMicrons();

            $report = MeditionsReport::where('id_proccess', $this->process_selected->id)->first();

            if ($report) {
                $this->reportExists = true;
                $this->method = $report->method;
                // Las micras se calculan desde el número de parte; si no hay dato, se conserva lo guardado
                $this->requirement = $this->requirement ?? $report->requirement;
                $this->status = $report->status;
                $this->notes = $report->notes;

                $observations = MeditionsreportObservation::where('id_medition_report', $report->id)->get();
                foreach ($observations as $observation) {
                    $this->measurementsList[] = [
                        'id_observation' => $observation->id,
                        'thickness_in_microns' => $observation->thickness_in_microns,
                        'visual_appearance' => $observation->visual_appearance,
                    ];
                }

                $this->refreshFolioRequirement($report);
            }
        }
    }

    protected function refreshFolioRequirement(?MeditionsReport $report = null): void
    {
        $report = $report ?? $this->currentReport();

        $this->requiresFolio = $report !== null
            && MeditionsReportFolio::findExactForReports([$report->id]) === null;

        $this->lastFolioUsed = MeditionsReportFolio::query()
            ->orderByDesc('id')
            ->value('folio');
    }

    protected function currentReport(): ?MeditionsReport
    {
        return $this->process_selected
            ? MeditionsReport::where('id_proccess', $this->process_selected->id)->first()
            : null;
    }

    // Abre el documento del reporte individual. Si el reporte nunca se ha
    // impreso solo, exige un folio nuevo y lo registra antes de imprimir.
    public function printReport()
    {
        $report = $this->currentReport();

        if (!$report) {
            LivewireAlert::title('No existe un reporte de medición para este proceso.')
                ->warning()
                ->show();
            return;
        }

        $existingFolio = MeditionsReportFolio::findExactForReports([$report->id]);

        if ($existingFolio) {
            // Reimpresión: no se pide folio ni se toca la base de datos.
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
                'folio.required' => 'Este reporte nunca se ha impreso. Ingresa un folio nuevo para poder imprimir.',
                'folio.unique' => 'Ese folio ya fue utilizado. Ingresa uno diferente.',
            ], [
                'folio' => 'Folio',
            ]);

            $newFolio = DB::transaction(function () use ($report) {
                $folio = MeditionsReportFolio::create(['folio' => $this->folio]);
                $folio->reports()->attach([$report->id]);

                return $folio;
            });

            $folioValue = $newFolio->folio;
            $this->folio = null;
            $this->requiresFolio = false;
            $this->lastFolioUsed = $folioValue;
        }

        return redirect()->route('measurementreports.document', [
            'proccess' => $this->process_selected->id,
            'folio' => $folioValue,
        ]);
    }

    // Micras tomadas automáticamente del número de parte del proceso
    protected function calculateMicrons()
    {
        $microns = optional(optional($this->process_selected->tarimaNp)->numberPart)->microns;

        if ($microns === null || $microns === '') {
            return null;
        }

        return rtrim(rtrim(number_format((float) $microns, 6, '.', ''), '0'), '.');
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
            'notes' => 'nullable|string',
        ], [], [
            'method' => 'Método',
            'requirement' => 'Requisito',
            'notes' => 'Notas',
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
                'notes' => $this->notes,
                'register_date' => now(),
            ]
        );

        if ($report->wasRecentlyCreated) {
            $this->reportExists = true;

            // Proceso terminado antes de capturar su reporte: la tarima puede quedar lista para entrega
            if ($this->process_selected->status === 'finished') {
                optional(optional($this->process_selected->tarimaNp)->tarima)->refreshDeliveryStatus();
            }
        } else {
            $report->method = $this->method;
            $report->requirement = $this->requirement;
            $report->notes = $this->notes;

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
            $this->validateReportFields();

            // Aseguramos que exista el registro principal del reporte
            $report = $this->getOrCreateReport();

            $observation = MeditionsreportObservation::create([
                'id_medition_report' => $report->id,
                'thickness_in_microns' => $this->thickness_in_microns,
                'visual_appearance' => $this->getVisualAppearanceLabel($this->visual_appearance),
            ]);

            $this->measurementsList[] = [
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
        if (!isset($this->measurementsList[$index])) {
            return;
        }

        $item = $this->measurementsList[$index];

        if (!empty($item['id_observation'])) {
            $observation = MeditionsreportObservation::find($item['id_observation']);
            if ($observation) {
                $observation->delete();
            }
        }

        unset($this->measurementsList[$index]);
        $this->measurementsList = array_values($this->measurementsList);
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
        if (count($this->measurementsList) === 0) {
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

            optional(optional($this->process_selected->tarimaNp)->tarima)->refreshDeliveryStatus();

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

    public function finishReport()
    {
        $this->validateReportFields();

        DB::beginTransaction();

        try {
            $report = $this->getOrCreateReport();

            $report->status = 'finished';
            $report->save();

            DB::commit();

            LivewireAlert::title('Reporte terminado correctamente.')
                ->success()
                ->show();
        
            $this->status = 'finished';
            $this->refreshFolioRequirement($report);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al terminar el reporte de medición: ' . $e->getMessage());

            LivewireAlert::title('Error al terminar el reporte.')
                ->error()
                ->show();
        }
    }   
}
