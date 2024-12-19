<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Models\Equipo;
use App\Models\Recurso;
use Illuminate\Support\Facades\Log;

class EquipoController extends Controller
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
        'recursos' => 'array',
        'recursos.*' => 'integer|exists:recursos,id',

        'fotos_nuevas.*' => ['image'],
        'fotos_eliminadas' => ['array'],
        'fotos_eliminadas.*' => ['integer', 'exists:fotos_recursos,id'],
    ];

    // Guardar un nuevo equipo
    public function store(Request $request)
    {
        $request->merge([
            'is_active' => filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN),
        ]);

        $request->validate($this->rules);
        $equipo = Equipo::create($request->all());

        // Procesar y guardar las imágenes si se enviaron
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $imagen) {
                $ruta = $imagen->store('equipos', 'public');
                $equipo->fotos()->create(['ruta' => $ruta]);
            }
        }

        // Actualizar los recursos asignados al equipo
        if ($request->has('recursos')) {
            Recurso::whereIn('id', $request->recursos)
                ->update(['equipo_id' => $equipo->id]);
        }

        return response()->json([
            'message' => 'Equipo creado exitosamente.',
            'equipo' => $equipo,
        ]);
    }


    // Actualizar un equipo existente
    public function update(Request $request, Equipo $equipo)
    {
        // Validación de los datos del equipo
        $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'nullable|string|max:20', // Cambié a required
            'tipo' => 'required|in:Reservable,No reservable,Suministro',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:Activo,Inactivo,Reservado,Prestado',
            'is_active' => 'boolean',
            'area_id' => 'nullable|exists:areas,id', // Cambié a required
            'fotos_nuevas.*' => 'image',
            'fotos_eliminadas' => 'array',
            'fotos_eliminadas.*' => 'integer|exists:foto_equipos,id', // Cambié a fotos_equipos
        ]);

        // Actualizar datos del equipo
        $equipo->update($request->all());

        // Eliminar fotos enviadas para eliminación
        if ($request->has('fotos_eliminadas')) {
            foreach ($request->fotos_eliminadas as $fotoId) {
                $foto = $equipo->fotos()->find($fotoId);
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
                $ruta = $imagen->store('equipos', 'public'); // Cambié la carpeta a 'equipos'
                $equipo->fotos()->create(['ruta' => $ruta]);
            }
        }
    }

    // Eliminar un equipo
    public function destroy(Equipo $equipo)
    {
        $equipo->update(['is_active' => false]);
    }
}
