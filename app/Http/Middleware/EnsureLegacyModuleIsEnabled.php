<?php

namespace App\Http\Middleware;

use App\Services\Legacy\LegacyModuleRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLegacyModuleIsEnabled
{
    public function __construct(private readonly LegacyModuleRegistry $registry)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();

        if ($route === null) {
            return $next($request);
        }

        $module = $route->parameter('module');

        if ($module === null) {
            return $next($request);
        }

        $manifest = $this->registry->get($module);
        abort_unless($manifest, 404, 'Legacy module unavailable.');

        $request->attributes->set('legacyModule', $manifest);

        return $next($request);
    }
}
