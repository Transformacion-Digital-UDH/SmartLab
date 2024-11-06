<?php

namespace Database\Seeders;

use App\Models\Equipo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EquiposSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipos = [
            [
                'nombre' => 'Equipo 1',
                'codigo' => 'E001',
                'tipo' => 'Reservable',
                'descripcion' => 'Descripción del Equipo 1',
                'estado' => 'Activo',
                'is_active' => true,
                'area_id' => 1, // Asegúrate de que exista un área con ID 1
            ],
            [
                'nombre' => 'Equipo 2',
                'codigo' => 'E002',
                'tipo' => 'No reservable',
                'descripcion' => 'Descripción del Equipo 2',
                'estado' => 'Inactivo',
                'is_active' => true,
                'area_id' => 1,
            ],
            [
                'nombre' => 'Equipo 3',
                'codigo' => 'E003',
                'tipo' => 'Suministro',
                'descripcion' => 'Descripción del Equipo 3',
                'estado' => 'Reservado',
                'is_active' => true,
                'area_id' => 1,
            ],
            [
                'nombre' => 'Equipo 4',
                'codigo' => 'E004',
                'tipo' => 'Reservable',
                'descripcion' => 'Descripción del Equipo 4',
                'estado' => 'Prestado',
                'is_active' => false,
                'area_id' => 1,
            ],
            [
                'nombre' => 'Equipo 5',
                'codigo' => 'E005',
                'tipo' => 'No reservable',
                'descripcion' => 'Descripción del Equipo 5',
                'estado' => 'Activo',
                'is_active' => true,
                'area_id' => 1,
            ],
        ];

        foreach ($equipos as $equipo) {
            Equipo::create($equipo);
        }
    }
}
