<?php

namespace Tests\Unit\Services;

use App\Models\Patient;
use App\Services\PatientNameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientNameServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_full_name_with_last_names_first(): void
    {
        $patient = Patient::factory()->create([
            'fname' => 'John',
            'mname' => 'Paul',
            'lname' => 'Doe',
            'lname2' => 'Smith',
        ]);

        $service = new PatientNameService();

        $this->assertSame('Doe Smith John Paul', $service->getFullName($patient->hc_number));
    }

    public function test_it_trims_missing_components_and_unknown_patients(): void
    {
        $patient = Patient::factory()->create([
            'fname' => 'Jane',
            'mname' => null,
            'lname' => 'Roe',
            'lname2' => null,
        ]);

        $service = new PatientNameService();

        $this->assertSame('Roe Jane', $service->getFullName($patient->hc_number));
        $this->assertSame('Desconocido', $service->getFullName('missing-hc-number'));
    }
}
