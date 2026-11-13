<?php

namespace App\Http\Controllers;

use App\Models\Tarima;
use App\Models\MeditionsReport;
use App\Models\MeditionsReportFolio;
use App\Models\Proccess;
use App\Models\StartChecklist;
use App\Models\User;
use App\Support\MeasurementReportFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class Controller
{
    // Controlador base de la aplicación
}

class PFController extends Controller
{
    public function tarimaDocument($tarima)
    {
        $tarima = Tarima::with(['customer', 'tarimaNps.numberPart'])->findOrFail($tarima);

        return view('formats.tarima-format', compact('tarima'));
    }

    public function measurementReportDocument(Request $request, $proccess)
    {
        $report = MeditionsReport::with([
            'proccess.tarimaNp.tarima.customer',
            'proccess.tarimaNp.numberPart',
            'observations',
            'folios',
        ])->where('id_proccess', $proccess)->firstOrFail();

        // El formato (1, 2 o 3) depende del cliente; ver Customer::measurement_format.
        $customer = optional(optional(optional($report->proccess)->tarimaNp)->tarima)->customer;

        return view(MeasurementReportFormat::viewFor($customer), [
            'reports' => collect([$report]),
            'customer' => $customer,
            // Folio de la impresión individual: el que viene en la URL, o el folio
            // que contiene solo este reporte. Como respaldo (enlaces viejos), el
            // folio más reciente de cualquier impresión en la que haya salido.
            'folio' => $request->input('folio')
                ?: optional(MeditionsReportFolio::findExactForReports([$report->id]))->folio
                ?: optional($report->latestFolio())->folio,
            'backUrl' => route('measurementreports', optional($report->proccess)->id),
            'pdfName' => "reporte-mediciones-{$report->id}.pdf",
            'pdfType' => 'reporte-mediciones',
        ]);
    }

    public function reportTarimasPrint(Request $request)
    {
        $tarimaId = $request->input('tarima');
        $reportIds = array_filter(explode(',', $request->input('reports', '')));

        $tarima = Tarima::with('customer')->findOrFail($tarimaId);
        $reports = MeditionsReport::with(['proccess.tarimaNp.numberPart', 'observations', 'folios'])
            ->whereIn('id', $reportIds)
            ->get();

        $folio = $request->input('folio');

        if (!$folio) {
            // Enlace directo/antiguo sin folio explícito en la URL: se cae
            // de vuelta al folio más reciente del primer reporte, si tiene.
            $folio = optional($reports->first())->latestFolio()?->folio;
        }

        // El formato (1, 2 o 3) depende del cliente de la tarima.
        return view(MeasurementReportFormat::viewFor($tarima->customer), [
            'reports' => $reports,
            'customer' => $tarima->customer,
            'folio' => $folio,
            'backUrl' => route('reporttarimas.detail', $tarima->id),
            'pdfName' => "reporte-mediciones-tarima-{$tarima->id}.pdf",
            'pdfType' => 'reporte-mediciones-tarimas',
        ]);
    }

    public function processReportPrint(Request $request)
    {
        $date = $request->date;
        $leaderId = $request->leader;

        if (!$date || !$leaderId) {
            return redirect()->route('reportprocesses');
        }

        $leader = User::findOrFail((int) $leaderId);
        $checklist = StartChecklist::query()
            ->where('id_user', $leaderId)
            ->whereDate('register_date', $date)
            ->first();

        $processes = Proccess::query()
            ->with([
                'tarimaNp.tarima.customer',
                'tarimaNp.numberPart',
                'whomade',
                'line',
                'charges' => fn ($q) => $q->whereDate('made_date', $date)->with('timeouts'),
            ])
            ->where('who_made', $leaderId)
            ->where(function ($query) use ($date) {
                $query->where(function ($q) use ($date) {
                    $q->whereDate('start_date', $date)->whereDoesntHave('charges');
                })->orWhereHas('charges', function ($q) use ($date) {
                    $q->whereDate('made_date', $date);
                });
            })
            ->orderBy('id')
            ->get();

        $deadtimeByType = [];
        $totalDeadtime = 0.0;
        $totalPieces = 0.0;
        $totalDecimeters = 0.0;
        foreach ($processes as $process) {
            $decimeters = $process->tarimaNp->numberPart->decimeters ?? 0;
            $pieces = (float) $process->charges->sum('quantity_pieces');
            $totalPieces += $pieces;
            $totalDecimeters += $pieces * $decimeters;

            foreach ($process->charges->flatMap->timeouts as $timeout) {
                $label = (string) $timeout->type;
                $hours = (float) $timeout->hours;

                if (!isset($deadtimeByType[$label])) {
                    $deadtimeByType[$label] = 0.0;
                }

                $deadtimeByType[$label] += $hours;
                $totalDeadtime += $hours;
            }
        }

        return view('formats.processreport-format', compact(
            'date',
            'leader',
            'checklist',
            'processes',
            'deadtimeByType',
            'totalDeadtime',
            'totalPieces',
            'totalDecimeters'
        ));
    }

    public function storePdf(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'string'],
            'file' => ['required', 'file', 'mimetypes:application/pdf,application/x-pdf,application/acrobat,applications/vnd.pdf,text/pdf'],
            'filename' => ['nullable', 'string'],
        ]);

        $type = preg_replace('/[^a-zA-Z0-9_-]/', '_', $validated['type']);
        $dateFolder = now()->format('Y-m-d');
        $baseFolder = "reports/{$type}/{$dateFolder}";

        $originalName = $validated['filename'] ?? $request->file('file')->getClientOriginalName();
        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName = Str::slug($nameWithoutExt) ?: 'reporte';
        $finalName = $safeName . '.pdf';

        $path = $request->file('file')->storeAs($baseFolder, $finalName);

        return response()->json([
            'success' => true,
            'path' => $path,
        ]);
    }
}
