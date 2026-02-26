<?php

use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',])->group(function () {

    Route::get('/', function () {
        return view('welcome');
    });
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

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
    //
});
