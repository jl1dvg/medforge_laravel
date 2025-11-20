<?php

namespace App\Providers;

use App\Models\Patient;
use App\Models\PrefacturaPaciente;
use App\Models\SolicitudProcedimiento;
use App\Models\Visit;
use App\Policies\PatientPolicy;
use App\Policies\PrefacturaPacientePolicy;
use App\Policies\SolicitudProcedimientoPolicy;
use App\Policies\VisitPolicy;
use App\Support\LegacyPermissions;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Patient::class => PatientPolicy::class,
        PrefacturaPaciente::class => PrefacturaPacientePolicy::class,
        SolicitudProcedimiento::class => SolicitudProcedimientoPolicy::class,
        Visit::class => VisitPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

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
