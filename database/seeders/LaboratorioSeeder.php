<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Laboratorio;

class LaboratorioSeeder extends Seeder
{
    public function run(): void
    {
        Laboratorio::create([
            'nombre' => 'Star Lab UDH',
            'codigo' => 'LAB01-STR',
            'descripcion' => 'Laboratorio de prototipado y creación de productos mínimos viables.',
            'aforo' => 40,
            'email' => 'starlab@udh.edu.pe',
            'inauguracion' => '2024-05-13',
            'is_active' => true,
        ]);

        Laboratorio::create([
            'nombre' => 'Visual Art Estudio',
            'codigo' => 'LAB02-VAE',
            'descripcion' => 'laboratorio de creación de contenido digital.',
            'aforo' => 10,
            'email' => 'starlab@udh.edu.pe',
            'inauguracion' => '2024-05-13',
            'is_active' => true,
        ]);

        Laboratorio::create([
            'nombre' => 'Transformación Digital',
            'codigo' => 'LAB03-LTD',
            'descripcion' => 'Laboratorio de desarollo de software, inteligencia artificial y tecnologías innovadoras.',
            'aforo' => 60,
            'email' => 'transformaciondigital@udh.edu.pe',
            'inauguracion' => '2024-05-13',
            'is_active' => true,
        ]);
    }
}
