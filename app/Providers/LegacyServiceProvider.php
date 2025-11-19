<?php

namespace App\Providers;

use App\Services\Legacy\LegacyModuleRegistry;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class LegacyServiceProvider extends ServiceProvider
{
    /**
     * Register bindings related to the legacy modules.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(base_path('config/legacy.php'), 'legacy');

        $this->app->singleton(LegacyModuleRegistry::class, function ($app) {
            return new LegacyModuleRegistry($app['config']->get('legacy', []));
        });
    }

    /**
     * Bootstrap shared dependencies.
     */
    public function boot(): void
    {
        View::share('legacyModulesManifest', $this->app->make(LegacyModuleRegistry::class)->all());
    }
}
