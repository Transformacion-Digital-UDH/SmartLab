<?php

namespace Database\Seeders;

use App\Models\Proyecto;
use App\Models\User;
use App\Models\MiembroProyecto;
use Illuminate\Database\Seeder;

class ProyectoSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener todos los usuarios existentes para asignar como responsables
        $usuarios = User::all();

        // Generar 10 proyectos
        for ($i = 1; $i <= 10; $i++) {
            // Crear un proyecto
            $proyecto = Proyecto::create([
                'nombre' => 'Proyecto ' . $i,
                'descripcion' => 'Descripción del Proyecto ' . $i,
                'fecha_inicio' => now()->subDays(rand(1, 365))->format('Y-m-d'),
                'fecha_fin' => now()->addDays(rand(1, 365))->format('Y-m-d'),
                'estado' => ['Sin iniciar', 'En proceso', 'Completado', 'Cancelado'][rand(0, 3)],
                'is_active' => true,
                'responsable_id' => $usuarios->random()->id,
            ]);

            // Seleccionar 4 usuarios aleatorios para ser participantes del proyecto
            $participantes = $usuarios->random(4);

            // Crear registros en la tabla miembro_proyectos
            foreach ($participantes as $participante) {
                MiembroProyecto::create([
                    'usuario_id' => $participante->id,
                    'proyecto_id' => $proyecto->id,
                    'is_active' => true,
                ]);
            }
        }
    }
}
