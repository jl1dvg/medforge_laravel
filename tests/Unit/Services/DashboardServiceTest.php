<?php

namespace Tests\Unit\Services;

use App\Services\Dashboard\DashboardStatsService;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_build_dashboard_data_returns_expected_payload(): void
    {
        $stats = Mockery::mock(DashboardStatsService::class);
        $stats->shouldReceive('summaryCounts')->andReturn(['patients' => 1, 'protocols' => 2, 'users' => 3]);
        $stats->shouldReceive('proceduresTrend')->andReturn(['labels' => ['2024-01-01'], 'data' => [1], 'total' => 1]);
        $stats->shouldReceive('recentSurgeries')->andReturn(collect());
        $stats->shouldReceive('topProcedures')->andReturn(['labels' => ['Proc'], 'data' => [5]]);

        $service = new DashboardService($stats);

        $result = $service->buildDashboardData();

        $this->assertSame(['patients' => 1, 'protocols' => 2, 'users' => 3], $result['counts']);
        $this->assertSame(['labels' => ['2024-01-01'], 'data' => [1], 'total' => 1], $result['trend']);
        $this->assertSame(['labels' => ['Proc'], 'data' => [5]], $result['topProcedures']);
        $this->assertArrayHasKey('users', $result);
        $this->assertIsIterable($result['users']);
    }
}
