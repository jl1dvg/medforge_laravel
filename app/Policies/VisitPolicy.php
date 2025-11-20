<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Visit;
use App\Support\LegacyPermissions;

class VisitPolicy
{
    public function viewAny(?User $user): bool
    {
        return $this->canViewAgenda($user);
    }

    public function view(?User $user, Visit $visit): bool
    {
        return $this->canViewAgenda($user);
    }

    private function canViewAgenda(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        $permissions = LegacyPermissions::normalize($user->permisos ?? []);

        return LegacyPermissions::containsAny($permissions, [
            'agenda.view',
            'agenda:read',
            'agenda',
            'agenda_access',
            'cirugias.view',
            'cirugias.manage',
            'superuser',
            'administrativo',
        ]);
    }
}
