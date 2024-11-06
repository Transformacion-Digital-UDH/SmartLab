<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            [
                'nombre' => 'Área 1',
                'descripcion' => 'Descripción del Área 1',
                'aforo' => 20,
                'is_active' => true,
                'laboratorio_id' => 1, 
            ],
            [
                'nombre' => 'Área 2',
                'descripcion' => 'Descripción del Área 2',
                'aforo' => 30,
                'is_active' => true,
                'laboratorio_id' => 1,
            ],
            [
                'nombre' => 'Área 3',
                'descripcion' => 'Descripción del Área 3',
                'aforo' => 25,
                'is_active' => true,
                'laboratorio_id' => 1,
            ],
            [
                'nombre' => 'Área 4',
                'descripcion' => 'Descripción del Área 4',
                'aforo' => 15,
                'is_active' => true,
                'laboratorio_id' => 1,
            ],
            [
                'nombre' => 'Área 5',
                'descripcion' => 'Descripción del Área 5',
                'aforo' => 40,
                'is_active' => false,
                'laboratorio_id' => 1,
            ],
        ];

        foreach ($areas as $area) {
            Area::create($area);
        }
    }
}
