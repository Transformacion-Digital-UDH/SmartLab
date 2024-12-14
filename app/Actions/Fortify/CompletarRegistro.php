<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class CompletarRegistro
{
    public function create()
    {
        $user = Auth::user();
        if (empty($user->se_registro)) {
            return Inertia::render(
                'Auth/CompletarRegistro',
                compact('user')
            );
        }
        return redirect()->route('dashboard');
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'dni' => ['required', 'integer', 'min:8'],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'codigo' => ['required', 'integer', 'min:8'],
            'celular' => ['required', 'integer', 'digits:9'],
        ])->validate();

        $user = User::find(Auth::user()->id);
        $user->nombres = $request->nombres;
        $user->apellidos = $request->apellidos;
        $user->dni = $request->dni;
        $user->codigo = $request->codigo;
        $user->celular = $request->celular;
        $user->se_registro = 1;

        $user->save();
        return redirect()->intended(route('dashboard'));
    }
}
