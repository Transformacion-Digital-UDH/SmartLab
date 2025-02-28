<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Equipo;
use App\Models\Laboratorio;
use App\Models\Recurso;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogoController extends Controller
{
    public function index()
	{
		$recursos = Recurso::with('area', 'area.laboratorio', 'equipo', 'fotos')
			->where('is_active', true)
			->where('tipo', 'Reservable')
			->orderBy('id', 'desc')
			->get();

		$equipos = Equipo::where('is_active', true)
			->where('tipo', 'Reservable')
			->orderBy('id', 'desc')
			->get();

		$areas = Area::where('is_active', true)
			->where('tipo', 'Reservable')
			->orderBy('id', 'desc')
			->get();

		$laboratorios = Laboratorio::where('is_active', true)->get();

		$admin = User::where('rol', '=', 'Admin')->first();

		return Inertia::render('Catalogo/Index', [
			'recursos' => $recursos,
			'areas' => $areas,
			'equipos' => $equipos,
			'laboratorios' => $laboratorios,
			'adminNumber' => $admin->celular ?? 'No disponible'
		]);
	}

	// Guardar un nuevo recurso
	public function store(Request $request)
	{
		$request->validate([
			'nombre' => 'required|string|max:100',
			'codigo' => 'nullable|string|max:20',
			'tipo' => 'required|in:Reservable,No reservable,Suministro',
			'descripcion' => 'nullable|string',
			'estado' => 'required|in:Activo,Inactivo,Reservado,Prestado',
			'is_active' => 'boolean',
			'area_id' => 'nullable|exists:areas,id',
			'equipo_id' => 'nullable|exists:equipos,id',
		]);

		Recurso::create($request->all());
	}

	// Actualizar un recurso existente
	public function update(Request $request, Recurso $recurso)
	{
		$request->validate([
			'nombre' => 'required|string|max:100',
			'codigo' => 'nullable|string|max:20',
			'tipo' => 'required|in:Reservable,No reservable,Suministro',
			'descripcion' => 'nullable|string',
			'estado' => 'required|in:Activo,Inactivo,Reservado,Prestado',
			'is_active' => 'boolean',
			'area_id' => 'nullable|exists:areas,id',
			'equipo_id' => 'nullable|exists:equipos,id',
		]);

		$recurso->update($request->all());
	}

	public function listaDeReservados ($id, $tipo) {
		$reservas = [];
		if ($tipo == 'recurso') {
			$reservas = Reserva::where('equipo_id', $id)->get();
		} else if ($tipo == 'equipo') {
			$reservas = Reserva::where('recurso_id', $id)->get();
		}

		return response()->json([
			'data' => $reservas
		]);
	}

	// Eliminar un recurso
	public function destroy(Recurso $recurso)
	{
		$recurso->update(['is_active' => false]);
	}
}
