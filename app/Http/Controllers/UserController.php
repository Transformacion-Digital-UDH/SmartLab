<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::select('id', 'nombres', 'apellidos', 'dni', 'email', 'rol', 'estado_cuenta', 'codigo', 'celular', 'password')
            ->where('rol', '!=', 'Admin')
            ->where('is_active', true);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('dni', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", ["%{$search}%"]);
            });
        }

        $usuarios = $query->orderBy('id', 'desc')->get();

        // Se separan en dos arrays según el rol
        $usuarios_registrados = $usuarios->where('rol', 'Invitado')->values();
        $usuarios_libres = $usuarios->where('rol', 'Libre')->values();

        // Agregar el campo laboratorio_registro a cada usuario libre
        $usuarios_libres->transform(function ($usuario) {
            // Usamos la relación para obtener la primera asistencia del usuario
            $primerAsistencia = $usuario->asistencias()->oldest('created_at')->first();

            $usuario->laboratorio_registro = $primerAsistencia && $primerAsistencia->laboratorio
            ? $primerAsistencia->laboratorio->nombre : null;

            return $usuario;
        });

        return Inertia::render('Usuarios/Index', [
            'usuarios_registrados' => $usuarios_registrados,
            'usuarios_libres' => $usuarios_libres,
            'search' => $request->input('search', ''),
        ]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'nombres'         => 'required|max:255',
            'apellidos'       => 'nullable|max:255',
            'dni'             => 'required|numeric|digits:8|unique:users,dni',
            'email'           => 'nullable|email|max:255|unique:users,email',
            'password'        => 'required|min:6',
            'is_active'       => 'boolean',
            'celular'         => 'nullable|numeric|digits:9',
            'rol'             => 'required|in:Libre,Invitado,Admin',
            // Validación para estado_cuenta y razon_registro:
            'estado_cuenta'   => 'nullable|in:"En revisión","Aprobada","Desaprobada","Suspendida"',
            'razon_registro'  => 'nullable|string',
        ], [
            'required'  => 'Este campo es obligatorio.',
            'email'     => 'Ingrese un correo electrónico válido.',
            'unique'    => 'Este valor ya está registrado.',
            'max'       => 'Este campo no puede exceder de :max caracteres.',
            'numeric'   => 'Este campo debe ser un número.',
            'digits'    => 'Este campo debe tener :digits dígitos.',
        ]);

        User::create([
            'nombres'         => $request->nombres,
            'apellidos'       => $request->apellidos,
            'dni'             => $request->dni,
            'email'           => $request->email,
            'celular'         => $request->celular,
            'password'        => Hash::make($request->password),
            'codigo'          => $request->codigo,
            'rol'             => $request->rol,
            'is_active'       => $request->is_active ?? true,
            // Si no se envía un valor, se asigna por defecto "En revisión"
            'estado_cuenta'   => $request->estado_cuenta ?? 'En revisión',
            'razon_registro'  => $request->razon_registro,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente');
    }


    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'nombres'         => 'nullable|max:255',
            'apellidos'       => 'nullable|max:255',
            'rol'             => 'required|in:Libre,Invitado,Admin',
            'celular'         => 'nullable|numeric|digits:9',
            // Validación para estado_cuenta y razon_registro:
            'estado_cuenta'   => 'nullable|in:"En revisión","Aprobada","Desaprobada","Suspendida"',
            'razon_registro'  => 'nullable|string',
        ], [
            'required'  => 'Este campo es obligatorio.',
            'numeric'   => 'Este campo debe ser un número.',
            'digits'    => 'Este campo debe tener :digits dígitos.',
        ]);

        $usuario->update([
            'nombres'         => $request->nombres,
            'apellidos'       => $request->apellidos,
            'dni'             => $request->dni,
            'email'           => $request->email,
            'celular'         => $request->celular,
            'password'        => $request->password ? Hash::make($request->password) : $usuario->password,
            'codigo'          => $request->codigo,
            'rol'             => $request->rol,
            'is_active'       => $request->is_active ?? true,
            // Actualización de los nuevos campos:
            'estado_cuenta'   => $request->estado_cuenta,
            'razon_registro'  => $request->razon_registro,
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
        return User::select('id', 'nombres', 'apellidos', 'dni', 'email', 'rol', 'is_active', 'codigo', 'celular', 'password')
            ->where('is_active', true)
            ->orderBy('nombres')
            ->get();
    }
}
