<?php

namespace Tests\Feature\Agenda;

use App\Models\PatientIdentityCertification;
use App\Models\ProjectedProcedure;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_agenda(): void
    {
        $this->get(route('agenda.index'))
            ->assertRedirect(route('login'));
    }

    public function test_user_without_permission_gets_forbidden(): void
    {
        $user = User::factory()->create(['permisos' => []]);

        $this->actingAs($user)
            ->get(route('agenda.index'))
            ->assertForbidden();
    }

    public function test_index_displays_projected_procedures(): void
    {
        $user = User::factory()->create(['permisos' => ['agenda.view']]);
        $visit = Visit::factory()->create();

        ProjectedProcedure::factory()
            ->forVisit($visit)
            ->create([
                'procedimiento_proyectado' => 'Facoemulsificación',
                'doctor' => 'Dra. Carter',
                'estado_agenda' => 'Agendado',
                'sede_departamento' => 'Quito',
            ]);

        $response = $this->actingAs($user)->get(route('agenda.index'));

        $response->assertOk()
            ->assertSee('Facoemulsificación', false)
            ->assertSee('Dra. Carter', false)
            ->assertSee('Ver encuentro', false);
    }

    public function test_visit_view_shows_details_and_states(): void
    {
        $user = User::factory()->create(['permisos' => ['agenda.view']]);
        $visit = Visit::factory()->create();

        $procedure = ProjectedProcedure::factory()
            ->forVisit($visit)
            ->create([
                'procedimiento_proyectado' => 'Vitrectomía',
                'estado_agenda' => 'Completado',
            ]);

        $procedure->states()->create([
            'estado' => 'Completado',
            'fecha_hora_cambio' => now(),
        ]);

        $certification = new PatientIdentityCertification([
            'patient_id' => $visit->hc_number,
            'status' => 'verified',
        ]);
        $certification->last_verification_at = now();
        $certification->save();

        $response = $this->actingAs($user)->get(route('agenda.visits.show', $visit->id));

        $response->assertOk()
            ->assertSee('Encuentro #' . $visit->id, false)
            ->assertSee('Vitrectomía', false)
            ->assertSee('Certificación biométrica vigente', false)
            ->assertSee('Completado', false);
    }

    public function test_visit_not_found_shows_friendly_message(): void
    {
        $user = User::factory()->create(['permisos' => ['agenda.view']]);

        $this->actingAs($user)
            ->get(route('agenda.visits.show', 999))
            ->assertOk()
            ->assertSee('Encuentro no encontrado', false)
            ->assertSee('Regresar a la agenda', false);
    }
}
