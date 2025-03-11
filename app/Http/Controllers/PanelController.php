<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Equipo;
use App\Models\Recurso;
use App\Models\Area;
use App\Models\User;
use App\Models\Proyecto;
use App\Models\Asistencia;
use App\Models\Laboratorio;
use Inertia\Inertia;
use Carbon\Carbon;

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

        // Calcular métricas para el dashboard
        $usuariosCount = User::where('is_active', true)
                           ->where('rol', '!=', 'Admin')
                           ->count();
        
        $proyectosCount = Proyecto::where('is_active', 1)
                                 ->where('estado', 'En proceso')
                                 ->count();
        
        $asistenciasCount = Asistencia::whereDate('created_at', Carbon::today())
                                    ->count();
        
        $reservasHoyCount = Reserva::whereDate('hora_inicio', Carbon::today())
                                 ->where('is_active', true)
                                 ->where('estado', 'Aprobada')
                                 ->count();

        return Inertia::render('Panel/Index', [
            'reservas' => $reservas,
            'equipos' => $equipos,
            'recursos' => $recursos,
            'areas' => $areas,
            'laboratorios' => $laboratorios,
            'metricas' => [
                'usuarios' => $usuariosCount,
                'proyectos' => $proyectosCount,
                'asistencias' => $asistenciasCount,
                'reservas' => $reservasHoyCount
            ]
        ]);
    }
}
