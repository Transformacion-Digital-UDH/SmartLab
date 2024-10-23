<?php

namespace Database\Factories;

use App\Models\Asistencia;
use Illuminate\Database\Eloquent\Factories\Factory;

class AsistenciaFactory extends Factory
{
    protected $model = Asistencia::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'usuario_id' => $this->faker->randomElement([1,2,3,4,5]),
            'hora_entrada' =>  $this->faker->dateTimeBetween('-1 year', 'now'),
            'hora_salida' =>  $this->faker->randomElement([null,$this->faker->dateTimeBetween('-1 year', 'now')]),
            'tarea' => $this->faker->sentence(),
        ];
    }
}
