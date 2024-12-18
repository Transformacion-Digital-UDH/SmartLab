<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Crear un usuario con rol Admin
        User::factory()->create([
            'dni' => '33445566',
            'nombres' => 'Jorge',
            'apellidos' => 'Ramírez',
            'email' => 'admin@smartlab.udh',
            'rol' => 'Admin',
        ]);

        // Crear un usuario con rol Responsable
        User::factory()->create([
            'dni' => '74937272',
            'nombres' => 'Abimael',
            'apellidos' => 'Fernandez Ventura',
            'email' => 'abimaelfv@hotmail.com',
            'rol' => 'Responsable',
        ]);

        User::factory()->create([
            'dni' => '71658076',
            'nombres' => 'Axel Texu',
            'apellidos' => 'Perez Lazarte',
            'email' => 'havook01@gmail.com',
            'rol' => 'Responsable',
            'celular' => '917289674',
        ]);

        // Crear un usuario con rol Coordinador
        User::factory()->create([
            'dni' => '11223355',
            'nombres' => 'Abimael',
            'apellidos' => 'Saenz',
            'email' => 'coordinador@smartlab.udh',
            'rol' => 'Coordinador',
        ]);

        // Crear un usuario con rol Libre
        User::factory()->create([
            'dni' => '12345678',
            'nombres' => 'Carlos',
            'apellidos' => 'Pérez',
            'email' => 'libre@smartlab.udh',
            'rol' => 'Libre',
        ]);

        // Crear un usuario con rol Invitado
        User::factory()->create([
            'dni' => '87654321',
            'nombres' => 'Ana',
            'apellidos' => 'López',
            'email' => 'invitado@smartlab.udh',
            'rol' => 'Invitado',
        ]);

        // Crear un usuario con rol Miembro
        User::factory()->create([
            'dni' => '11223344',
            'nombres' => 'Luis',
            'apellidos' => 'González',
            'email' => 'miembro@smartlab.udh',
            'rol' => 'Miembro',
        ]);
    }
}
