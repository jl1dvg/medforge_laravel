<?php

namespace App\Http\Controllers\Api\Legacy;

use App\Http\Controllers\Controller;
use App\Services\Legacy\LegacyModuleRegistry;
use Illuminate\Http\JsonResponse;

class LegacyModuleAssetController extends Controller
{
    public function __construct(private readonly LegacyModuleRegistry $registry)
    {
    }

    public function index(string $module): JsonResponse
    {
        $manifest = $this->registry->get($module);
        abort_unless($manifest, 404);

        return response()->json([
            'data' => $this->registry->resolveAssetMetadata($manifest['assets']),
        ]);
    }

    public function show(string $module, string $type): JsonResponse
    {
        $manifest = $this->registry->get($module);
        abort_unless($manifest, 404);

        $assets = $this->registry->resolveAssetMetadata($manifest['assets']);
        abort_unless(isset($assets[$type]), 404);

        return response()->json([
            'data' => [
                'type' => $type,
                'entries' => $assets[$type],
            ],
        ]);
    }
}
