<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;

class CompletarRegistro
{
    /**
     * Muestra el formulario de completar registro.
     */
    public function create()
    {
        $user = Auth::user();
        if (empty($user->se_registro)) {
            return Inertia::render('Auth/CompletarRegistro', compact('user'));
        }
        return redirect()->route('dashboard');
    }

    /**
     * Procesa el formulario de completar registro.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'dni'            => ['required', 'integer', 'digits:8'],
            'nombres'        => ['required', 'string', 'max:255'],
            'apellidos'      => ['required', 'string', 'max:255'],
            'codigo'         => ['nullable', 'integer', 'digits:10'],
            'celular'        => ['required', 'integer', 'digits:9'],
            'razon_registro' => ['nullable', 'string'],
            'is_active'      => ['boolean'],
        ]);

        // Buscar el usuario autenticado y actualizar sus datos
        $user = User::findOrFail(Auth::id());
        $user->dni       = $request->dni;
        $user->nombres   = $request->nombres;
        $user->apellidos = $request->apellidos;
        $user->codigo    = $request->codigo;
        $user->celular   = $request->celular;
        $user->rol       = "Invitado";
        $user->se_registro = 1;
        $user->save();

        return redirect()->route('dashboard');
    }
}
