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

    // Aprobar una reserva
    public function aprobar(Reserva $reserva)
    {
        // Aprobar la reserva y guardarla
        $reserva->estado = 'Aprobada';
        $reserva->save();

        // Instanciar el servicio de Google Calendar (con credenciales de la cuenta de servicio)
        $googleCalendarService = new \App\Services\GoogleCalendarService();

        // Determinar el laboratorio correspondiente a la reserva
        $laboratorio = null;
        if ($reserva->equipo && $reserva->equipo->area) {
            $laboratorio = $reserva->equipo->area->laboratorio;
        } elseif ($reserva->recurso && $reserva->recurso->area) {
            $laboratorio = $reserva->recurso->area->laboratorio;
        } elseif ($reserva->area) {
            $laboratorio = $reserva->area->laboratorio;
        }

        // Preparar los datos del evento (los mismos para ambos calendarios)
        $eventData = [
            'summary' => "Reserva aprobada: " . (
                $reserva->equipo
                    ? $reserva->equipo->nombre
                    : ($reserva->recurso
                        ? $reserva->recurso->nombre
                        : ($reserva->area->nombre ?? 'Sin especificar'))
            ),
            'description' => 'Reserva aprobada por ' . $reserva->usuario->nombres,
            'start' => [
                'dateTime' => \Carbon\Carbon::parse($reserva->hora_inicio)->toAtomString(),
                'timeZone' => 'America/Lima',
            ],
            'end' => [
                'dateTime' => \Carbon\Carbon::parse($reserva->hora_fin)->toAtomString(),
                'timeZone' => 'America/Lima',
            ],
        ];

        // 1. Crear el evento en el calendario del laboratorio (cuenta de servicio)
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
                // Opcional: almacenar el ID del evento en la reserva
                $reserva->google_event_id = $createdLabEvent->getId();
                $reserva->save();
            } catch (\Exception $e) {
                Log::error("Error al crear el evento en el calendario del laboratorio: " . $e->getMessage());
            }
        }

        // 2. Crear el evento en el calendario predeterminado del usuario autenticado (cuenta personal)
        $usuario = $reserva->usuario;
        if ($usuario) {
            // Obtener el token completo del usuario desde la BD (google_token_json)
            $tokenData = $usuario->google_token_json;
            if (is_string($tokenData)) {
                $tokenData = json_decode($tokenData, true);
            }

            // Crear un nuevo cliente para el usuario
            $userClient = new \Google_Client();
            $userClient->setAccessToken($tokenData);
            $userClient->addScope(\Google_Service_Calendar::CALENDAR);
            $userClient->addScope(\Google_Service_Calendar::CALENDAR_EVENTS);

            // Crear un servicio de calendario con el cliente del usuario
            $userCalendarService = new \Google_Service_Calendar($userClient);

            try {
                // Al usar "primary", se crea el evento en el calendario predeterminado del usuario
                $event = new \Google_Service_Calendar_Event($eventData);
                $createdUserEvent = $userCalendarService->events->insert('primary', $event);
                \Log::debug('Evento creado en el calendario del usuario:', (array) $createdUserEvent);
                // Opcional: guardar el ID del evento en la reserva u otro registro
            } catch (\Exception $e) {
                \Log::error("Error al crear el evento en el calendario del usuario: " . $e->getMessage());
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
