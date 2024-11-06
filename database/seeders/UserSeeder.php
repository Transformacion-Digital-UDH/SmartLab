<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un usuario con rol Libre
        User::create([
            'nombres' => 'Carlos',
            'apellidos' => 'Pérez',
            'dni' => '12345678',
            'email' => 'libre@smartlab.udh',
            'password' => Hash::make('libre@smartlab.udh'),
            'rol' => 'Libre',
            'email_verified_at' => now(),
            'is_active' => true,
            'codigo' => Str::random(6),
        ]);

        // Crear un usuario con rol Invitado
        User::create([
            'nombres' => 'Ana',
            'apellidos' => 'López',
            'dni' => '87654321',
            'email' => 'invitado@smartlab.udh',
            'password' => Hash::make('invitado@smartlab.udh'),
            'rol' => 'Invitado',
            'email_verified_at' => now(),
            'is_active' => true,
            'codigo' => Str::random(6),
        ]);

        // Crear un usuario con rol Miembro
        User::create([
            'nombres' => 'Luis',
            'apellidos' => 'González',
            'dni' => '11223344',
            'email' => 'miembro@smartlab.udh',
            'password' => Hash::make('miembro@smartlab.udh'),
            'rol' => 'Miembro',
            'email_verified_at' => now(),
            'is_active' => true,
            'codigo' => Str::random(6),
        ]);

        // Crear un usuario con rol Miembro
        User::create([
            'nombres' => 'Abimael',
            'apellidos' => 'Saenz',
            'dni' => '71223344',
            'email' => 'coordinador@smartlab.udh',
            'password' => Hash::make('coordinador@smartlab.udh'),
            'rol' => 'Coordinador',
            'email_verified_at' => now(),
            'is_active' => true,
            'codigo' => Str::random(6),
        ]);

        // Crear un usuario con rol Responsable
        User::create([
            'nombres' => 'María',
            'apellidos' => 'Martínez',
            'dni' => '22334455',
            'email' => 'responsable@smartlab.udh',
            'password' => Hash::make('responsable@smartlab.udh'),
            'rol' => 'Responsable',
            'email_verified_at' => now(),
            'is_active' => true,
            'codigo' => Str::random(6),
        ]);

        // Crear un usuario con rol Admin
        User::create([
            'nombres' => 'Jorge',
            'apellidos' => 'Ramírez',
            'dni' => '33445566',
            'email' => 'admin@smartlab.udh',
            'password' => Hash::make('admin@smartlab.udh'),
            'rol' => 'Admin',
            'email_verified_at' => now(),
            'is_active' => true,
            'codigo' => Str::random(6),
        ]);
    }
}
