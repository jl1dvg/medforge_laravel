<?php

namespace App\Http\Controllers\Legacy;

use App\Http\Controllers\Controller;
use App\Services\Legacy\LegacyModuleRegistry;
use Illuminate\View\View;

class ModuleBrowserController extends Controller
{
    public function __construct(private readonly LegacyModuleRegistry $registry)
    {
    }

    public function index(): View
    {
        return view('legacy.modules.index', [
            'modules' => array_values($this->registry->all()),
        ]);
    }

    public function show(string $module): View
    {
        $manifest = $this->registry->get($module);
        abort_unless($manifest, 404);

        $viewPaths = array_map(fn (string $view): array => [
            'relative' => $view,
            'absolute' => $this->registry->viewsPath($view),
        ], $manifest['views'] ?? []);

        $moduleFolder = $manifest['folder'] ?? null;

        return view('legacy.modules.show', [
            'module' => $manifest,
            'paths' => [
                'controller' => $manifest['legacy_entry']
                    ? $this->registry->controllersPath($manifest['legacy_entry'])
                    : null,
                'views' => $viewPaths,
                'module_root' => $moduleFolder ? $this->registry->modulesPath($moduleFolder) : null,
            ],
            'assets' => $this->registry->resolveAssetMetadata($manifest['assets']),
        ]);
    }
}
