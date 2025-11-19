<?php

namespace App\Providers;

use App\Support\LegacyPermissions;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('agenda.view', function ($user): bool {
            if ($user === null) {
                return false;
            }

            $permissions = LegacyPermissions::normalize($user->permisos ?? []);

            return LegacyPermissions::containsAny($permissions, [
                'agenda.view',
                'agenda:read',
                'agenda',
                'agenda_access',
            ]);
        });
    }
}
