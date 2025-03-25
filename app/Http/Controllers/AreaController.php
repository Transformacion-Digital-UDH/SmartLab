<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Laboratorio;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AreaController extends Controller
{
    protected $rules = [
        'nombre' => 'required|max:100',
        'descripcion' => 'nullable',
        'aforo' => 'nullable|integer|min:1',
        'is_active' => 'boolean',
        'tipo' => 'required|string|max:50',
        'laboratorio_id' => 'required|exists:laboratorios,id',
    ];

    // Listar las áreas en la vista
    public function index()
    {
        $user = Auth::user();

        $areasQuery = Area::with('laboratorio',  'fotos')
            ->where('is_active', true);

        // No filtrar por laboratorio si el usuario es "Admin" y no tiene un laboratorio seleccionado
        if (!($user->rol === 'Admin' && $user->laboratorio_seleccionado === null)) {
            $areasQuery->where('laboratorio_id', $user->laboratorio_seleccionado);
        }

        $areas = $areasQuery->orderBy('id', 'desc')->get();
        $laboratorios = Laboratorio::all();

        return Inertia::render('Areas/Index', [
            'areas' => $areas,
            'laboratorios' => $laboratorios,
        ]);
    }

    public function json($laboratorio_id)
    {
        if (Laboratorio::where('id', $laboratorio_id)->exists()) {

            $areas = Area::where('laboratorio_id', $laboratorio_id)
                ->where('is_active', true)
                ->orderBy('id', 'desc')
                ->get();

            return response()->json($areas);
        }
    }

    // Crear un área
    public function store(Request $request)
    {
        $request->validate($this->rules);
        Area::create($request->all());
    }

    // Actualizar un área
    public function update(Request $request, Area $area)
    {
        $request->validate($this->rules);
        $area->update($request->all());
    }

    // Eliminar un área (desactivar)
    public function destroy(Area $area)
    {
        $area->is_active = false;
        $area->save();
    }
}


