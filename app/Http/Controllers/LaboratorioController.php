<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Laboratorio;
use App\Models\User;

class LaboratorioController extends Controller
{
    protected $rules = [
        'nombre' => 'required|max:100',
        'codigo' => 'nullable|max:20',
        'descripcion' => 'nullable|max:255',
        'aforo' => 'nullable|integer',
        'email' => 'nullable|email|max:100',
        'inauguracion' => 'nullable|date',
        'responsable_id' => 'nullable|exists:users,id',
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
        Laboratorio::create($request->all());
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

    // Obtener los miembros de un laboratorio
    public function obtenerMiembros(Laboratorio $laboratorio)
    {
        return $laboratorio->miembros;
    }
}
