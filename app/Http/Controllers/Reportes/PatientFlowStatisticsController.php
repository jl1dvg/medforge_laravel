<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reportes\PatientFlowStatsRequest;
use App\Services\Legacy\PatientFlowStatisticsService;
use Illuminate\Http\JsonResponse;

class PatientFlowStatisticsController extends Controller
{
    public function __construct(private readonly PatientFlowStatisticsService $patientFlowStatisticsService)
    {
    }

    public function __invoke(PatientFlowStatsRequest $request): JsonResponse
    {
        $data = $this->patientFlowStatisticsService->fetch($request->validated());

        return response()->json($data);
    }
}
