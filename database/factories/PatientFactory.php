<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'hc_number' => 'HC' . fake()->unique()->numerify('####'),
            'fname' => fake()->firstName(),
            'mname' => fake()->optional()->firstName(),
            'lname' => fake()->lastName(),
            'lname2' => fake()->optional()->lastName(),
            'afiliacion' => fake()->randomElement(['Privado', 'Seguro', 'N/A']),
            'celular' => fake()->optional()->phoneNumber(),
            'fecha_nacimiento' => fake()->date(),
        ];
    }
}
