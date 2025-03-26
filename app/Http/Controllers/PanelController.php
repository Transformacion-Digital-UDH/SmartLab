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
use Illuminate\Support\Facades\Auth;

class PanelController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Variable que determina si se debe aplicar el filtro por laboratorio
        $shouldFilter = !($user->rol === 'Admin' && $user->laboratorio_seleccionado === null);
        // Si se aplica el filtro, obtenemos el ID del laboratorio
        $labId = $shouldFilter ? $user->laboratorio_seleccionado : null;

        // Obtener usuarios de solicitud: estado "En revisión" y rol "Invitado"
        $usuarios_solicitud = User::where('estado_cuenta', 'En revisión')
            ->where('rol', 'Invitado')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Reservas: se aplican los filtros de laboratorio si corresponde
        $reservasQuery = Reserva::with(['usuario', 'equipo', 'recurso', 'area'])
            ->where('estado', 'Por aprobar');

        if ($shouldFilter) {
            $reservasQuery->where(function ($query) use ($labId) {
                $query->whereHas('equipo', function($q) use ($labId) {
                    $q->whereHas('area', function($q) use ($labId) {
                        $q->where('laboratorio_id', $labId);
                    });
                })
                ->orWhereHas('recurso', function($q) use ($labId) {
                    $q->whereHas('area', function($q) use ($labId) {
                        $q->where('laboratorio_id', $labId);
                    });
                })
                ->orWhereHas('area', function($q) use ($labId) {
                    $q->where('laboratorio_id', $labId);
                });
            });
        }
        $reservas = $reservasQuery->orderBy('hora_inicio')->get();

        // Equipos: filtrar por laboratorio a través del área si corresponde
        $equiposQuery = Equipo::where('is_active', true);
        if ($shouldFilter) {
            $equiposQuery->whereHas('area', function($q) use ($labId) {
                $q->where('laboratorio_id', $labId);
            });
        }
        $equipos = $equiposQuery->get();

        // Recursos: filtrar por laboratorio a través del área si corresponde
        $recursosQuery = Recurso::where('is_active', true);
        if ($shouldFilter) {
            $recursosQuery->whereHas('area', function($q) use ($labId) {
                $q->where('laboratorio_id', $labId);
            });
        }
        $recursos = $recursosQuery->get();

        // Áreas: filtrar directamente por laboratorio si corresponde
        $areasQuery = Area::where('is_active', true);
        if ($shouldFilter) {
            $areasQuery->where('laboratorio_id', $labId);
        }
        $areas = $areasQuery->get();

        $laboratorios = Laboratorio::whereNotNull('google_calendar_id')->get();

        // Métricas para el dashboard

        // Cantidad de usuarios (sin Admin)
        $usuariosCount = User::where('is_active', true)
            ->where('rol', '!=', 'Admin')
            ->count();

        // Proyectos: se aplica filtro por laboratorio si corresponde
        $proyectosCount = Proyecto::where('is_active', true)
            ->where('estado', 'En proceso')
            ->when($shouldFilter, function($query) use ($labId) {
                $query->where('laboratorio_id', $labId);
            })
            ->count();

        // Asistencias de hoy: se filtra por laboratorio si corresponde
        $asistenciasCount = Asistencia::whereDate('created_at', Carbon::today())
            ->where('is_active', true)
            ->when($shouldFilter, function($query) use ($labId) {
                $query->where('laboratorio_id', $labId);
            })
            ->count();

        // Reservas de hoy: se filtran de la misma forma que las reservas generales
        $reservasHoyCount = Reserva::whereDate('hora_inicio', Carbon::today())
            ->where('is_active', true)
            ->where('estado', 'Aprobada')
            ->when($shouldFilter, function($query) use ($labId) {
                $query->where(function($q) use ($labId) {
                    $q->whereHas('equipo', function($q) use ($labId) {
                        $q->whereHas('area', function($q) use ($labId) {
                            $q->where('laboratorio_id', $labId);
                        });
                    })
                    ->orWhereHas('recurso', function($q) use ($labId) {
                        $q->whereHas('area', function($q) use ($labId) {
                            $q->where('laboratorio_id', $labId);
                        });
                    })
                    ->orWhereHas('area', function($q) use ($labId) {
                        $q->where('laboratorio_id', $labId);
                    });
                });
            })
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
                ->when($shouldFilter, function($query) use ($labId) {
                    $query->where('laboratorio_id', $labId);
                })
                ->count();

            $asistenciasMensuales[] = $conteo;
        }

        return Inertia::render('Panel/Index', [
            'usuarios_solicitud' => $usuarios_solicitud,
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
