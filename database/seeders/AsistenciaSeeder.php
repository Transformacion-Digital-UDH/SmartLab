<?php

namespace Database\Seeders;

use App\Models\Asistencia;
use Illuminate\Database\Seeder;

class AsistenciaSeeder extends Seeder
{
    public function run(): void
    {
        Asistencia::factory(50)->create();
    }
}
