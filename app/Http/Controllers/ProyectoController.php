<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Proyecto;
use App\Models\User;

class ProyectoController extends Controller
{
    protected $rules = [
        'nombre' => 'required|max:100',
        'descripcion' => 'nullable|max:255',
        'fecha_inicio' => 'nullable|date',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        'estado' => 'required|in:Sin iniciar,En proceso,Completado,Cancelado',
        'responsable_id' => 'nullable|exists:users,id',
    ];

    // Listar los proyectos en la vista
    public function index()
    {
        $proyectos = Proyecto::with('responsable')
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->get();

        $responsables = User::where('rol', 'Responsable')->get();

        return Inertia::render('Proyectos/Index', [
            'proyectos' => $proyectos,
            'responsables' => $responsables,
        ]);
    }

    // Crear el proyecto
    public function store(Request $request)
    {
        $request->validate($this->rules);
        Proyecto::create($request->all());
    }

    // Actualizar proyecto
    public function update(Request $request, Proyecto $proyecto)
    {
        $request->validate($this->rules);
        $proyecto->update($request->all());
    }

    // Eliminar proyecto
    public function destroy(Proyecto $proyecto)
    {
        $proyecto->is_active = false;
        $proyecto->save();
    }

    // Obtener participantes del proyecto
    public function obtenerParticipantes(Proyecto $proyecto)
    {
        $participantes = $proyecto->participantes()
        ->with(['usuario' => function ($query) {
            $query->select('id', 'nombres','apellidos', 'dni', 'email', 'celular');
        }])->get();

        return response()->json($participantes);
    }


    // Agregar un participante al proyecto
    public function agregarParticipante(Request $request, Proyecto $proyecto)
    {
        $request->validate([
            'usuario_id' => 'required|exists:users,id',
        ]);

        $proyecto->participantes()->create([
            'usuario_id' => $request->usuario_id,
        ]);

        return response()->json([
            'message' => 'Participante agregado correctamente.',
        ]);
    }

    // Quitar un participante del proyecto
    public function quitarParticipante(Proyecto $proyecto, $participanteId)
    {
        $proyecto->participantes()->where('id', $participanteId)->delete();

        return response()->json([
            'message' => 'Participante eliminado correctamente.',
        ]);
    }

}
