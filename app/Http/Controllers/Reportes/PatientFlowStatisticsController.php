<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PatientFlowStatisticsController extends Controller
{
    /**
     * Placeholder invokable controller to unblock route resolution until the
     * legacy report is fully migrated.
     */
    public function __invoke(): JsonResponse
    {
        return $this->placeholder();
    }

    /**
     * Some legacy routes reference an explicit `index` action. Keep it
     * available to avoid "Invalid route action" errors while migration is
     * in progress.
     */
    public function index(): JsonResponse
    {
        return $this->placeholder();
    }

    private function placeholder(): JsonResponse
    {
        return response()->json([
            'message' => 'Patient flow statistics report is not yet implemented.',
            'status' => 'pending',
        ]);
    }
}
