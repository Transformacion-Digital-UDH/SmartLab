<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Laboratorio;
use Carbon\Carbon;

class LaboratorioController extends Controller
{
    public function index()
    {
        // Obtener los laboratorios desde la base de datos
        $laboratorios = Laboratorio::with('responsable')->get();

        // Pasar los laboratorios a la vista usando Inertia
        return Inertia::render('Laboratorios/Index', [
            'laboratorios' => $laboratorios,
        ]);
    }

    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'nombre' => 'required|max:100',
            'codigo' => 'nullable|max:20',
            'descripcion' => 'nullable|max:255',
            'aforo' => 'nullable|integer',
            'email' => 'nullable|email|max:100',
            'inauguracion' => 'nullable|date',
            'responsable_id' => 'required|exists:users,id',
        ], [
            'required' => 'Este campo es obligatorio.',
            'email' => 'Ingrese un correo electrónico válido.',
            'max' => 'Este campo no puede exceder de :max caracteres.',
            'integer' => 'Este campo debe ser un número entero.',
            'date' => 'Ingrese una fecha válida.',
        ]);

        // Convertir la fecha de inauguración a formato 'Y-m-d'
        if ($request->has('inauguracion')) {
            $request->merge([
                'inauguracion' => date('Y-m-d', strtotime($request->inauguracion))
            ]);
        }

        // Crear el laboratorio
        $laboratorio = Laboratorio::create($request->all())->load('responsable');

        // Retornar el laboratorio recién creado como JSON
        return response()->json([
            'laboratorio' => $laboratorio
        ], 201);
    }



    public function update(Request $request, Laboratorio $laboratorio)
    {
        // Validación
        $validated = $request->validate([
            'nombre' => 'required|max:100',
            'codigo' => 'nullable|max:20',
            'descripcion' => 'nullable|max:255',
            'aforo' => 'nullable|integer',
            'email' => 'nullable|email|max:100',
            'inauguracion' => 'nullable|date',
            'responsable_id' => 'required|exists:users,id',
        ], [
            'required' => 'Este campo es obligatorio.',
            'email' => 'Ingrese un correo electrónico válido.',
            'max' => 'Este campo no puede exceder de :max caracteres.',
            'integer' => 'Este campo debe ser un número entero.',
            'date' => 'Ingrese una fecha válida.',
        ]);

        // Actualizar laboratorio
        $laboratorio->update($validated);

        // Redirigir con laboratorios actualizados usando Inertia
        $laboratorios = Laboratorio::all();
        return redirect()->route('laboratorios.index')->with('laboratorios', $laboratorios);
    }

    public function destroy(Laboratorio $laboratorio)
    {
        // Eliminar laboratorio
        $laboratorio->delete();

        // Redireccionar con éxito usando Inertia
        return redirect()->back()->with('success', 'Laboratorio eliminado con éxito.');
    }


}
