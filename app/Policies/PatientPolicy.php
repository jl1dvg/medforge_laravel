<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use App\Support\LegacyPermissions;

class PatientPolicy
{
    public function viewAny(?User $user): bool
    {
        return $this->canAccessPatients($user);
    }

    public function view(?User $user, Patient $patient): bool
    {
        return $this->canAccessPatients($user);
    }

    private function canAccessPatients(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        $permissions = LegacyPermissions::normalize($user->permisos ?? []);

        return LegacyPermissions::containsAny($permissions, [
            'pacientes.view',
            'pacientes.manage',
            'superuser',
            'administrativo',
        ]);
    }
}
