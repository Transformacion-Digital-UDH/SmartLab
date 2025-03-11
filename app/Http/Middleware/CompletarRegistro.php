<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CompletarRegistro
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Verificar si es necesario completar el registro
        if (empty($user->se_registro)) {
            // Si la solicitud está enviando datos hacia la ruta completar.registro,
            // permitirla pasar sin interferir para que el controller pueda procesarla
            if ($request->route()->getName() === 'completar.registro' && $request->isMethod('post')) {
                $data = $request->validate([
                    'dni' => 'required|string|max:8',
                    'nombres' => 'required|string|max:255',
                    'apellidos' => 'required|string|max:255',
                    'celular' => 'required|string|max:9',
                    'codigo' => 'nullable|string|max:255',
                    'razon_registro' => 'nullable|string|max:1000',
                ]);

                DB::table('users')
                    ->where('id', Auth::id())
                    ->update([
                        'dni'            => $data['dni'],
                        'nombres'        => $data['nombres'],
                        'apellidos'      => $data['apellidos'],
                        'celular'        => $data['celular'],
                        'codigo'         => $data['codigo'] ?? null,
                        'razon_registro' => $data['razon_registro'] ?? null,
                        'se_registro'    => true,
                    ]);

                return redirect()->route('dashboard');
            }
            return redirect()->route('completar.registro');
        }
        return $next($request);
    }
}
