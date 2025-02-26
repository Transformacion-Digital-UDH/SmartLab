<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Equipo;
use App\Models\Recurso;
use App\Models\Reserva;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    protected $rules = [
        'hora_inicio' => 'required|date',
        'hora_fin' => 'required|date|after:hora_inicio',
        'estado' => 'required|in:Por aprobar,Aprobada,No aprobada,Finalizada',
        'usuario_id' => 'required|exists:users,id',
        'equipo_id' => 'nullable|exists:equipos,id',
        'recurso_id' => 'nullable|exists:recursos,id',
        'area_id' => 'nullable|exists:areas,id',
    ];

    // Listar las reservas en la vista
    public function index()
    {
        $reservas = Reserva::with(['usuario', 'equipo', 'recurso'])
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->get();

        $usuarios = User::all();
        $equipos = Equipo::all();
        $recursos = Recurso::all();
        $areas = Area::all();

        return Inertia::render('Reservas/Index', [
            'reservas' => $reservas,
            'usuarios' => $usuarios,
            'equipos' => $equipos,
            'recursos' => $recursos,
            'areas' => $areas,
        ]);
    }

    // Crear una reserva
    public function store(Request $request){
        $validatedData = $request->validate([
            'hora_inicio' => 'required|date_format:Y-m-d\TH:i|before:hora_fin',
            'hora_fin' => 'required|date_format:Y-m-d\TH:i|after:hora_inicio',
            'recurso_id' => 'nullable|integer|exists:recursos,id',
            'equipo_id' => 'nullable|integer|exists:equipos,id',
        ]);

        Reserva::create([
            'hora_inicio' => $validatedData['hora_inicio'],
            'hora_fin' => $validatedData['hora_fin'],
            'usuario_id' => Auth::id(),
            'equipo_id' => $validatedData['equipo_id'],
            'recurso_id' => $validatedData['recurso_id'],
        ]);

        return response()->noContent(201);
    }

    // Actualizar una reserva
    public function update(Request $request, Reserva $reserva)
    {
        $request->validate($this->rules);
        $reserva->update($request->all());
    }

    // Eliminar una reserva
    public function destroy(Reserva $reserva)
    {
        $reserva->is_active = false;
        $reserva->save();
    }
}
