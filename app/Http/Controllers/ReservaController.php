<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{
	public function create(Request $request){
		$data = $request->all();

		$hora_inicio = $data['hora_inicio'];
		$hora_fin = $data['hora_fin'];
		$equipo_id = $data['equipo_id'];
		$recurso_id = $data['recurso_id'];

		Reserva::create([
			'hora_inicio' => $hora_inicio,
			'hora_fin' => $hora_fin,
			'usuario_id' => Auth::id(),
			'equipo_id' => $equipo_id,
			'recurso_id' => $recurso_id
		]);
	}
}
