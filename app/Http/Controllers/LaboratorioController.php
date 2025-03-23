<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Laboratorio;
use App\Models\User;
use App\Models\Area;

use Illuminate\Support\Facades\Log;
use App\Services\GoogleCalendarService;

class LaboratorioController extends Controller
{
    public $rules = [
        'nombre' => ['required', 'string', 'min:5', 'max:90'],
        'codigo' => ['required', 'string', 'min:5', 'max:10'],
        'descripcion' => ['nullable', 'string', 'min:5', 'max:100'],
        'aforo' => ['nullable', 'integer', 'min:1', 'max:100'],
        'email' => ['nullable', 'email', 'min:5', 'max:100'],
        'inauguracion' => ['nullable', 'date'],
        'responsable_id' => ['required', 'integer', 'exists:users,id'],
        'coordinador_id' => ['nullable', 'integer', 'exists:users,id'],
    ];

    // Listar los laboratorios en la vista
    public function index()
    {
        $laboratorios = Laboratorio::where('is_active', true)
            ->orderBy('id', 'desc')
            ->get();

        $usuarios = User::where('is_active', true)
            ->get();

        return Inertia::render('Laboratorios/Index', [
            'laboratorios' => $laboratorios,
            'usuarios' => $usuarios,
        ]);
    }

    // Crear el laboratorio
    public function store(Request $request, GoogleCalendarService $googleCalendarService)
    {
        $request->validate($this->rules);

        // Crear el laboratorio en la BD
        $laboratorio = Laboratorio::create($request->all());

        // Asociar usuarios con roles
        $laboratorio->participantes()->attach($request->responsable_id, [
            'rol' => 'Responsable',
            'is_active' => true
        ]);

        $laboratorio->participantes()->attach($request->coordinador_id, [
            'rol' => 'Coordinador',
            'is_active' => true
        ]);

        try {
            $calendarId = $googleCalendarService->createCalendar($laboratorio->nombre);
            $laboratorio->update(['google_calendar_id' => $calendarId]);
        } catch (\Exception $e) {
            Log::error("Error al crear calendario: " . $e->getMessage());
            return response()->json([
                'error' => 'No se pudo crear el calendario en Google: ' . $e->getMessage()
            ], 500);
        }

        Area::create([
            'nombre' => 'General',
            'descripcion' => 'Área general del laboratorio',
            'laboratorio_id' => $laboratorio->id,
            'is_active' => true,
            'tipo' => 'No reservable'
        ]);

        return response()->json([
            'message' => 'Laboratorio y calendario creados correctamente',
            'laboratorio' => $laboratorio
        ]);
    }

    // Actualizar laboratorio
    public function update(Request $request, Laboratorio $laboratorio)
    {
        $request->validate($this->rules);

        // Verificar si el responsable ha cambiado
        if ($request->responsable_id != $laboratorio->responsable_id) {
            // Eliminar todos los responsables actuales del laboratorio
            $laboratorio->participantes()
                ->wherePivot('rol', 'Responsable')
                ->detach();

            // Asignar el nuevo responsable
            $laboratorio->participantes()->attach($request->responsable_id, [
                'rol' => 'Responsable',
                'is_active' => true
            ]);
        }

        // Verificar si el coordinador ha cambiado
        if ($request->coordinador_id != $laboratorio->coordinador_id) {
            // Eliminar todos los coordinadores actuales del laboratorio
            $laboratorio->participantes()
                ->wherePivot('rol', 'Coordinador')
                ->detach();

            // Asignar el nuevo coordinador
            $laboratorio->participantes()->attach($request->coordinador_id, [
                'rol' => 'Coordinador',
                'is_active' => true
            ]);
        }

        // Actualizar los demás campos del laboratorio
        $laboratorio->update($request->all());

        return response()->json([
            'message' => 'Laboratorio actualizado correctamente',
            'laboratorio' => $laboratorio
        ]);
    }

    // Eliminar laboratorio
    public function destroy(Laboratorio $laboratorio)
    {
        $laboratorio->is_active = false;
        $laboratorio->save();
    }

    // API
    public function info()
    {
        $laboratorios = Laboratorio::select('id', 'nombre')->where('is_active', 1)->get();
        return [
            'data' => $laboratorios
        ];
    }
    public function validar_lab(Request $request)
    {
        $id = $request->input('id');
        $codigo = $request->input('codigo');

        $lab = Laboratorio::find($id);
        if ($lab->codigo == $codigo) {
            return response(null);
        }
        return response('Codigo Invalido', 401);
    }

    // Obtener los miembros de un laboratorio
    public function obtenerMiembros(Laboratorio $laboratorio)
    {
        return $laboratorio->miembros;
    }
}
