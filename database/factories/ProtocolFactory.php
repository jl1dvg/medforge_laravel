<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Protocol;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Protocol>
 */
class ProtocolFactory extends Factory
{
    protected $model = Protocol::class;

    public function definition(): array
    {
        return [
            'hc_number' => Patient::factory()->create()->hc_number,
            'membrete' => strtoupper(fake()->randomElement(['FACOEMULSIFICACIÓN', 'VITRECTOMÍA'])),
            'procedimiento_id' => fake()->unique()->numerify('PROC-###'),
            'fecha_inicio' => fake()->dateTimeBetween('-1 month', 'now'),
            'form_id' => fake()->unique()->numerify('FORM-###'),
        ];
    }
}
