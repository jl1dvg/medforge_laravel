<?php

namespace App\Http\Controllers\Legacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Legacy\BillingExcelRequest;
use App\Http\Requests\Legacy\BillingMonthExportRequest;
use App\Services\Legacy\BillingExportService;
use Symfony\Component\HttpFoundation\Response;

class BillingExportController extends Controller
{
    public function __construct(private readonly BillingExportService $billingExportService)
    {
    }

    public function export(BillingExcelRequest $request): Response
    {
        $validated = $request->validated();

        return $this->billingExportService->exportIndividual(
            $validated['form_id'],
            $validated['grupo'] ?? null
        );
    }

    public function exportMonth(BillingMonthExportRequest $request): Response
    {
        $validated = $request->validated();

        return $this->billingExportService->exportByMonth(
            $validated['mes'],
            $validated['grupo'] ?? null
        );
    }
}
