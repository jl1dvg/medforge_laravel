<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\LegacyDashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly LegacyDashboardService $legacy)
    {
    }

    public function __invoke(Request $request): View
    {
        $range = $this->legacy->resolveDateRange(
            $request->string('start_date')->toString(),
            $request->string('end_date')->toString()
        );

        $data = $this->legacy->build($range['start'], $range['end']);

        return view('dashboard.legacy.index', array_merge($data, [
            'pageTitle' => 'Dashboard',
        ]));
    }
}
