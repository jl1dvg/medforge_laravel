<?php

namespace App\Support;

class AgendaViewHelper
{
    public static function badgeClass(?string $estado): string
    {
        $normalized = strtoupper(trim((string) $estado));

        return match ($normalized) {
            'AGENDADO', 'PROGRAMADO' => 'badge bg-primary-light text-primary',
            'LLEGADO', 'EN CURSO' => 'badge bg-success-light text-success',
            'ATENDIDO', 'COMPLETADO' => 'badge bg-success text-white',
            'CANCELADO' => 'badge bg-danger-light text-danger',
            'NO LLEGO', 'NO LLEGÓ', 'NO_ASISTIO', 'NO ASISTIO' => 'badge bg-warning-light text-warning',
            default => 'badge bg-secondary',
        };
    }

    public static function coverageBadge(?string $estado): string
    {
        return match ($estado) {
            'Con Cobertura' => 'badge bg-success',
            'Sin Cobertura' => 'badge bg-danger',
            default => 'badge bg-secondary',
        };
    }
}
