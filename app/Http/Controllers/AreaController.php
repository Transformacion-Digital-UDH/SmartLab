<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $areas = Area::when($search, function ($query, $search) {
            return $query->where('nombre', 'like', "%{$search}%")
                ->orWhere('descripcion', 'like', "%{$search}%");
        })
            ->paginate(10);

        return response()->json($areas);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'aforo' => 'nullable|integer',
            'is_active' => 'boolean',
            'laboratorio_id' => 'required|exists:laboratorios,id',
        ]);

        $area = Area::create($validatedData);

        return response()->json($area, 201);
    }

    public function show($id)
    {
        $area = Area::findOrFail($id);

        return response()->json($area);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'aforo' => 'nullable|integer',
            'is_active' => 'boolean',
            'laboratorio_id' => 'required|exists:laboratorios,id',
        ]);

        $area = Area::findOrFail($id);

        $area->update($validatedData);

        return response()->json($area);
    }

    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        $area->delete();

        return response()->json(null, 204);
    }
}
