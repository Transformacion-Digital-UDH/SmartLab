<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReservaController extends Controller
{
	public function create(Request $request){
		$validatedData = $request->validate([
			'hora_inicio' => 'required|date_format:Y-m-d H:i:s|before:hora_fin',
			'hora_fin' => 'required|date_format:Y-m-d H:i:s|after:hora_inicio',
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
}
