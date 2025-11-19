<?php

namespace Tests\Feature;

use App\Services\Dashboard\DashboardStatsService;
use Carbon\Carbon;
use Database\Seeders\LegacyDashboardSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacyDashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_matches_legacy_kpis_with_seeded_ranges(): void
    {
        $this->seed(LegacyDashboardSeeder::class);

        $service = $this->app->make(DashboardStatsService::class);

        $counts = $service->summaryCounts();
        $this->assertSame(2, $counts['patients']);
        $this->assertSame(5, $counts['protocols']);
        $this->assertSame(2, $counts['users']);

        $start = Carbon::parse('2024-07-01');
        $end = Carbon::parse('2024-07-05');
        $trend = $service->proceduresTrend($start, $end);
        $this->assertSame(['2024-07-01', '2024-07-02', '2024-07-03', '2024-07-04', '2024-07-05'], $trend['labels']);
        $this->assertSame([1, 1, 2, 0, 1], $trend['data']);
        $this->assertSame(5, $trend['total']);

        $recent = $service->recentSurgeries($start, $end, 3);
        $this->assertCount(3, $recent);
        $this->assertSame('HC-001', $recent->first()['hc_number']);

        $top = $service->topProcedures($start, $end, 2);
        $this->assertSame(['FACOEMULSIFICACIÓN', 'VITRECTOMÍA'], $top['labels']);
        $this->assertSame([3, 2], $top['data']);
    }
}
