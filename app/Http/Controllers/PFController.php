<?php

namespace App\Http\Controllers;

use App\Models\Tarima;
use App\Models\MeditionsReport;
use App\Models\Proccess;
use App\Models\StartChecklist;
use App\Models\User;
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

    public function measurementReportDocument($proccess)
    {
        $report = MeditionsReport::with([
            'proccess.tarimaNp.tarima.customer',
            'proccess.tarimaNp.numberPart',
            'observations',
            'folios',
        ])->where('id_proccess', $proccess)->firstOrFail();

        return view('formats.measurementreport-format', compact('report'));
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

        return view('formats.measurementreporttarimas-format', compact('tarima', 'reports', 'folio'));
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
            ->with(['tarimaNp.tarima.customer', 'tarimaNp.numberPart', 'whomade', 'timeouts', 'line', 'charges'])
            ->where('who_made', $leaderId)
            ->whereDate('start_date', $date)
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

            foreach ($process->timeouts as $timeout) {
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
