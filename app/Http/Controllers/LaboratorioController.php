<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Laboratorio;
use App\Models\User;
use Carbon\Carbon;

class LaboratorioController extends Controller
{
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


    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'nombre' => 'required|max:100',
            'codigo' => 'nullable|max:20',
            'descripcion' => 'nullable|max:255',
            'aforo' => 'nullable|integer',
            'email' => 'nullable|email|max:100',
            'inauguracion' => 'nullable|date',
            'responsable_id' => 'nullable|exists:users,id',
        ], [
            'required' => 'Este campo es obligatorio.',
            'email' => 'Ingrese un correo electrónico válido.',
            'max' => 'Este campo no puede exceder de :max caracteres.',
            'integer' => 'Este campo debe ser un número entero.',
            'date' => 'Ingrese una fecha válida.',
        ]);

        // Crear el laboratorio
        Laboratorio::create($request->all());
    }



    public function update(Request $request, Laboratorio $laboratorio)
    {
        // Validación
        $request->validate([
            'nombre' => 'required|max:100',
            'codigo' => 'nullable|max:20',
            'descripcion' => 'nullable|max:255',
            'aforo' => 'nullable|integer',
            'email' => 'nullable|email|max:100',
            'inauguracion' => 'nullable|date',
            'responsable_id' => 'nullable|exists:users,id',
        ], [
            'required' => 'Este campo es obligatorio.',
            'email' => 'Ingrese un correo electrónico válido.',
            'max' => 'Este campo no puede exceder de :max caracteres.',
            'integer' => 'Este campo debe ser un número entero.',
            'date' => 'Ingrese una fecha válida.',
        ]);

        // Actualizar laboratorio
        $laboratorio->update($request->all());

    }

    public function destroy(Laboratorio $laboratorio)
    {
        $laboratorio->is_active = false;
        $laboratorio->save();
    }

    // API
    public function info()
    {
        $laboratorios = Laboratorio::select('id', 'nombre')->get();
        return [
            'data' => $laboratorios
        ];
    }
    public function validar_lab(Request $request)
    {
        $id = $request -> input('id');
        $codigo = $request -> input('codigo');

        $lab = Laboratorio::find($id);
        if($lab -> codigo == $codigo) {
            return response(null);
        }
        return response('Codigo Invalido', 401);
    }
}
