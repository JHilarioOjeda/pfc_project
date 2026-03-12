<?php

use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',])->group(function () {

    Route::get('/', function () {
        return view('welcome');
    });
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/myprofile', function () {
        return view('users.myprofile');
    })->name('myprofile');

    //CATALOGOS
        Route::get('/admin/users', function () {
            return view('users.users');
        });
        Route::get('/admin/customers', function () {
            return view('catalogs.customers');
        });
        Route::get('/admin/np', function () {
            return view('catalogs.nps');
        });
    //

    //ALMACEN
        Route::get('/storage', function () {
            return view('storage.storage');
        })->name('storage');

        Route::get('/storage/{id}', function ($id) {
            return view('storage.tarima', compact('id'));
        })->name('storage.tarima');

        Route::get('/storage/{tarima}/document', function ($tarima) {
            $tarima = \App\Models\Tarima::with(['customer', 'tarimaNps.numberPart'])->findOrFail($tarima);

            return view('formats.tarima-format', compact('tarima'));
        })->name('storage.tarima.document');
    //

    // PROCESOS
        Route::get('/processes', function () {
            return view('processes.processes');
        })->name('processes');

        Route::get('/process/{id}', function ($id) {
            return view('processes.process', compact('id'));
        })->name('processes.process');
    //

    // REPORTES
        Route::get('/reportmeasurement/{id}', function ($id) {
            return view('processes.measurementreports', compact('id'));
        })->name('measurementreports');

        Route::get('/reportmeasurement/{proccess}/document', function ($proccess) {
            $report = \App\Models\MeditionsReport::with([
                'proccess.tarimaNp.tarima.customer',
                'proccess.tarimaNp.numberPart',
                'observations',
            ])->where('id_proccess', $proccess)->firstOrFail();

            return view('formats.measurementreport-format', compact('report'));
        })->name('measurementreports.document');

        Route::get('/reporttarimas', function () {
            return view('processes.measurereporttarimas');
        })->name('reporttarimas');

        Route::get('/reporttarimas/print', function () {
            $tarimaId = request('tarima');
            $reportIds = array_filter(explode(',', request('reports', '')));

            $tarima = \App\Models\Tarima::with('customer')->findOrFail($tarimaId);
            $reports = \App\Models\MeditionsReport::with(['proccess.tarimaNp.numberPart', 'observations'])
                ->whereIn('id', $reportIds)
                ->get();

            return view('formats.measurementreporttarimas-format', compact('tarima', 'reports'));
        })->name('reporttarimas.print');

        Route::get('/reporttarimas/{id}', function ($id) {
            return view('processes.measurereporttarimadetail', compact('id'));
        })->name('reporttarimas.detail');
    //
});
