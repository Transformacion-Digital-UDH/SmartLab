<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class MiembroController extends Controller
{
    public function index()
    {
        $miembros = User::where('rol', 'Miembro')
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('Miembros/Index', [
            'miembros' => $miembros,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|max:255',
            'apellidos' => 'required|max:255',
            'dni' => 'required|numeric|digits:8|unique:users,dni',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|min:6',
            'is_active' => 'boolean',
        ], [
            'required' => 'Este campo es obligatorio.',
            'email' => 'Ingrese un correo electrónico válido.',
            'unique' => 'Este valor ya está registrado.',
            'max' => 'Este campo no puede exceder de :max caracteres.',
            'numeric' => 'Este campo debe ser un número.',
            'digits' => 'Este campo debe tener :digits dígitos.',
            'in' => 'El rol seleccionado no es válido.',
        ]);

        User::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'dni' => $request->dni,
            'email' => $request->email,
            'celular' => $request->celular,
            'password' => Hash::make($request->password),
            'codigo' => $request->codigo,
            'rol' => 'Miembro',
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('miembros.index')->with('success', 'Miembro creado exitosamente');
    }

    public function update(Request $request, User $miembro)
    {
        $request->validate([
            'nombres' => 'required|max:255',
            'apellidos' => 'required|max:255',
            'rol' => 'required|in:Libre,Invitado,Miembro,Coordinador',
        ]);

        $miembro->update([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'dni' => $request->dni,
            'email' => $request->email,
            'celular' => $request->celular,
            'password' => $request->password ? Hash::make($request->password) : $miembro->password,
            'codigo' => $request->codigo,
            'rol' => $request->rol,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('miembros.index')->with('success', 'Miembro actualizado exitosamente');
    }

    public function destroy(User $miembro)
    {
        $miembro->is_active = false;
        $miembro->save();

        return redirect()->route('miembros.index')->with('success', 'Miembro desactivado exitosamente');
    }
}
