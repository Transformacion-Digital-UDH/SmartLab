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

        // Regla pública: Compartir públicamente (lectura)
        $aclScopePublic = new Google_Service_Calendar_AclRuleScope();
        $aclScopePublic->setType('default');
        $publicRule = new Google_Service_Calendar_AclRule();
        $publicRule->setScope($aclScopePublic);
        $publicRule->setRole('reader');
        $service->acl->insert($createdCalendar->getId(), $publicRule);

        // Regla para la cuenta de la app: asignar propiedad a "transformaciondigital@udh.edu.pe"
        $aclScopeApp = new Google_Service_Calendar_AclRuleScope();
        $aclScopeApp->setType('user');
        $aclScopeApp->setValue('transformaciondigital@udh.edu.pe');
        $appRule = new Google_Service_Calendar_AclRule();
        $appRule->setScope($aclScopeApp);
        $appRule->setRole('owner');
        $service->acl->insert($createdCalendar->getId(), $appRule);

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


    public function setUserCredentials($tokenData)
    {
        // Si el token viene como string, lo decodificamos a un arreglo
        if (is_string($tokenData)) {
            $tokenData = json_decode($tokenData, true);
        }
        // Verificamos que el token tenga el formato correcto
        if (!isset($tokenData['access_token'])) {
            throw new \InvalidArgumentException("Invalid token format");
        }

        $this->client->setAccessToken($tokenData);

        // Refrescar el token si es necesario
        if ($this->client->isAccessTokenExpired()) {
            $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            $this->client->setAccessToken($newToken);
            // Aquí puedes actualizar la base de datos con el nuevo token si es necesario
        }
    }


    public function getAccessToken()
    {
        return $this->client->getAccessToken();
    }

}
