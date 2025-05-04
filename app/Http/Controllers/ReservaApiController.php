<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Carbon\Carbon;

class ReservaApiController extends Controller
{
<<<<<<< HEAD
=======

>>>>>>> dcbf80d3a65200e8ac9d17a43dba829de91c12ab
    public function index($tipo, $id)
    {
        $query = Reserva::where('is_active', true)
            ->where('estado', 'Aprobada')
            ->whereDate('hora_inicio', '>=', now()->toDateString()) 
            ->orderBy('hora_inicio', 'asc');

        if ($tipo === 'equipo') {
            $query->where('equipo_id', $id);
        } elseif ($tipo === 'recurso') {
            $query->where('recurso_id', $id);
        } elseif ($tipo === 'area') {
            $query->where('area_id', $id);
        }

        return response()->json([
            'reservas' => $query->get()
        ]);
    }

}
