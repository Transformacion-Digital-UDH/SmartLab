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
        // Obtener reservas pendientes por aprobar
        $reservas = Reserva::with(['usuario', 'equipo', 'recurso', 'area'])
            ->where('estado', 'Por aprobar')
            ->orderBy('hora_inicio')
            ->get();

        // Datos para formularios y modales
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
        
        // Cambiar equipos por reservas de hoy
        $reservasHoyCount = Reserva::whereDate('hora_inicio', Carbon::today())
                                 ->where('is_active', true)
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
                'equipos' => $reservasHoyCount // Mantenemos la clave "equipos" para evitar cambios en el frontend
            ]
        ]);
    }
}
