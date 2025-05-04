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
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;
use App\Services\GoogleCalendarService;

class ReservaController extends Controller
{
    protected $rules = [
        'hora_inicio' => 'required|date',
        'hora_fin'    => 'required|date|after:hora_inicio',
        'estado'      => 'nullable|in:Por aprobar,Aprobada,No aprobada,Cancelada',
        'usuario_id'  => 'nullable|exists:users,id',
        'equipo_id'   => 'nullable|exists:equipos,id',
        'recurso_id'  => 'nullable|exists:recursos,id',
        'area_id'     => 'nullable|exists:areas,id',
    ];

    /**
     * Función para validar que no exista una reserva solapada en el mismo día
     * para el mismo recurso, equipo o área.
     *
     * Parámetros:
     * - $nuevaReserva: array con los datos de la reserva que se intenta crear o actualizar.
     * - $excluirReservaId: (opcional) ID de la reserva a excluir de la validación (útil en actualizaciones).
     *
     * La condición de solapamiento es:
     *   - La reserva existente debe iniciar antes de que termine la nueva reserva.
     *   - Y debe terminar después de que inicie la nueva reserva.
     *
     * @param array $nuevaReserva
     * @param mixed $excluirReservaId
     * @return bool
     */
    private function validarDisponibilidad(array $nuevaReserva, $excluirReservaId = null)
    {
        $fechaReserva = Carbon::parse($nuevaReserva['hora_inicio'])->toDateString();

        $query = Reserva::where('is_active', true)
            ->whereDate('hora_inicio', $fechaReserva)
            ->where('hora_inicio', '<', $nuevaReserva['hora_fin'])
            ->where('hora_fin', '>', $nuevaReserva['hora_inicio'])
            ->where(function ($q) use ($nuevaReserva) {
                $q->when(!empty($nuevaReserva['equipo_id']), function ($q) use ($nuevaReserva) {
                    $q->orWhere('equipo_id', $nuevaReserva['equipo_id']);
                })
                ->when(!empty($nuevaReserva['recurso_id']), function ($q) use ($nuevaReserva) {
                    $q->orWhere('recurso_id', $nuevaReserva['recurso_id']);
                })
                ->when(!empty($nuevaReserva['area_id']), function ($q) use ($nuevaReserva) {
                    $q->orWhere('area_id', $nuevaReserva['area_id']);
                });
            });

        if ($excluirReservaId) {
            $query->where('id', '!=', $excluirReservaId);
        }

        return $query->exists();
    }

    // Listar las reservas en la vista
    public function index()
    {
        $user = Auth::user();

        // Query base para reservas
        $reservasQuery = Reserva::with(['usuario', 'equipo', 'recurso', 'area'])
            ->where('is_active', true);

        // Queries para modelos relacionados (agregando el filtro de tipo "Reservable")
        $usuariosQuery = User::where('is_active', true);
        $equiposQuery = Equipo::where('is_active', true)->where('tipo', 'Reservable');
        $recursosQuery = Recurso::where('is_active', true)->where('tipo', 'Reservable');
        $areasQuery = Area::where('is_active', true)->where('tipo', 'Reservable');

        // Aplicar filtro de laboratorio solo si no es Admin o si tiene lab seleccionado
        if (!($user->rol === 'Admin' && $user->laboratorio_seleccionado === null)) {
            // Filtro para reservas
            $reservasQuery->where(function($query) use ($user) {
                $query->whereHas('equipo.area', fn($q) => $q->where('laboratorio_id', $user->laboratorio_seleccionado))
                      ->orWhereHas('recurso.area', fn($q) => $q->where('laboratorio_id', $user->laboratorio_seleccionado))
                      ->orWhereHas('area', fn($q) => $q->where('laboratorio_id', $user->laboratorio_seleccionado));
            });

            // Filtros para equipos, recursos y áreas
            $equiposQuery->whereHas('area', fn($q) => $q->where('laboratorio_id', $user->laboratorio_seleccionado));
            $recursosQuery->whereHas('area', fn($q) => $q->where('laboratorio_id', $user->laboratorio_seleccionado));
            $areasQuery->where('laboratorio_id', $user->laboratorio_seleccionado);
        }

        // Ejecutar consultas
        $reservas = $reservasQuery->orderBy('id', 'desc')->get();
        $usuarios = $usuariosQuery->get();
        $equipos = $equiposQuery->get();
        $recursos = $recursosQuery->get();
        $areas = $areasQuery->get();

        return Inertia::render('Reservas/Index', [
            'reservas' => $reservas,
            'usuarios' => $usuarios,
            'equipos' => $equipos,
            'recursos' => $recursos,
            'areas' => $areas,
        ]);
    }


    // Crear una reserva
    public function store(Request $request)
    {
        $validatedData = $request->validate($this->rules);
        $validatedData['usuario_id'] = Auth::id();

        // Validar disponibilidad: si ya existe una reserva solapada, se retorna un error.
        if ($this->validarDisponibilidad($validatedData)) {
            return response()->json(['error' => 'Reserva solapada.'], 422);
        }

        $reserva = Reserva::create($validatedData);

        // Si la reserva se crea con estado "Aprobada", se genera el evento en Google Calendar.
        if ($reserva->estado === 'Aprobada') {
            $this->aprobar($reserva);
        }

        return response()->noContent(201);
    }

    // Actualizar una reserva
    public function update(Request $request, Reserva $reserva)
    {
        $validatedData = $request->validate($this->rules);

        // Validar disponibilidad, excluyendo la reserva actual para no considerarla en conflicto.
        if ($this->validarDisponibilidad($validatedData, $reserva->id)) {
            return response()->json(['error' => 'Reserva solapada.'], 422);
        }

        $oldEstado = $reserva->estado;
        $reserva->update($validatedData);

        // Si el estado cambia a "Aprobada", se aprueba la reserva y se crea el evento en Google Calendar.
        if ($reserva->estado === 'Aprobada' && $oldEstado !== 'Aprobada') {
            $this->aprobar($reserva);
        }
    }

    // Eliminar una reserva
    public function destroy(Reserva $reserva)
    {
        $reserva->is_active = false;
        $reserva->save();
    }

    // Aprobar una reserva
    public function aprobar(Reserva $reserva)
    {
        // Validar disponibilidad antes de aprobar, excluyendo la reserva actual.
        if ($this->validarDisponibilidad($reserva->toArray(), $reserva->id)) {
            return response()->json(['error' => 'Reserva solapada.'], 422);
        }

        // Aprobar la reserva y guardarla.
        $reserva->estado = 'Aprobada';
        $reserva->save();

        // Instanciar el servicio de Google Calendar.
        $googleCalendarService = new GoogleCalendarService();

        // Determinar el laboratorio correspondiente a la reserva.
        $laboratorio = null;
        if ($reserva->equipo && $reserva->equipo->area) {
            $laboratorio = $reserva->equipo->area->laboratorio;
        } elseif ($reserva->recurso && $reserva->recurso->area) {
            $laboratorio = $reserva->recurso->area->laboratorio;
        } elseif ($reserva->area) {
            $laboratorio = $reserva->area->laboratorio;
        }

        // Preparar los datos del evento para Google Calendar.
        $eventData = [
            'summary' => "Reserva UDH: " . (
                $reserva->equipo
                    ? $reserva->equipo->nombre
                    : ($reserva->recurso
                        ? $reserva->recurso->nombre
                        : ($reserva->area->nombre ?? 'Sin especificar'))
            ),
            'description' => 'Reserva aprobada por ' . $reserva->usuario->nombres,
            'start' => [
                'dateTime' => Carbon::parse($reserva->hora_inicio)->toAtomString(),
                'timeZone' => 'America/Lima',
            ],
            'end' => [
                'dateTime' => Carbon::parse($reserva->hora_fin)->toAtomString(),
                'timeZone' => 'America/Lima',
            ],
        ];

        // 1. Crear el evento en el calendario del laboratorio.
        if ($laboratorio) {
            if (!$laboratorio->google_calendar_id) {
                $calendarIdLab = $googleCalendarService->createCalendar($laboratorio->nombre);
                $laboratorio->google_calendar_id = $calendarIdLab;
                $laboratorio->save();
            } else {
                $calendarIdLab = $laboratorio->google_calendar_id;
            }

            try {
                $createdLabEvent = $googleCalendarService->createEvent($calendarIdLab, $eventData);
                // Opcional: almacenar el ID del evento en la reserva.
                $reserva->google_event_id = $createdLabEvent->getId();
                $reserva->save();
            } catch (Exception $e) {
                Log::error("Error al crear el evento en el calendario del laboratorio: " . $e->getMessage());
            }
        }

        // 2. Crear el evento en el calendario predeterminado del usuario autenticado.
        $usuario = $reserva->usuario;
        if ($usuario) {
            $tokenData = $usuario->google_token_json;
            if (is_string($tokenData)) {
                $tokenData = json_decode($tokenData, true);
            }

            $userClient = new Google_Client();
            $userClient->setAccessToken($tokenData);
            $userClient->addScope(Google_Service_Calendar::CALENDAR);
            $userClient->addScope(Google_Service_Calendar::CALENDAR_EVENTS);

            $userCalendarService = new Google_Service_Calendar($userClient);

            try {
                $event = new Google_Service_Calendar_Event($eventData);
                $createdUserEvent = $userCalendarService->events->insert('primary', $event);
                Log::debug('Evento creado en el calendario del usuario:', (array) $createdUserEvent);
            } catch (Exception $e) {
                Log::error("Error al crear el evento en el calendario del usuario: " . $e->getMessage());
            }
        }
    }

    // Desaprobar una reserva
    public function desaprobar(Reserva $reserva)
    {
        $reserva->estado = 'No aprobada';
        $reserva->save();
    }
}
