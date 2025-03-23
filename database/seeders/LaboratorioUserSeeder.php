<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Laboratorio;
use App\Models\LaboratorioUser;

class LaboratorioUserSeeder extends Seeder
{
    public function run()
    {
        // Recuperamos todos los usuarios y laboratorios existentes
        $users = User::all();
        $laboratorios = Laboratorio::all();

        // Definimos los roles posibles
        $roles = ['Miembro', 'Coordinador', 'Responsable'];

        // Verificamos que existan usuarios y laboratorios
        if ($users->isEmpty() || $laboratorios->isEmpty()) {
            $this->command->info('No hay usuarios o laboratorios existentes para asignar.');
            return;
        }

        // Por cada usuario asignamos uno o varios laboratorios de forma aleatoria
        foreach ($users as $user) {
            // Elegimos de manera aleatoria la cantidad de laboratorios a asignar (mínimo 1)
            $cantidad = rand(1, $laboratorios->count());

            // Seleccionamos laboratorios aleatorios
            $laboratoriosSeleccionados = $laboratorios->random($cantidad);

            // Si solo se selecciona un laboratorio, lo convertimos en colección
            if (!$laboratoriosSeleccionados instanceof \Illuminate\Support\Collection) {
                $laboratoriosSeleccionados = collect([$laboratoriosSeleccionados]);
            }

            // Asignamos cada laboratorio al usuario con un rol aleatorio
            foreach ($laboratoriosSeleccionados as $laboratorio) {
                LaboratorioUser::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'laboratorio_id' => $laboratorio->id,
                    ],
                    [
                        'rol' => $roles[array_rand($roles)]
                    ]
                );
            }
        }
    }
}
