<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Models\Equipo;

class EquipoController extends Controller
{
        // Guardar un nuevo equipo
    public function store(Request $request)
    {
        $request->merge([
            'is_active' => filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN)
        ]);

        // Validación incluyendo las imágenes
        $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'nullable|string|max:20',  // Cambié a required ya que en el schema es obligatorio
            'tipo' => 'required|in:Reservable,No reservable,Suministro',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:Activo,Inactivo,Reservado,Prestado',
            'is_active' => 'boolean',
            'area_id' => 'nullable|exists:areas,id', // Cambié a required ya que en el schema es obligatorio
            'fotos.*' => 'image',
        ]);

        // Crear el equipo
        $equipo = Equipo::create($request->all());

        // Procesar y guardar las imágenes si se enviaron
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $imagen) {
                $ruta = $imagen->store('equipos', 'public'); // Cambié la carpeta a 'equipos'
                $equipo->fotos()->create(['ruta' => $ruta]);
            }
        }
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
