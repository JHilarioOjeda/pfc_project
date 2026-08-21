<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PFController;


Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified','user.active'])->group(function () {

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
        Route::get('/storages', function () {
            return view('storage.storage');
        })->name('storage');

        Route::get('/storage/{id}', function ($id) {
            return view('storage.tarima', compact('id'));
        })->name('storage.tarima');

        Route::get('/storage/{tarima}/document', [PFController::class, 'tarimaDocument'])
            ->name('storage.tarima.document');
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

        Route::get('/reportmeasurement/{proccess}/document', [PFController::class, 'measurementReportDocument'])
            ->name('measurementreports.document');

        Route::get('/reporttarimas', function () {
            return view('processes.measurereporttarimas');
        })->name('reporttarimas');

        Route::get('/reporttarimas/print', [PFController::class, 'reportTarimasPrint'])
            ->name('reporttarimas.print');

        Route::get('/reporttarimas/{id}', function ($id) {
            return view('processes.measurereporttarimadetail', compact('id'));
        })->name('reporttarimas.detail');

        Route::get('/reportprocesses', function () {
            return view('processes.reportprocesses');
        })->name('reportprocesses');

        Route::get('/reportprocesses/print/{date}/{leader}', [PFController::class, 'processReportPrint'])
            ->name('reportprocesses.print');

        Route::get('/reportentries', function () {
            return view('processes.reportentries');
        })->name('reportentries');

        // Almacenamiento de PDFs generados en el navegador
        Route::post('/pdf/store', [PFController::class, 'storePdf'])
            ->name('pdf.store');

    //
});
