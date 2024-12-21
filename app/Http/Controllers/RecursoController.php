<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Equipo;
use App\Models\Recurso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class RecursoController extends Controller
{

    protected $rules = [
        'nombre' => ['required', 'string', 'max:100'],
        'codigo' => ['required', 'string', 'max:20'],
        'tipo' => ['required', 'in:Reservable,No reservable,Suministro'],
        'descripcion' => ['nullable', 'string'],
        'estado' => ['required', 'in:Activo,Inactivo,Reservado,Prestado'],
        'area_id' => ['nullable', 'exists:areas,id'],
        'equipo_id' => ['nullable', 'exists:equipos,id'],
        'is_active' => ['boolean'],
        'fotos.*' => ['image'],
        'fotos_nuevas.*' => ['image'],
        'fotos_eliminadas' => ['array'],
        'fotos_eliminadas.*' => ['integer', 'exists:fotos_recursos,id'],
    ];

    public function index(Request $request)
    {
        $recursos = Recurso::with('area', 'equipo',  'fotos')
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->get();

        $equipos = Equipo::with('area', 'recursos',  'fotos')
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->get();

        $areas = Area::orderBy('id', 'desc')->get();

        $tab = $request->query('tab', 1);

        return Inertia::render('Recursos/Index', [
            'recursos' => $recursos,
            'areas' => $areas,
            'equipos' => $equipos,
            'tab' => $tab,
        ]);
    }


    // Guardar un nuevo recurso
    public function store(Request $request)
    {
        $request->merge([
            'is_active' => filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN)
        ]);

        $request->validate($this->rules);
        $recurso = Recurso::create($request->all());

        // Procesar y guardar las imágenes si se enviaron
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $imagen) {
                $ruta = $imagen->store('recursos', 'public');
                $recurso->fotos()->create(['ruta' => $ruta]);
            }
        }
    }

    // Actualizar un recurso existente
    public function update(Request $request, Recurso $recurso)
    {
        $request->validate($this->rules);
        $recurso->update($request->all());

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
    }


    // Eliminar un recurso
    public function destroy(Recurso $recurso)
    {
        $recurso->update(['is_active' => false]);
    }
}
