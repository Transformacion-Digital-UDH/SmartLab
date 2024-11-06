<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Recurso;

class RecursosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recursos = [
            [
                'nombre' => 'Recurso 1',
                'codigo' => 'R001',
                'tipo' => 'Reservable',
                'descripcion' => 'Este es el recurso 1',
                'estado' => 'Activo',
                'is_active' => true,
                'area_id' => null,
                'equipo_id' => null,
            ],
            [
                'nombre' => 'Recurso 2',
                'codigo' => 'R002',
                'tipo' => 'No reservable',
                'descripcion' => 'Este es el recurso 2',
                'estado' => 'Inactivo',
                'is_active' => true,
                'area_id' => null,
                'equipo_id' => null,
            ],
            [
                'nombre' => 'Recurso 3',
                'codigo' => 'R003',
                'tipo' => 'Suministro',
                'descripcion' => 'Este es el recurso 3',
                'estado' => 'Reservado',
                'is_active' => true,
                'area_id' => null,
                'equipo_id' => null,
            ],
            [
                'nombre' => 'Recurso 4',
                'codigo' => 'R004',
                'tipo' => 'Reservable',
                'descripcion' => 'Este es el recurso 4',
                'estado' => 'Prestado',
                'is_active' => false,
                'area_id' => null,
                'equipo_id' => null,
            ],
            [
                'nombre' => 'Recurso 5',
                'codigo' => 'R005',
                'tipo' => 'No reservable',
                'descripcion' => 'Este es el recurso 5',
                'estado' => 'Activo',
                'is_active' => true,
                'area_id' => null,
                'equipo_id' => null,
            ],
            [
                'nombre' => 'Recurso 6',
                'codigo' => 'R006',
                'tipo' => 'Suministro',
                'descripcion' => 'Este es el recurso 6',
                'estado' => 'Inactivo',
                'is_active' => true,
                'area_id' => null,
                'equipo_id' => null,
            ],
            [
                'nombre' => 'Recurso 7',
                'codigo' => 'R007',
                'tipo' => 'Reservable',
                'descripcion' => 'Este es el recurso 7',
                'estado' => 'Reservado',
                'is_active' => true,
                'area_id' => null,
                'equipo_id' => null,
            ],
            [
                'nombre' => 'Recurso 8',
                'codigo' => 'R008',
                'tipo' => 'No reservable',
                'descripcion' => 'Este es el recurso 8',
                'estado' => 'Prestado',
                'is_active' => false,
                'area_id' => null,
                'equipo_id' => null,
            ],
        ];

        foreach ($recursos as $recurso) {
            Recurso::create($recurso);
        }
    }
}
