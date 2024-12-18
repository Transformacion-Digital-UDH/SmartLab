<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Recurso;
use App\Models\Reserva;
use App\Models\User;
use Inertia\Inertia;

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

        return Inertia::render('Reservas/Index', [
            'reservas' => $reservas,
            'usuarios' => $usuarios,
            'equipos' => $equipos,
            'recursos' => $recursos,
        ]);
    }

    // Crear una reserva
    public function store(Request $request)
    {
        $request->validate($this->rules);
        Reserva::create($request->all());
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
