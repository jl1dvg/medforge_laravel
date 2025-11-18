<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardStatsService;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardStatsService $stats)
    {
    }

    /**
     * Simple placeholder dashboard while legacy modules are migrated.
     */
    public function __invoke(): View
    {
            $rangeEnd = Carbon::now();
            $rangeStart = $rangeEnd->copy()->subDays(13);

            return view('dashboard.index', [
                'counts' => $this->stats->summaryCounts(),
                'trend' => $this->stats->proceduresTrend($rangeStart, $rangeEnd),
                'recentSurgeries' => $this->stats->recentSurgeries($rangeStart, $rangeEnd),
                'topProcedures' => $this->stats->topProcedures($rangeStart, $rangeEnd),
                'range' => [
                    'start' => $rangeStart->toFormattedDateString(),
                    'end' => $rangeEnd->toFormattedDateString(),
                ],
            ]);
    }
}
