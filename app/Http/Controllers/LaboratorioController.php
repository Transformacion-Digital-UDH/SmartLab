<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Laboratorio;
use App\Models\User;
use App\Models\Area;

class LaboratorioController extends Controller
{
    public $rules = [
        'nombre' => ['required', 'string', 'min:5', 'max:90'],
        'codigo' => ['required', 'string', 'min:5', 'max:10'],
        'descripcion' => ['nullable', 'string', 'min:5', 'max:100'],
        'aforo' => ['nullable', 'integer', 'min:1', 'max:100'],
        'email' => ['nullable', 'email', 'min:5', 'max:100'],
        'inauguracion' => ['nullable', 'date'],
        'responsable_id' => ['required', 'integer', 'exists:users,id'],
    ];

    // Listar los laboratorios en la vista
    public function index()
    {
        $laboratorios = Laboratorio::with('responsable')
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->get();

        $responsables = User::where('rol', 'Responsable')->get();

        return Inertia::render('Laboratorios/Index', [
            'laboratorios' => $laboratorios,
            'responsables' => $responsables,
        ]);
    }

    // Crear el laboratorio
    public function store(Request $request)
    {
        $request->validate($this->rules);

        $laboratorio = Laboratorio::create($request->all());

        // Crear un área llamada "General" por defecto para el laboratorio
        Area::create([
            'nombre' => 'General',
            'descripcion' => 'Área general del laboratorio',
            'laboratorio_id' => $laboratorio->id,
            'is_active' => true,
            'tipo' => 'No reservable'
        ]);
    }

    // Actualizar laboratorio
    public function update(Request $request, Laboratorio $laboratorio)
    {
        $request->validate($this->rules);
        $laboratorio->update($request->all());
    }

    // Eliminar laboratorio
    public function destroy(Laboratorio $laboratorio)
    {
        $laboratorio->is_active = false;
        $laboratorio->save();
    }

    // API
    public function info()
    {
        $laboratorios = Laboratorio::select('id', 'nombre')->where('is_active', 1)->get();
        return [
            'data' => $laboratorios
        ];
    }
    public function validar_lab(Request $request)
    {
        $id = $request->input('id');
        $codigo = $request->input('codigo');

        $lab = Laboratorio::find($id);
        if ($lab->codigo == $codigo) {
            return response(null);
        }
        return response('Codigo Invalido', 401);
    }

    // Obtener los miembros de un laboratorio
    public function obtenerMiembros(Laboratorio $laboratorio)
    {
        return $laboratorio->miembros;
    }
}
