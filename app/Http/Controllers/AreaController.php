<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Laboratorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AreaController extends Controller
{
    protected $rules = [
        'nombre' => ['required', 'string', 'max:100'],
        'descripcion' => ['nullable', 'string'],
        'tipo' => ['required', 'in:Reservable,No reservable'],
        'aforo' => ['nullable', 'integer', 'min:1'],
        'laboratorio_id' => ['required', 'exists:laboratorios,id'],
        'fotos.*' => ['image'],
        'fotos_nuevas.*' => ['image'],
        'fotos_eliminadas' => ['array'],
        'fotos_eliminadas.*' => ['integer', 'exists:fotos_areas,id'],
    ];

    public function index(Request $request)
    {
        $user = Auth::user();

        $areasQuery = Area::with('laboratorio', 'fotos')
            ->where('is_active', true);

        // Si el usuario no es Admin o ya tiene laboratorio seleccionado, filtramos por laboratorio
        if (!($user->rol === 'Admin' && $user->laboratorio_seleccionado === null)) {
            $areasQuery->where('laboratorio_id', $user->laboratorio_seleccionado);
        }

        // Excluir la primera área (por id mínimo) de cada laboratorio
        $areasQuery->whereNotIn('id', function($query) {
            $query->select(DB::raw('MIN(id)'))
                  ->from('areas')
                  ->groupBy('laboratorio_id');
        });

        $areas = $areasQuery->orderBy('id', 'desc')->get();

        $laboratorios = Laboratorio::where('is_active', true)
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('Areas/Index', [
            'areas' => $areas,
            'laboratorios' => $laboratorios,
        ]);
    }

    public function json($laboratorio_id)
    {
        if (Laboratorio::where('id', $laboratorio_id)->exists()) {

            $areas = Area::where('laboratorio_id', $laboratorio_id)
                ->where('is_active', true)
                ->orderBy('id', 'desc')
                ->get();

            return response()->json($areas);
        }
    }

    public function store(Request $request)
    {
        $request->validate($this->rules);
        $area = Area::create($request->all());

        // Procesar y guardar las imágenes si se enviaron
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $imagen) {
                $ruta = $imagen->store('areas', 'public');
                $area->fotos()->create(['ruta' => $ruta]);
            }
        }
    }

    public function update(Request $request, Area $area)
    {
        $request->validate($this->rules);
        $area->update($request->all());

        // Eliminar fotos enviadas para eliminación
        if ($request->has('fotos_eliminadas')) {
            foreach ($request->fotos_eliminadas as $fotoId) {
                $foto = $area->fotos()->find($fotoId);
                if ($foto) {
                    // Eliminar archivo físico y el registro en la base de datos
                    Storage::disk('public')->delete($foto->ruta);
                    $foto->delete();
                }
            }
        }

        // Agregar fotos nuevas
        if ($request->hasFile('fotos_nuevas')) {
            foreach ($request->file('fotos_nuevas') as $imagen) {
                $ruta = $imagen->store('areas', 'public');
                $area->fotos()->create(['ruta' => $ruta]);
            }
        }
    }

    public function destroy(Area $area)
    {
        $area->update(['is_active' => false]);
    }
}

