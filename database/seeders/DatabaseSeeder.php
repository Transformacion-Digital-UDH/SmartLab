<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);

        $this->call(RolesSeeder::class);

        $this->call(AsistenciaSeeder::class);

        $this->call(LaboratorioSeeder::class);

        $this->call(AreasSeeder::class);

        $this->call(RecursosSeeder::class);

        $this->call(EquiposSeeder::class);

        $this->call(ProyectoSeeder::class);

        $this->call(ReservaSeeder::class);

        $this->call(LaboratorioUserSeeder::class);

    }
}
