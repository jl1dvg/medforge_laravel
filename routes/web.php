<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Legacy\ModuleBrowserController;
use App\Http\Controllers\Pacientes\PacienteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/auth/login', [LoginController::class, 'create'])->name('login');
    Route::post('/auth/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/auth/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/auth/logout', [LoginController::class, 'destroy']);

    Route::prefix('agenda')
        ->name('agenda.')
        ->middleware('can:agenda.view')
        ->group(function (): void {
            Route::get('/', [AgendaController::class, 'index'])->name('index');
            Route::get('/visitas/{visitId}', [AgendaController::class, 'show'])
                ->whereNumber('visitId')
                ->name('visits.show');
        });

    Route::prefix('pacientes')->name('pacientes.')->group(function () {
        Route::get('/', [PacienteController::class, 'index'])->name('index');
        Route::post('/datatable', [PacienteController::class, 'datatable'])->name('datatable');
        Route::get('/{hcNumber}', [PacienteController::class, 'show'])
            ->where('hcNumber', '[A-Za-z0-9\-]+')
            ->name('show');
    });

    Route::prefix('legacy')->name('legacy.')->group(function () {
        Route::resource('modules', ModuleBrowserController::class)
            ->only(['index', 'show'])
            ->parameter('modules', 'module');
    });
});
