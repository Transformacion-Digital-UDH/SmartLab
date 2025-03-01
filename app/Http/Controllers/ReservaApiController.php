<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Carbon\Carbon;

class ReservaApiController extends Controller
{
    
    public function index($tipo, $id)
    {
        $reservas = [];
        if ($tipo == 'equipo') {
            $reservas = Reserva::where('equipo_id', $id)->orderBy('hora_inicio', 'asc')->get();
        } else if ($tipo == 'recurso') {
            $reservas = Reserva::where('recurso_id', $id)->orderBy('hora_inicio', 'asc')->get();
        }

        $filteredReservas = $reservas->filter(function ($reserva) {
            return now()->timestamp < strtotime($reserva->hora_fin);
        });

        return response()->json([
            'reservas' => $filteredReservas -> values()
        ]);
    }
}
