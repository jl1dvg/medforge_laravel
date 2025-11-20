<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PrefacturaPaciente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\PrefacturaPaciente>
 */
class PrefacturaPacienteFactory extends Factory
{
    protected $model = PrefacturaPaciente::class;

    public function definition(): array
    {
        $creation = fake()->dateTimeBetween('-2 months', 'now');

        return [
            'hc_number' => Patient::factory()->create()->hc_number,
            'cod_derivacion' => fake()->bothify('DER-###'),
            'fecha_vigencia' => fake()->dateTimeBetween('now', '+3 months'),
            'fecha_creacion' => $creation,
            'fecha_registro' => $creation,
            'procedimientos' => [
                ['codigo' => fake()->numerify('PRC-###'), 'descripcion' => 'Cirugía'],
                ['codigo' => fake()->numerify('PRC-###'), 'descripcion' => 'Control'],
            ],
            'form_id' => fake()->unique()->numerify('PF-####'),
        ];
    }
}
