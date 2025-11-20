<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\SolicitudProcedimiento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\SolicitudProcedimiento>
 */
class SolicitudProcedimientoFactory extends Factory
{
    protected $model = SolicitudProcedimiento::class;

    public function definition(): array
    {
        $created = fake()->dateTimeBetween('-2 weeks', 'now');

        return [
            'hc_number' => Patient::factory()->create()->hc_number,
            'procedimiento' => fake()->sentence(3),
            'created_at' => $created,
            'tipo' => fake()->randomElement(['Cirugía', 'Consulta', 'Control']),
            'form_id' => fake()->unique()->numerify('SOL-###'),
            'fecha' => $created,
            'turno' => fake()->time('H:i'),
        ];
    }
}
