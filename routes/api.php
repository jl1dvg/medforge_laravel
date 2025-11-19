<?php

use App\Http\Controllers\Api\Legacy\LegacyModuleAssetController;
use App\Http\Controllers\Api\Legacy\LegacyModuleController;
use App\Http\Controllers\Api\Pacientes\SolicitudDetalleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'legacy.module'])
    ->prefix('legacy')
    ->name('api.legacy.')
    ->group(function (): void {
        Route::apiResource('modules', LegacyModuleController::class)
            ->only(['index', 'show'])
            ->parameter('modules', 'module');

        Route::apiResource('modules.assets', LegacyModuleAssetController::class)
            ->only(['index', 'show'])
            ->parameter('modules', 'module')
            ->parameter('assets', 'type');
    });

Route::middleware('api')
    ->prefix('pacientes')
    ->name('api.pacientes.')
    ->group(function (): void {
        Route::get('{hcNumber}/solicitudes/{formId}', [SolicitudDetalleController::class, 'show'])
            ->name('solicitudes.show');
    });
