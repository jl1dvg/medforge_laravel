<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\ProjectedProcedure;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ProjectedProcedure>
 */
class ProjectedProcedureFactory extends Factory
{
    protected $model = ProjectedProcedure::class;

    public function definition(): array
    {
        return [
            'form_id' => fake()->unique()->numerify('PROC####'),
            'hc_number' => Patient::factory()->create()->hc_number,
            'procedimiento_proyectado' => fake()->sentence(3),
            'doctor' => fake()->name(),
            'fecha' => fake()->dateTimeBetween('-1 day', '+1 day'),
            'hora' => fake()->dateTimeBetween('-1 hours', '+2 hours'),
            'estado_agenda' => fake()->randomElement(['Agendado', 'Programado', 'Completado']),
            'sede_departamento' => fake()->randomElement(['Quito', 'Guayaquil']),
            'visita_id' => null,
        ];
    }

    public function forVisit(Visit $visit): static
    {
        return $this->state(fn () => [
            'visita_id' => $visit->id,
            'hc_number' => $visit->hc_number,
        ]);
    }
}
