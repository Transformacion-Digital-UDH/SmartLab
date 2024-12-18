<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Laboratorio;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    protected $reglas = [
        'nombre' => ['required', 'string', 'max:80'],
        'descripcion' => ['nullable', 'string', 'max:160'],
        'aforo' => ['nullable', 'integer'],
        'laboratorio_id' => ['nullable', 'exists:laboratorios,id'],
    ];

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
        $request->validate($this->reglas);

        // Crear el área
        Area::create([
            'nombre' => $request->nombre ?? null,
            'descripcion' => $request->descripcion ?? null,
            'aforo' => $request->aforo ?? null,
            'laboratorio_id' => $request->laboratorio_id ?? null,
            'is_active' => true,
        ]);
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
    public function update(Request $request, $area_id)
    {
        // Validación de los datos recibidos
        $request->validate($this->reglas);

        // Buscar el área por ID y actualizarla
        $area = Area::findOrFail($area_id);
        $area->nombre = $request->nombre;
        $area->descripcion = $request->descripcion;
        $area->aforo = $request->aforo;
        $area->save();
    }

    /**
     * API: Eliminar un área específica.
     */
    public function destroy($area_id)
    {
        $area = Area::findOrFail($area_id);
        $area->update(['is_active' => false]);
    }
}
