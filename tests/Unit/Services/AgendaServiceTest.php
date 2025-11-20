<?php

namespace Tests\Unit\Services;

use App\Models\Patient;
use App\Models\PatientIdentityCertification;
use App\Models\ProjectedProcedure;
use App\Models\Visit;
use App\Services\AgendaService;
use App\Services\IdentityVerificationPolicy;
use App\Services\PatientContextService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AgendaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_resolve_filters_orders_dates_and_trims(): void
    {
        Carbon::setTestNow('2024-05-10');

        $service = new AgendaService($this->mockPolicy(), $this->mockPatientContext());

        $filters = $service->resolveFilters([
            'fecha_inicio' => '2024-05-12',
            'fecha_fin' => '2024-05-01',
            'doctor' => '  Dr. Who  ',
            'estado' => '',
            'sede' => null,
            'solo_con_visita' => '1',
        ]);

        $this->assertSame('2024-05-01', $filters['fecha_inicio']);
        $this->assertSame('2024-05-12', $filters['fecha_fin']);
        $this->assertSame('Dr. Who', $filters['doctor']);
        $this->assertNull($filters['estado']);
        $this->assertTrue($filters['solo_con_visita']);
    }

    public function test_get_agenda_index_data_returns_available_metadata(): void
    {
        $service = new AgendaService($this->mockPolicy(), $this->mockPatientContext());

        ProjectedProcedure::factory()->create([
            'doctor' => 'Dr. Strange',
            'estado_agenda' => 'Agendado',
            'sede_departamento' => 'Quito',
            'fecha' => Carbon::parse('2024-05-10'),
        ]);

        ProjectedProcedure::factory()->create([
            'doctor' => 'Dra. Grey',
            'estado_agenda' => 'Programado',
            'id_sede' => 'Norte',
            'fecha' => Carbon::parse('2024-05-15'),
        ]);

        $filters = [
            'fecha_inicio' => '2024-05-01',
            'fecha_fin' => '2024-05-31',
            'doctor' => null,
            'estado' => null,
            'sede' => null,
            'solo_con_visita' => false,
        ];

        $data = $service->getAgendaIndexData($filters);

        $this->assertCount(2, $data['procedures']);
        $this->assertEquals(['Agendado', 'Programado'], $data['availableStates']);
        $this->assertEquals(['Dra. Grey', 'Dr. Strange'], $data['availableDoctors']);
        $this->assertCount(2, $data['availableLocations']);
    }

    public function test_get_visit_view_data_returns_visit_payload(): void
    {
        $policy = $this->mockPolicy(15);
        $patientContext = $this->mockPatientContext([
            'coverageStatus' => 'Con Cobertura',
            'timelineItems' => [['nombre' => 'Prefactura']],
        ]);

        $service = new AgendaService($policy, $patientContext);

        $patient = Patient::factory()->create();
        $visit = Visit::factory()->create(['hc_number' => $patient->hc_number]);
        $procedure = ProjectedProcedure::factory()->forVisit($visit)->create([
            'estado_agenda' => 'Programado',
            'doctor' => 'Dra. Carter',
        ]);

        PatientIdentityCertification::create([
            'patient_id' => $patient->hc_number,
            'status' => 'verified',
        ]);

        $result = $service->getVisitViewData($visit->id);

        $this->assertNotNull($result);
        $this->assertSame($visit->id, $result['visit']['id']);
        $this->assertSame('Con Cobertura', $result['visit']['estado_cobertura']);
        $this->assertSame($procedure->estado_agenda, $result['visit']['procedimientos'][0]['estado_agenda']);
        $this->assertFalse($result['identityVerification']['requires_checkin']);
        $this->assertSame(15, $result['identityVerification']['validity_days']);
        $this->assertSame($patient->full_name, $result['identityVerification']['summary']['patient_full_name']);
    }

    private function mockPolicy(int $validity = 365): IdentityVerificationPolicy
    {
        $policy = Mockery::mock(IdentityVerificationPolicy::class);
        $policy->shouldReceive('getValidityDays')->andReturn($validity);

        return $policy;
    }

    private function mockPatientContext(array $context = ['coverageStatus' => 'N/A', 'timelineItems' => []]): PatientContextService
    {
        $service = Mockery::mock(PatientContextService::class);
        $service->shouldReceive('buildContext')->andReturn($context);

        return $service;
    }
}
