<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Laboratorio;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * API: Obtener todas las áreas activas de un laboratorio.
     */
    public function index($laboratorio_id)
    {
        if (Laboratorio::where('id', $laboratorio_id)->exists()) {

            $areas = Area::where('laboratorio_id', $laboratorio_id)
                    ->where('is_active', true)
                    ->orderBy('id', 'desc')
                    ->get();

            return response()->json($areas);
        }
    }

    /**
     * API: Guardar un nuevo área.
     */
    public function store(Request $request)
    {
        // Validación de los datos
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'aforo' => 'nullable|integer',
            'laboratorio_id' => 'required|exists:laboratorios,id',
        ]);

        // Crear el área
        $area = Area::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'aforo' => $validated['aforo'] ?? null,
            'laboratorio_id' => $validated['laboratorio_id'],
            'is_active' => true,
        ]);

        // Retornar respuesta JSON con el área creada
        return response()->json($area, 201);
    }

    /**
     * API: Obtener un área específica.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * API: Actualizar un área específica.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * API: Eliminar un área específica.
     */
    public function destroy(string $id)
    {

    }
}
