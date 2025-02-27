<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Equipo;
use App\Models\Recurso;
use App\Models\Area;
use App\Models\Laboratorio;
use Inertia\Inertia;

class PanelController extends Controller
{
    public function index()
    {
        $reservas = Reserva::with(['usuario', 'equipo', 'recurso', 'area'])
            ->where('estado', 'Por aprobar')
            ->orderBy('hora_inicio')
            ->get();

        $equipos = Equipo::all();
        $recursos = Recurso::all();
        $areas = Area::all();
        $laboratorios = Laboratorio::whereNotNull('google_calendar_id')->get();

        return Inertia::render('Panel/Index', [
            'reservas' => $reservas,
            'equipos' => $equipos,
            'recursos' => $recursos,
            'areas' => $areas,
            'laboratorios' => $laboratorios,
        ]);
    }
}
