<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Crear permisos
        Permission::create(['name' => 'Laboratorio']);
        Permission::create(['name' => 'Recursos']);
        Permission::create(['name' => 'Usuarios']);
        Permission::create(['name' => 'Asistencias']);
        Permission::create(['name' => 'Miembros']);
        Permission::create(['name' => 'Proyectos']);

        // Crear roles
        $admin = Role::create(['name' => 'Admin']);

        // Asignar permisos a roles
        $admin->givePermissionTo(['Laboratorio', 'Recursos', 'Usuarios', 'Asistencias', 'Miembros', 'Proyectos']);

         // Asignar rol admin
        $user = User::find(6);
        $user->assignRole('admin');

    }
}
