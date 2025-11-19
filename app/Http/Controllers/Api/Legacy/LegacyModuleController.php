<?php

namespace App\Http\Controllers\Api\Legacy;

use App\Http\Controllers\Controller;
use App\Services\Legacy\LegacyModuleRegistry;
use Illuminate\Http\JsonResponse;

class LegacyModuleController extends Controller
{
    public function __construct(private readonly LegacyModuleRegistry $registry)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => array_values($this->registry->all()),
        ]);
    }

    public function show(string $module): JsonResponse
    {
        $manifest = $this->registry->get($module);
        abort_unless($manifest, 404);

        return response()->json([
            'data' => $manifest,
        ]);
    }
}
