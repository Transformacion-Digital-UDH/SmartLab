<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::select('id', 'nombres', 'apellidos', 'dni', 'email', 'rol', 'is_active', 'codigo', 'celular', 'password')
            ->where('rol', '!=', 'Admin') 
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('Usuarios/Index', [
            'usuarios' => $usuarios,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|max:255',
            'apellidos' => 'nullable|max:255',
            'dni' => 'required|numeric|digits:8|unique:users,dni',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|min:6',
            'is_active' => 'boolean',
            'celular' => 'nullable|numeric|digits:9',
        ], [
            'required' => 'Este campo es obligatorio.',
            'email' => 'Ingrese un correo electrónico válido.',
            'unique' => 'Este valor ya está registrado.',
            'max' => 'Este campo no puede exceder de :max caracteres.',
            'numeric' => 'Este campo debe ser un número.',
            'digits' => 'Este campo debe tener :digits dígitos.',
        ]);

        User::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'dni' => $request->dni,
            'email' => $request->email,
            'celular' => $request->celular,
            'password' => Hash::make($request->password),
            'codigo' => $request->codigo,
            'rol' => 'Libre',
            'is_active' => $request->is_active ?? true,
            'celular' => $request->celular,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente');
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'nombres' => 'required|max:255',
            'apellidos' => 'nullable|max:255',
            'rol' => 'required|in:Libre,Invitado,Miembro,Coordinador',
            'celular' => 'nullable|numeric|digits:9',
        ], [
            'required' => 'Este campo es obligatorio.',
            'numeric' => 'Este campo debe ser un número.',
            'digits' => 'Este campo debe tener :digits dígitos.',
        ]);

        $usuario->update([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'dni' => $request->dni,
            'email' => $request->email,
            'celular' => $request->celular,
            'password' => $request->password ? Hash::make($request->password) : $usuario->password,
            'codigo' => $request->codigo,
            'rol' => $request->rol,
            'is_active' => $request->is_active ?? true,
            'celular' => $request->celular,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente');
    }

    public function destroy(User $usuario)
    {
        $usuario->is_active = false;
        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario desactivado exitosamente');
    }


    // Devuelve la lista de usuarios en JSON
    public function getUsuarios()
    {
        return User::select('id', 'nombres', 'apellidos', 'dni', 'email', 'rol', 'is_active', 'codigo', 'celular','password')
        ->where('is_active', true)
        ->orderBy('nombres')
        ->get();    
    }

}
