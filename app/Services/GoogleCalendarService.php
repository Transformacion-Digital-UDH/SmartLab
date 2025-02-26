<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class GoogleCalendarService
{
    protected $client;
    protected $service;
    protected $calendarId;

    public function __construct()
    {
        // Configura el cliente de Google con la cuenta de servicio
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('app/google-credentials.json'));
        $this->client->addScope(Google_Service_Calendar::CALENDAR);

        // Crea la instancia de la API de Calendar
        $this->service = new Google_Service_Calendar($this->client);

        // El ID del calendario donde quieres crear los eventos
        $this->calendarId = 'tu_calendar_id@group.calendar.google.com';
    }

    public function createEvent($reserva)
    {
        $event = new Google_Service_Calendar_Event([
            'summary'     => "Reserva #{$reserva->id}",
            'description' => "Reserva creada por el usuario #{$reserva->usuario_id}",
            'start'       => [
                'dateTime' => $reserva->hora_inicio,
                'timeZone' => 'America/Lima',
            ],
            'end'         => [
                'dateTime' => $reserva->hora_fin,
                'timeZone' => 'America/Lima',
            ],
            // Si quieres invitar a un usuario, usa 'attendees':
            // 'attendees' => [
            //     ['email' => 'usuario@example.com']
            // ],
        ]);

        // Inserta el evento en tu calendario
        $createdEvent = $this->service->events->insert($this->calendarId, $event);

        // Retorna el ID del evento en Google Calendar
        return $createdEvent->getId();
    }
}
