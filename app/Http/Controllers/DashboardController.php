<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardStatsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

        $users = DB::table('users')
            ->orderByDesc('id')
            ->limit(25)
            ->get()
            ->map(function ($record) {
                $username = $record->username ?? $record->email ?? 'usuario-'.$record->id;
                $fullName = $record->nombre ?? $record->name ?? $username;

                return [
                    'id' => $record->id,
                    'username' => $username,
                    'email' => $record->email ?? null,
                    'nombre' => $fullName,
                    'especialidad' => $record->especialidad ?? null,
                    'subespecialidad' => $record->subespecialidad ?? null,
                    'is_approved' => (bool) ($record->is_approved ?? false),
                    'role' => $record->role ?? ($record->role_name ?? 'Usuario'),
                    'firma' => $record->firma ?? null,
                    'biografia' => $record->biografia ?? null,
                    'created_at' => $record->created_at ?? null,
                ];
            });

        return view('dashboard.index', [
            'counts' => $this->stats->summaryCounts(),
            'trend' => $this->stats->proceduresTrend($rangeStart, $rangeEnd),
            'recentSurgeries' => $this->stats->recentSurgeries($rangeStart, $rangeEnd),
            'topProcedures' => $this->stats->topProcedures($rangeStart, $rangeEnd),
            'range' => [
                'start' => $rangeStart->toFormattedDateString(),
                'end' => $rangeEnd->toFormattedDateString(),
            ],
            'users' => $users,
        ]);
    }
}
