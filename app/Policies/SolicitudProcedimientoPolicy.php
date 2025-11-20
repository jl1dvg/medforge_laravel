<?php

namespace App\Policies;

use App\Models\SolicitudProcedimiento;
use App\Models\User;
use App\Support\LegacyPermissions;

class SolicitudProcedimientoPolicy
{
    public function view(?User $user, SolicitudProcedimiento $solicitud): bool
    {
        return $this->canManageRequests($user);
    }

    public function viewAny(?User $user): bool
    {
        return $this->canManageRequests($user);
    }

    private function canManageRequests(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        $permissions = LegacyPermissions::normalize($user->permisos ?? []);

        return LegacyPermissions::containsAny($permissions, [
            'cirugias.view',
            'cirugias.manage',
            'superuser',
            'administrativo',
        ]);
    }
}
