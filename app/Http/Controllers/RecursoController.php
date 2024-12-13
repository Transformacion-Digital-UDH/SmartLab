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
        $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'nullable|string|max:20',
            'tipo' => 'required|in:Reservable,No reservable,Suministro',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:Activo,Inactivo,Reservado,Prestado',
            'is_active' => 'boolean',
            'area_id' => 'nullable|exists:areas,id',
            'equipo_id' => 'nullable|exists:equipos,id',
            // 'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación de imágenes
        ]);

        // Actualizar datos del recurso
        $recurso->update($request->all());

        // Fotos enviadas desde el frontend
        $fotosNuevas = $request->file('fotos', []);
        $fotosExistentes = $request->input('fotos', []);

        // Eliminar las fotos que ya no están presentes en la lista
        foreach ($recurso->fotos as $foto) {
            if (!in_array($foto->ruta, $fotosExistentes)) {
                // Eliminar foto del almacenamiento
                Storage::disk('public')->delete($foto->ruta);
                // Eliminar foto de la base de datos
                $foto->delete();
            }
        }

        // Agregar las fotos nuevas
        foreach ($fotosNuevas as $imagen) {
            $ruta = $imagen->store('recursos', 'public');
            $recurso->fotos()->create([
                'ruta' => $ruta,
            ]);
        }
    }

    // Eliminar un recurso
    public function destroy(Recurso $recurso)
    {
        $recurso->update(['is_active' => false]);
    }
}
