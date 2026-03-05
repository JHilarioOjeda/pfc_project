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
    //

    //ALMACEN
        Route::get('/storage', function () {
            return view('storage.storage');
        })->name('storage');

        Route::get('/storage/{id}', function ($id) {
            return view('storage.tarima', compact('id'));
        })->name('storage.tarima');
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
    //
});
