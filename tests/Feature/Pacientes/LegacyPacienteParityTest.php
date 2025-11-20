<?php

namespace Tests\Feature\Pacientes;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacyPacienteParityTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_index_matches_current_view(): void
    {
        $user = User::factory()->create(['permisos' => ['pacientes.view']]);

        $current = $this->actingAs($user)->get(route('pacientes.index'));
        $legacy = $this->actingAs($user)->get(route('legacy.pacientes.index'));

        $current->assertOk();
        $legacy->assertOk();

        $this->assertSame($current->getContent(), $legacy->getContent());
    }

    public function test_legacy_datatable_matches_current_payload(): void
    {
        $user = User::factory()->create(['permisos' => ['pacientes.view']]);
        $patient = Patient::factory()->create([
            'hc_number' => 'HC-LEG-001',
            'fname' => 'Ana',
            'lname' => 'García',
            'afiliacion' => 'IESS',
        ]);

        $payload = [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => $patient->hc_number],
        ];

        $current = $this->actingAs($user)->postJson(route('pacientes.datatable'), $payload);
        $legacy = $this->actingAs($user)->postJson(route('legacy.pacientes.datatable'), $payload);

        $current->assertOk();
        $legacy->assertOk();

        $this->assertSame($current->json(), $legacy->json());
    }

    public function test_api_legacy_datatable_parity_with_web_response(): void
    {
        $user = User::factory()->create(['permisos' => ['pacientes.view']]);
        $patient = Patient::factory()->create([
            'hc_number' => 'HC-LEG-API',
            'fname' => 'Majo',
            'lname' => 'Quispe',
            'afiliacion' => 'Particular',
        ]);

        $payload = [
            'draw' => 2,
            'start' => 0,
            'length' => 5,
            'search' => ['value' => $patient->hc_number],
        ];

        $webResponse = $this->actingAs($user)->postJson(route('legacy.pacientes.datatable'), $payload);
        $apiResponse = $this->postJson(route('api.legacy.pacientes.datatable'), $payload);

        $webResponse->assertOk();
        $apiResponse->assertOk();

        $this->assertSame($webResponse->json(), $apiResponse->json());
    }
}
