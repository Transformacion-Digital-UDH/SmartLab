<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Calendar;
use Google_Service_Calendar_AclRule;
use Google_Service_Calendar_AclRuleScope;
use Google_Service_Calendar_Event;


use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Google_Client();
        // Apunta al archivo JSON de la cuenta de servicio
        $this->client->setAuthConfig(storage_path('app/google/credentials.json'));
        // Permisos necesarios para manejar calendarios y eventos
        $this->client->addScope(Google_Service_Calendar::CALENDAR);
        $this->client->addScope(Google_Service_Calendar::CALENDAR_EVENTS);
    }

    public function createCalendar($nombreLaboratorio)
    {
        $service = new Google_Service_Calendar($this->client);

        $calendar = new Google_Service_Calendar_Calendar();
        $calendar->setSummary($nombreLaboratorio);
        $calendar->setTimeZone('America/Lima');

        $createdCalendar = $service->calendars->insert($calendar);

        // Crear y configurar el objeto AclRuleScope
        $aclScope = new \Google_Service_Calendar_AclRuleScope();
        $aclScope->setType('user');
        $aclScope->setValue('transformaciondigital@udh.edu.pe');

        // Crear la regla ACL y asignar el scope
        $rule = new \Google_Service_Calendar_AclRule();
        $rule->setScope($aclScope);
        $rule->setRole('owner'); // O 'writer' si prefieres

        $service->acl->insert($createdCalendar->getId(), $rule);

        return $createdCalendar->getId();
    }



    // Ejemplo: Crear un evento en un calendario
    public function createEvent($calendarId, $eventData)
    {
        $service = new Google_Service_Calendar($this->client);

        $event = new \Google_Service_Calendar_Event($eventData);
        $createdEvent = $service->events->insert($calendarId, $event);

        return $createdEvent;
    }
}
