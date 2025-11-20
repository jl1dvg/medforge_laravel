<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    /**
     * Simple placeholder dashboard while legacy modules are migrated.
     */
    public function __invoke(): View
    {
        return view('dashboard.index', $this->dashboardService->buildDashboardData());
    }
}
