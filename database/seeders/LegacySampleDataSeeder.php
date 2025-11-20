<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\PatientIdentityCertification;
use App\Models\PrefacturaPaciente;
use App\Models\ProjectedProcedure;
use App\Models\ProjectedProcedureState;
use App\Models\Protocol;
use App\Models\Role;
use App\Models\SolicitudProcedimiento;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Seeder;

class LegacySampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::factory()->create([
            'name' => 'Administrador legacy',
            'permissions' => ['superuser', 'pacientes.view', 'agenda.view', 'billing.view'],
        ]);

        $user = User::factory()->create([
            'username' => 'legacy-admin',
            'nombre' => 'Coordinación Legacy',
            'permisos' => ['superuser', 'pacientes.view', 'agenda.view', 'billing.view'],
            'role_id' => $role->id,
        ]);

        $patient = Patient::factory()->create([
            'hc_number' => 'HC-LEG-001',
            'fname' => 'Andrea',
            'lname' => 'Salazar',
            'lname2' => 'Cruz',
            'afiliacion' => 'IESS',
        ]);

        $visit = Visit::factory()->forPatient($patient)->create([
            'usuario_registro' => $user->username,
        ]);

        $procedure = ProjectedProcedure::factory()
            ->forVisit($visit)
            ->create([
                'form_id' => 'PROC-LEG-001',
                'procedimiento_proyectado' => 'FACOEMULSIFICACIÓN - OJO DERECHO',
                'estado_agenda' => 'Agendado',
                'doctor' => 'Dra. Ana Pérez',
                'sede_departamento' => 'Quito',
            ]);

        ProjectedProcedureState::factory()
            ->forProcedure($procedure)
            ->create([
                'estado' => 'Programado',
            ]);

        SolicitudProcedimiento::factory()->create([
            'hc_number' => $patient->hc_number,
            'procedimiento' => 'Vitrectomía',
            'form_id' => 'SOL-LEG-001',
            'tipo' => 'Cirugía',
        ]);

        Protocol::factory()->create([
            'hc_number' => $patient->hc_number,
            'membrete' => 'FACOEMULSIFICACIÓN',
            'form_id' => 'FORM-LEG-001',
        ]);

        PrefacturaPaciente::factory()->create([
            'hc_number' => $patient->hc_number,
            'form_id' => 'PF-LEG-001',
            'cod_derivacion' => 'LEGACY-01',
        ]);

        PatientIdentityCertification::factory()->create([
            'patient_id' => $patient->hc_number,
            'status' => 'verified',
        ]);
    }
}
