<?php

namespace Database\Factories;

use App\Models\ProjectedProcedure;
use App\Models\ProjectedProcedureState;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ProjectedProcedureState>
 */
class ProjectedProcedureStateFactory extends Factory
{
    protected $model = ProjectedProcedureState::class;

    public function definition(): array
    {
        return [
            'form_id' => fake()->unique()->numerify('PROC####'),
            'estado' => fake()->randomElement(['Agendado', 'Programado', 'Completado']),
            'fecha_hora_cambio' => fake()->dateTimeBetween('-1 week', 'now'),
        ];
    }

    public function forProcedure(ProjectedProcedure $procedure): static
    {
        return $this->state(fn () => [
            'form_id' => $procedure->form_id,
        ]);
    }
}
