<?php

namespace Tests\Feature\Pacientes;

use App\Models\Patient;
use App\Models\PrefacturaPaciente;
use App\Models\Protocol;
use App\Models\SolicitudProcedimiento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PacienteRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_pacientes_index(): void
    {
        $this->get(route('pacientes.index'))
            ->assertRedirect(route('login'));
    }

    public function test_user_without_permission_gets_forbidden(): void
    {
        $user = User::factory()->create(['permisos' => []]);

        $this->actingAs($user)
            ->get(route('pacientes.index'))
            ->assertForbidden();
    }

    public function test_datatable_returns_patient_row(): void
    {
        $user = User::factory()->create(['permisos' => ['pacientes.view']]);
        $patient = Patient::factory()->create([
            'hc_number' => 'HC-PR-001',
            'fname' => 'Lucía',
            'lname' => 'Mena',
            'afiliacion' => 'Particular',
        ]);

        $response = $this->actingAs($user)->post(route('pacientes.datatable'), [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => 'HC-PR-001'],
        ]);

        $response->assertOk()
            ->assertJsonFragment([
                'hc_number' => 'HC-PR-001',
                'full_name' => 'Lucía Mena',
                'afiliacion' => 'Particular',
            ]);
    }

    public function test_show_displays_legacy_timeline_items(): void
    {
        $user = User::factory()->create(['permisos' => ['pacientes.view']]);
        $patient = Patient::factory()->create([
            'hc_number' => 'HC-PR-002',
            'fname' => 'Mateo',
            'lname' => 'Barrera',
        ]);

        SolicitudProcedimiento::factory()->create([
            'hc_number' => $patient->hc_number,
            'procedimiento' => 'Vitrectomía',
            'form_id' => 'SOL-PR-001',
        ]);

        PrefacturaPaciente::factory()->create([
            'hc_number' => $patient->hc_number,
            'cod_derivacion' => 'REF-01',
            'form_id' => 'PF-PR-001',
        ]);

        Protocol::factory()->create([
            'hc_number' => $patient->hc_number,
            'membrete' => 'FACOEMULSIFICACIÓN',
            'form_id' => 'FORM-PR-001',
        ]);

        $response = $this->actingAs($user)->get(route('pacientes.show', $patient->hc_number));

        $response->assertOk()
            ->assertSee('Paciente ' . $patient->hc_number, false)
            ->assertSee('Vitrectomía', false)
            ->assertSee('Prefactura', false)
            ->assertSee('FACOEMULSIFICACIÓN', false);
    }

    public function test_api_solicitud_detail_merges_consulta_data(): void
    {
        $patient = Patient::factory()->create(['hc_number' => 'HC-API-001']);
        $solicitud = SolicitudProcedimiento::factory()->create([
            'hc_number' => $patient->hc_number,
            'form_id' => 'SOL-API-001',
            'procedimiento' => 'Consulta Oftalmológica',
        ]);

        DB::table('consulta_data')->insert([
            'hc_number' => $patient->hc_number,
            'form_id' => $solicitud->form_id,
            'fecha' => now(),
            'diagnosticos' => json_encode([['idDiagnostico' => 'H25.1']]),
            'examen_fisico' => 'Agudeza visual 20/20',
        ]);

        $response = $this->getJson(route('api.pacientes.solicitudes.show', [
            'hcNumber' => $patient->hc_number,
            'formId' => $solicitud->form_id,
        ]));

        $response->assertOk()
            ->assertJsonFragment(['form_id' => 'SOL-API-001'])
            ->assertJsonFragment(['procedimiento' => 'Consulta Oftalmológica'])
            ->assertJsonFragment(['examen_fisico' => 'Agudeza visual 20/20']);
    }
}
