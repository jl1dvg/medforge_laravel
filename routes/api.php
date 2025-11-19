<?php

use App\Http\Controllers\Api\Legacy\LegacyModuleAssetController;
use App\Http\Controllers\Api\Legacy\LegacyModuleController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')
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
