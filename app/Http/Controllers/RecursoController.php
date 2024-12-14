<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Equipo;
use App\Models\Recurso;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class RecursoController extends Controller
{
    public function index()
    {
        $recursos = Recurso::with('area', 'equipo',  'fotos')
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->get();

        $areas = Area::orderBy('id', 'desc')->get();
        $equipos = Equipo::orderBy('id', 'desc')->get();

        return Inertia::render('Recursos/Index', [
            'recursos' => $recursos,
            'areas' => $areas,
            'equipos' => $equipos,
        ]);
    }


    // Guardar un nuevo recurso
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'nullable|string|max:20',
            'tipo' => 'required|in:Reservable,No reservable,Suministro',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:Activo,Inactivo,Reservado,Prestado',
            'is_active' => 'boolean',
            'area_id' => 'nullable|exists:areas,id',
            'equipo_id' => 'nullable|exists:equipos,id',
        ]);

        Recurso::create($request->all());
    }

    // Actualizar un recurso existente
    public function update(Request $request, Recurso $recurso)
    {
        // Validación de los datos del recurso
        $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'nullable|string|max:20',
            'tipo' => 'required|in:Reservable,No reservable,Suministro',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:Activo,Inactivo,Reservado,Prestado',
            'is_active' => 'boolean',
            'area_id' => 'nullable|exists:areas,id',
            'equipo_id' => 'nullable|exists:equipos,id',
            'fotos_nuevas.*' => 'image|mimes:jpeg,png,jpg|max:2048', // Validar fotos nuevas
            'fotos_eliminadas' => 'array', // Validar IDs de fotos eliminadas
            'fotos_eliminadas.*' => 'integer|exists:fotos_recursos,id', // Validar que existan en la BD
        ]);

        // Actualizar datos del recurso
        $recurso->update($request->only([
            'nombre', 'codigo', 'tipo', 'descripcion', 'estado', 'is_active', 'area_id', 'equipo_id',
        ]));

        // Eliminar fotos enviadas para eliminación
        if ($request->has('fotos_eliminadas')) {
            foreach ($request->fotos_eliminadas as $fotoId) {
                $foto = $recurso->fotos()->find($fotoId);
                if ($foto) {
                    // Eliminar archivo físico y el registro en la base de datos
                    Storage::disk('public')->delete($foto->ruta);
                    $foto->delete();
                }
            }
        }

        // Agregar fotos nuevas
        if ($request->hasFile('fotos_nuevas')) {
            foreach ($request->file('fotos_nuevas') as $imagen) {
                $ruta = $imagen->store('recursos', 'public');
                $recurso->fotos()->create(['ruta' => $ruta]);
            }
        }

        // Responder con el recurso actualizado
        return response()->json([
            'mensaje' => 'Recurso actualizado exitosamente.',
            'recurso' => $recurso->load('fotos'), // Incluye las fotos actualizadas
        ], 200);
    }


    // Eliminar un recurso
    public function destroy(Recurso $recurso)
    {
        $recurso->update(['is_active' => false]);
    }
}
