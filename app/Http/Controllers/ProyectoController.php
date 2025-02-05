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

        // Por ahora cualquier usuario puede ser responsable de un proyecto
        $responsables = User::all();

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
            ->where('is_active', true) // Filtro por is_active
            ->with(['usuario' => function ($query) {
                $query->select('id', 'nombres', 'apellidos', 'dni', 'email', 'celular');
            }])
            ->get();

        return response()->json($participantes);
    }


    // Agregar un participante al proyecto
    public function agregarParticipante(Request $request, Proyecto $proyecto)
    {
        $request->validate([
            'usuario_ids' => 'required|array',
            'usuario_ids.*' => 'exists:users,id', // Validar que cada ID existe
        ]);

        // Registrar múltiples participantes
        foreach ($request->usuario_ids as $usuarioId) {
            $proyecto->participantes()->updateOrCreate([
                'usuario_id' => $usuarioId,
            ]);
        }

        return response()->json([
            'message' => 'Participantes agregados correctamente.',
        ]);
    }


    // Quitar un participante del proyecto
    public function quitarParticipante(Proyecto $proyecto, $participanteId)
    {
        // Buscar al participante en el proyecto
        $participante = $proyecto->participantes()->find($participanteId);

        // Verificar si el participante existe
        if (!$participante) {
            return response()->json([
                'message' => 'El participante no pertenece a este proyecto.',
            ], 404);
        }

        // Eliminar al participante
        $participante->update(['is_active' => false]);

        return response()->json([
            'message' => 'Participante eliminado exitosamente.',
        ]);
    }


}
