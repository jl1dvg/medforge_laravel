<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PatientIdentityCertification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\PatientIdentityCertification>
 */
class PatientIdentityCertificationFactory extends Factory
{
    protected $model = PatientIdentityCertification::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['verified', 'pending', 'revoked']);

        return [
            'patient_id' => Patient::factory()->create()->hc_number,
            'status' => $status,
            'expired_at' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'last_verification_at' => $status === 'verified'
                ? fake()->dateTimeBetween('-2 weeks', 'now')
                : null,
            'document_number' => fake()->numerify('17######'),
            'document_type' => 'cedula',
        ];
    }
}
