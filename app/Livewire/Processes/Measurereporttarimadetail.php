<?php

namespace App\Livewire\Processes;

use Livewire\Component;
use App\Models\Tarima;
use App\Models\MeditionsReport;
use App\Models\MeditionsreportObservation;
use App\Models\Proccess;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class Measurereporttarimadetail extends Component
{
    public $idtarima;
    public $tarima;
    public $reports = [];
    public $selectedReports = [];
    public $folio;

    public function mount($id)
    {
        $this->idtarima = $id;

        $this->tarima = Tarima::with('customer')->findOrFail($id);

        $this->loadReports();
    }

    protected function loadReports(): void
    {
        $reports = MeditionsReport::query()
            ->select('meditions_report.*')
            ->join('proccess', 'proccess.id', '=', 'meditions_report.id_proccess')
            ->join('tarima_nps', 'tarima_nps.id', '=', 'proccess.id_tarima_np')
            ->where('tarima_nps.id_tarima', $this->idtarima)
            ->with(['observations', 'proccess.tarimaNp.numberPart'])
            ->orderBy('meditions_report.register_date', 'desc')
            ->get();

        $this->reports = $reports->map(function ($report) {
            return [
                'id' => $report->id,
                'folio' => $report->folio,
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

    public function printSelected()
    {
        if (empty($this->selectedReports)) {
            LivewireAlert::title('Selecciona al menos un reporte para imprimir.')
                ->warning()
                ->show();
            return;
        }

        $this->validate([
            'folio' => 'required|string|max:255',
        ], [], [
            'folio' => 'Folio',
        ]);

        MeditionsReport::whereIn('id', $this->selectedReports)->update([
            'folio' => $this->folio,
        ]);

        $ids = implode(',', $this->selectedReports);

        return redirect()->route('reporttarimas.print', [
            'tarima' => $this->idtarima,
            'reports' => $ids,
        ]);
    }

    public function render()
    {
        return view('livewire.processes.measurereporttarimadetail');
    }
}
