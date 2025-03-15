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
    public function index(Request $request)
    {
        $query = $request->input('q');
        $laboratorioId = $request->input('laboratorio_id');

        $laboratorios = Laboratorio::where('is_active', true)->get();

        $areas = Area::with('laboratorio')
            ->where('tipo', 'Reservable')
            ->where('is_active', true)
            ->when($laboratorioId !== null, function ($q) use ($laboratorioId) {
                $q->where('laboratorio_id', $laboratorioId);
            })
            ->get();

        $recursos = Recurso::with('area', 'area.laboratorio', 'fotos')
            ->where('is_active', true)
            ->where('tipo', 'Reservable')
            ->when($laboratorioId !== null, function ($q) use ($laboratorioId) {
                $q->whereHas('area', function ($q) use ($laboratorioId) {
                    $q->where('laboratorio_id', $laboratorioId);
                });
            })
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q) use ($query) {
                    $q->where('nombre', 'like', "%{$query}%")
                        ->orWhere('codigo', 'like', "%{$query}%");
                });
            })
            ->orderBy('id', 'desc')
            ->get();

        $equipos = Equipo::with('area', 'area.laboratorio', 'fotos')
            ->where('is_active', true)
            ->where('tipo', 'Reservable')
            ->when($laboratorioId !== null, function ($q) use ($laboratorioId) {
                $q->whereHas('area', function ($q) use ($laboratorioId) {
                    $q->where('laboratorio_id', $laboratorioId);
                });
            })
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q) use ($query) {
                    $q->where('nombre', 'like', "%{$query}%")
                        ->orWhere('codigo', 'like', "%{$query}%");
                });
            })
            ->orderBy('id', 'desc')
            ->get();

        $admin = User::where('rol', '=', 'Admin')->first();

        return Inertia::render('Catalogo/Index', [
            'recursos' => $recursos,
            'areas' => $areas,
            'equipos' => $equipos,
            'laboratorios' => $laboratorios,
            'adminNumber' => $admin->celular ?? 'No disponible'
        ]);
    }


    public function listaDeReservados($id, $tipo)
    {
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

}
