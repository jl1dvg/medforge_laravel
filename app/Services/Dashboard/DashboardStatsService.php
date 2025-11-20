<?php

namespace App\Services\Dashboard;

use App\Models\Patient;
use App\Models\Protocol;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardStatsService
{
    /**
     * Totals for the hero cards.
     *
     * @return array<string, int>
     */
    public function summaryCounts(): array
    {
        return [
            'patients' => Patient::query()->count(),
            'protocols' => Protocol::query()->count(),
            'users' => User::query()->count(),
        ];
    }

    /**
     * Procedures grouped per day for charting.
     *
     * @return array{labels: list<string>, data: list<int>, total:int}
     */
    public function proceduresTrend(Carbon $start, Carbon $end): array
    {
        $rows = Protocol::query()
            ->selectRaw('DATE(fecha_inicio) as day, COUNT(*) as total')
            ->betweenDates($start, $end)
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $labels = [];
        $data = [];
        $cursor = $start->copy();

        while ($cursor <= $end) {
            $label = $cursor->format('Y-m-d');
            $labels[] = $label;
            $data[] = (int) ($rows[$label] ?? 0);
            $cursor->addDay();
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'total' => array_sum($data),
        ];
    }

    /**
     * Last surgeries performed within the window.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function recentSurgeries(Carbon $start, Carbon $end, int $limit = 6): Collection
    {
        return Protocol::query()
            ->with('patient')
            ->betweenDates($start, $end)
            ->orderByDesc('fecha_inicio')
            ->limit($limit)
            ->get()
            ->map(function (Protocol $protocol) {
                return [
                    'id' => $protocol->id,
                    'membrete' => $protocol->membrete,
                    'fecha_inicio' => $protocol->fecha_inicio,
                    'hc_number' => $protocol->hc_number,
                    'patient_name' => $protocol->patient?->full_name,
                    'doctor_name' => 'Sin asignar',
                ];
            });
    }

    /**
     * Top procedures by membrete.
     *
     * @return array{labels:list<string>, data:list<int>}
     */
    public function topProcedures(Carbon $start, Carbon $end, int $limit = 6): array
    {
        $rows = Protocol::query()
            ->selectRaw('membrete, COUNT(*) as total')
            ->betweenDates($start, $end)
            ->groupBy('membrete')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();

        return [
            'labels' => $rows->pluck('membrete')->map(fn ($label) => $label ?: 'Sin membrete')->all(),
            'data' => $rows->pluck('total')->map(fn ($value) => (int) $value)->all(),
        ];
    }
}
