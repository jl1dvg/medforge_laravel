<?php

namespace App\Services;

use App\Services\Dashboard\DashboardStatsService;
use App\Models\User;
use Carbon\Carbon;

class DashboardService
{
    public function __construct(private readonly DashboardStatsService $stats)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function buildDashboardData(): array
    {
        $rangeEnd = Carbon::now();
        $rangeStart = $rangeEnd->copy()->subDays(13);

        $users = User::query()
            ->orderByDesc('id')
            ->limit(25)
            ->get()
            ->map(function (User $user) {
                $username = $user->username ?? $user->email ?? 'usuario-'.$user->id;
                $fullName = $user->nombre ?? $user->name ?? $username;

                return [
                    'id' => $user->id,
                    'username' => $username,
                    'email' => $user->email,
                    'nombre' => $fullName,
                    'especialidad' => $user->especialidad,
                    'subespecialidad' => $user->subespecialidad,
                    'is_approved' => (bool) ($user->is_approved ?? false),
                    'role' => $user->role?->name ?? $user->role_name ?? 'Usuario',
                    'firma' => $user->firma,
                    'biografia' => $user->biografia,
                    'created_at' => $user->created_at,
                ];
            });

        return [
            'counts' => $this->stats->summaryCounts(),
            'trend' => $this->stats->proceduresTrend($rangeStart, $rangeEnd),
            'recentSurgeries' => $this->stats->recentSurgeries($rangeStart, $rangeEnd),
            'topProcedures' => $this->stats->topProcedures($rangeStart, $rangeEnd),
            'range' => [
                'start' => $rangeStart->toFormattedDateString(),
                'end' => $rangeEnd->toFormattedDateString(),
            ],
            'users' => $users,
        ];
    }
}
