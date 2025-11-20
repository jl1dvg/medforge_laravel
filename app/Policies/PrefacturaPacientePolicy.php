<?php

namespace App\Policies;

use App\Models\PrefacturaPaciente;
use App\Models\User;
use App\Support\LegacyPermissions;

class PrefacturaPacientePolicy
{
    public function viewAny(?User $user): bool
    {
        return $this->canAccessBilling($user);
    }

    public function view(?User $user, PrefacturaPaciente $prefactura): bool
    {
        return $this->canAccessBilling($user);
    }

    private function canAccessBilling(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        $permissions = LegacyPermissions::normalize($user->permisos ?? []);

        return LegacyPermissions::containsAny($permissions, [
            'reportes.view',
            'reportes.export',
            'superuser',
            'administrativo',
        ]);
    }
}
