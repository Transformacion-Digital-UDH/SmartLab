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
        $usuarios = User::where('is_active', false)
                        ->get();
                        
        $reservas = Reserva::with(['usuario', 'equipo', 'recurso', 'area'])
            ->where('estado', 'Por aprobar')
            ->orderBy('hora_inicio')
            ->get();

        $equipos = Equipo::where('is_active', true)->get();
        $recursos = Recurso::where('is_active', true)->get();
        $areas = Area::where('is_active', true)->get();

        $laboratorios = Laboratorio::whereNotNull('google_calendar_id')->get();

        // Calcular métricas para el dashboard
        $usuariosCount = User::where('is_active', true)
                            ->where('rol', '!=', 'Admin')
                            ->count();

        $proyectosCount = Proyecto::where('is_active', true)
                            ->where('estado', 'En proceso')
                            ->count();

        $asistenciasCount = Asistencia::whereDate('created_at', Carbon::today())
                            ->where('is_active', true)
                            ->count();

        // Cambiar equipos por reservas de hoy
        $reservasHoyCount = Reserva::whereDate('hora_inicio', Carbon::today())
                                 ->where('is_active', true)
                                 ->where('estado', 'Aprobada')
                                 ->count();

        // Calcular asistencias mensuales para la gráfica (últimos 6 meses)
        $asistenciasMensuales = [];
        $etiquetasMeses = [];

        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $nombreMes = $mes->translatedFormat('F');
            $etiquetasMeses[] = $nombreMes;

            $conteo = Asistencia::whereYear('hora_entrada', $mes->year)
                              ->whereMonth('hora_entrada', $mes->month)
                              ->count();

            $asistenciasMensuales[] = $conteo;
        }

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
            ],
            'asistenciasMensuales' => $asistenciasMensuales,
            'etiquetasMeses' => $etiquetasMeses,
        ]);
    }
}
