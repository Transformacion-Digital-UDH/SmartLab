<?php

namespace App\Http\Controllers;

use App\Models\Laboratorio;
use App\Models\LaboratorioUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class MiembroController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // No filtrar por laboratorio si el usuario es "Admin" y no tiene un laboratorio seleccionado
        if (!($user->rol === 'Admin' && $user->laboratorio_seleccionado === null)) {

            $laboratorio = Laboratorio::find($user->laboratorio_seleccionado);

            $participantes = $laboratorio->participantes()
            ->where('users.is_active', true)
            ->orderBy('users.id', 'desc')
            ->get();

        } else {
            $participantes = collect();
        }

        $usuarios = User::where('is_active', true)
            ->get();

        return Inertia::render('Miembros/Index', [
            'miembros' => $participantes,
            'usuarios' => $usuarios,
        ]);
    }

    public function store(Request $request)
    {
        // Validar que el usuario seleccionado existe
        $request->validate([
            'usuario_id' => 'required|exists:users,id',
        ], [
            'required' => 'Este campo es obligatorio.',
            'exists' => 'El usuario seleccionado no existe.',
        ]);

        // Obtener el laboratorio seleccionado del usuario autenticado
        $user = Auth::user();
        $laboratorioId = $user->laboratorio_seleccionado;

        // Crear la relación en la tabla laboratorio_user
        LaboratorioUser::create([
            'user_id' => $request->usuario_id,
            'laboratorio_id' => $laboratorioId,
            'rol' => 'Miembro',
        ]);

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
    }

    public function destroy(Request $request, User $miembro)
    {
        $request->validate([
            'laboratorio_id' => 'required|integer',
            'rol' => 'required|string'
        ]);

        LaboratorioUser::where('user_id', $miembro->id)
            ->where('laboratorio_id', $request->laboratorio_id)
            ->where('rol', $request->rol)
            ->delete();
    }
}
