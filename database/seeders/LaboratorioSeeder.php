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
            'nombre' => 'Laboratorio de Química',
            'codigo' => 'LAB-Q123',
            'descripcion' => 'Laboratorio especializado en análisis químicos.',
            'aforo' => 30,
            'email' => 'quimica@ejemplo.com',
            'inauguracion' => '2020-03-15',
            'is_active' => true,
            'responsable_id' => 4, // Cambiado a 4
        ]);

        Laboratorio::create([
            'nombre' => 'Laboratorio de Biología',
            'codigo' => 'LAB-B456',
            'descripcion' => 'Laboratorio para investigaciones biológicas.',
            'aforo' => 25,
            'email' => 'biologia@ejemplo.com',
            'inauguracion' => '2019-08-20',
            'is_active' => true,
            'responsable_id' => 4, // Cambiado a 4
        ]);

        Laboratorio::create([
            'nombre' => 'Laboratorio de Física',
            'codigo' => 'LAB-F789',
            'descripcion' => 'Laboratorio con equipos para experimentos físicos.',
            'aforo' => 40,
            'email' => 'fisica@ejemplo.com',
            'inauguracion' => '2018-11-05',
            'is_active' => true,
            'responsable_id' => 4, // Cambiado a 4
        ]);

        Laboratorio::create([
            'nombre' => 'Laboratorio de Informática',
            'codigo' => 'LAB-I101',
            'descripcion' => 'Laboratorio equipado con computadoras de última generación.',
            'aforo' => 50,
            'email' => 'informatica@ejemplo.com',
            'inauguracion' => '2021-06-10',
            'is_active' => true,
            'responsable_id' => 4, // Cambiado a 4
        ]);

        Laboratorio::create([
            'nombre' => 'Laboratorio de Electrónica',
            'codigo' => 'LAB-E202',
            'descripcion' => 'Laboratorio con dispositivos electrónicos avanzados.',
            'aforo' => 35,
            'email' => 'electronica@ejemplo.com',
            'inauguracion' => '2022-01-25',
            'is_active' => false,
            'responsable_id' => 4, // Cambiado a 4
        ]);
    }
}
