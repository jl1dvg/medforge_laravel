<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Visit>
 */
class VisitFactory extends Factory
{
    protected $model = Visit::class;

    public function definition(): array
    {
        return [
            'hc_number' => Patient::factory(),
            'fecha_visita' => fake()->dateTimeBetween('-2 days', '+2 days'),
            'hora_llegada' => fake()->dateTimeBetween('-1 hours', '+1 hours'),
            'usuario_registro' => fake()->userName(),
        ];
    }

    public function forPatient(Patient $patient): static
    {
        return $this->state(fn () => [
            'hc_number' => $patient->hc_number,
        ]);
    }
}
