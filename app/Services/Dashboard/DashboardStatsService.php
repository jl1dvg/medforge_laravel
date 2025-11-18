<?php

namespace App\Services\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
            'patients' => (int) DB::table('patient_data')->count(),
            'protocols' => (int) DB::table('protocolo_data')->count(),
            'users' => (int) DB::table('users')->count(),
        ];
    }

    /**
     * Procedures grouped per day for charting.
     *
     * @return array{labels: list<string>, data: list<int>, total:int}
     */
    public function proceduresTrend(Carbon $start, Carbon $end): array
    {
        $rows = DB::table('protocolo_data')
            ->selectRaw('DATE(fecha_inicio) as day, COUNT(*) as total')
            ->whereBetween('fecha_inicio', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
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
        return DB::table('protocolo_data as pr')
            ->join('patient_data as p', 'p.hc_number', '=', 'pr.hc_number')
            ->select([
                'pr.id',
                'pr.membrete',
                'pr.fecha_inicio',
                'p.hc_number',
                DB::raw("CONCAT(COALESCE(p.lname,''), ' ', COALESCE(p.lname2,''), ' ', COALESCE(p.fname,'')) as patient_name"),
                DB::raw('"Sin asignar" as doctor_name'),
            ])
            ->whereBetween('pr.fecha_inicio', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->orderByDesc('pr.fecha_inicio')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => (array) $row);
    }

    /**
     * Top procedures by membrete.
     *
     * @return array{labels:list<string>, data:list<int>}
     */
    public function topProcedures(Carbon $start, Carbon $end, int $limit = 6): array
    {
        $rows = DB::table('protocolo_data')
            ->selectRaw('membrete, COUNT(*) as total')
            ->whereBetween('fecha_inicio', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
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
