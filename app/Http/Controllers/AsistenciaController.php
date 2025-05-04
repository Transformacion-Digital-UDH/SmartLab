<?php
namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\MiembroProyecto;
use App\Models\Proyecto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AsistenciaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $cantidad = $request->query('cantidad', 20);

        $asistenciasQuery = DB::table('asistencias')
            ->where('asistencias.is_active', 1)
            ->join('users', 'users.id', '=', 'asistencias.usuario_id')
            ->when(request('q'), function ($query, $busqueda) {
                // Buscar por nombres, apellidos o DNI
                $query->where(function ($subquery) use ($busqueda) {
                    $subquery->where('users.dni', 'like', "%{$busqueda}%")
                        ->orWhere('users.nombres', 'like', "%{$busqueda}%")
                        ->orWhere('users.apellidos', 'like', "%{$busqueda}%");
                });
            });

        // No filtrar por laboratorio si el usuario es "Admin" y no tiene un laboratorio seleccionado
        if (!($user->rol === 'Admin' && $user->laboratorio_seleccionado === null)) {
            $asistenciasQuery->where('asistencias.laboratorio_id', $user->laboratorio_seleccionado);
        }

        $asistencias = $asistenciasQuery
            ->select('asistencias.*', 'users.dni', 'users.nombres', 'users.apellidos', 'users.rol')
            ->orderBy('asistencias.hora_entrada', 'desc')
            ->paginate($cantidad);

        return Inertia::render('Asistencia/Index', [
            'token' => csrf_token(),
            'asistencias' => $asistencias
        ]);
    }

    public function misAsistencias(Request $request) {
        $usuario_id = Auth::id();
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);

        $asistencias = DB::table('asistencias')
            ->join('users', 'users.id', '=', 'asistencias.usuario_id')
            ->leftJoin('laboratorios', 'laboratorios.id', '=', 'asistencias.laboratorio_id')
            ->leftJoin('proyectos', 'proyectos.id', '=', 'asistencias.proyecto_id')
            ->where('asistencias.usuario_id', '=', $usuario_id)
            ->select(
                'asistencias.*',
                'users.dni',
                'users.nombres',
                'users.apellidos',
                'users.rol',
                'laboratorios.nombre as laboratorio',
                'proyectos.nombre as proyecto'
            )
            ->orderBy('asistencias.hora_entrada', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $totalAsistencias = DB::table('asistencias')
            ->where('usuario_id', '=', $usuario_id)
            ->count();

        $totalCompletas = DB::table('asistencias')
            ->where('usuario_id', '=', $usuario_id)
            ->whereNotNull('hora_salida')
            ->count();

        $totalIncompletas = DB::table('asistencias')
            ->where('usuario_id', '=', $usuario_id)
            ->whereNull('hora_salida')
            ->count();

        $asistenciasCompletas = DB::table('asistencias')
            ->join('users', 'users.id', '=', 'asistencias.usuario_id')
            ->leftJoin('laboratorios', 'laboratorios.id', '=', 'asistencias.laboratorio_id')
            ->leftJoin('proyectos', 'proyectos.id', '=', 'asistencias.proyecto_id')
            ->where('asistencias.usuario_id', '=', $usuario_id)
            ->whereNotNull('asistencias.hora_salida')
            ->select(
                'asistencias.*',
                'users.dni',
                'users.nombres',
                'users.apellidos',
                'users.rol',
                'laboratorios.nombre as laboratorio',
                'proyectos.nombre as proyecto'
            )
            ->orderBy('asistencias.hora_entrada', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $asistenciasIncompletas = DB::table('asistencias')
            ->join('users', 'users.id', '=', 'asistencias.usuario_id')
            ->leftJoin('laboratorios', 'laboratorios.id', '=', 'asistencias.laboratorio_id')
            ->leftJoin('proyectos', 'proyectos.id', '=', 'asistencias.proyecto_id')
            ->where('asistencias.usuario_id', '=', $usuario_id)
            ->whereNull('asistencias.hora_salida')
            ->select(
                'asistencias.*',
                'users.dni',
                'users.nombres',
                'users.apellidos',
                'users.rol',
                'laboratorios.nombre as laboratorio',
                'proyectos.nombre as proyecto'
            )
            ->orderBy('asistencias.hora_entrada', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('Asistencia/MiAsistencia', [
            'token' => csrf_token(),
            'asistencias' => $asistencias,
            'totalAsistencias' => $totalAsistencias,
            'totalCompletas' => $totalCompletas,
            'totalIncompletas' => $totalIncompletas,
            'asistenciasCompletas' => $asistenciasCompletas,
            'asistenciasIncompletas' => $asistenciasIncompletas,
        ]);
    }

    public function asistenciasCompletas(Request $request) {
        $usuario_id = Auth::id();
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);

        $asistenciasCompletas = DB::table('asistencias')
            ->join('users', 'users.id', '=', 'asistencias.usuario_id')
            ->leftJoin('laboratorios', 'laboratorios.id', '=', 'asistencias.laboratorio_id')
            ->leftJoin('proyectos', 'proyectos.id', '=', 'asistencias.proyecto_id')
            ->where('asistencias.usuario_id', '=', $usuario_id)
            ->whereNotNull('asistencias.hora_salida')
            ->select(
                'asistencias.*',
                'users.dni',
                'users.nombres',
                'users.apellidos',
                'users.rol',
                'laboratorios.nombre as laboratorio',
                'proyectos.nombre as proyecto'
            )
            ->orderBy('asistencias.hora_entrada', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('Asistencia/MiAsistencia', [
            'asistenciasCompletas' => $asistenciasCompletas,
        ]);
    }

    public function asistenciasIncompletas(Request $request) {
        $usuario_id = Auth::id();
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);

        $asistenciasIncompletas = DB::table('asistencias')
            ->join('users', 'users.id', '=', 'asistencias.usuario_id')
            ->leftJoin('laboratorios', 'laboratorios.id', '=', 'asistencias.laboratorio_id')
            ->leftJoin('proyectos', 'proyectos.id', '=', 'asistencias.proyecto_id')
            ->where('asistencias.usuario_id', '=', $usuario_id)
            ->whereNull('asistencias.hora_salida')
            ->select(
                'asistencias.*',
                'users.dni',
                'users.nombres',
                'users.apellidos',
                'users.rol',
                'laboratorios.nombre as laboratorio',
                'proyectos.nombre as proyecto'
            )
            ->orderBy('asistencias.hora_entrada', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('Asistencia/MiAsistencia', [
            'asistenciasIncompletas' => $asistenciasIncompletas,
        ]);
    }

    public function misAsistenciasCompletas(Request $request) {
        $usuario_id = Auth::id();
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);

        $asistenciasCompletas = DB::table('asistencias')
            ->join('users', 'users.id', '=', 'asistencias.usuario_id')
            ->leftJoin('laboratorios', 'laboratorios.id', '=', 'asistencias.laboratorio_id')
            ->leftJoin('proyectos', 'proyectos.id', '=', 'asistencias.proyecto_id')
            ->where('asistencias.usuario_id', '=', $usuario_id)
            ->whereNotNull('asistencias.hora_salida')
            ->select(
                'asistencias.*',
                'users.dni',
                'users.nombres',
                'users.apellidos',
                'users.rol',
                'laboratorios.nombre as laboratorio',
                'proyectos.nombre as proyecto'
            )
            ->orderBy('asistencias.hora_entrada', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('Asistencia/MiAsistencia', [
            'token' => csrf_token(),
            'asistencias' => $asistenciasCompletas,
            'asistenciasCompletas' => $asistenciasCompletas,
            'asistenciasIncompletas' => []
        ]);
    }

    public function misAsistenciasIncompletas(Request $request) {
        $usuario_id = Auth::id();
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);

        $asistenciasIncompletas = DB::table('asistencias')
            ->join('users', 'users.id', '=', 'asistencias.usuario_id')
            ->leftJoin('laboratorios', 'laboratorios.id', '=', 'asistencias.laboratorio_id')
            ->leftJoin('proyectos', 'proyectos.id', '=', 'asistencias.proyecto_id')
            ->where('asistencias.usuario_id', '=', $usuario_id)
            ->whereNull('asistencias.hora_salida')
            ->select(
                'asistencias.*',
                'users.dni',
                'users.nombres',
                'users.apellidos',
                'users.rol',
                'laboratorios.nombre as laboratorio',
                'proyectos.nombre as proyecto'
            )
            ->orderBy('asistencias.hora_entrada', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('Asistencia/MiAsistencia', [
            'token' => csrf_token(),
            'asistencias' => $asistenciasIncompletas,
            'asistenciasCompletas' => [],
            'asistenciasIncompletas' => $asistenciasIncompletas
        ]);
    }

    public function test() {
        return $asistencias = DB::table('asistencias')
            ->where('asistencias.is_active',1)
            ->join('users','users.id','=','asistencias.usuario_id')
            ->select('asistencias.*', 'users.dni','users.nombres','users.rol')
            ->orderBy('asistencias.hora_entrada', 'desc')
            ->paginate(12);
    }

    private function crearUsuarioInvitado($dni) {
        return User::create([
            'id' => null,
            'dni' => $dni,
            'codigo' => null,
            'rol' => 'Invitado'
        ]);
    }

    // APIS
    // GET - Recuperar la informacion del usuario
    public function info(Request $request, $dni) {
        $user = User::where('dni', $dni)->first();

        if (!$user) {
            $user = $this->crearUsuarioInvitado($dni);
            if (!$user) {
                return response(["status" => 356]);
            }
        }

        // $defi = User::with('asistencias')->where('dni', '=', $dni)->get();
        $asistencias = Asistencia::where('usuario_id', $user->id)
            ->whereDate('hora_entrada', Carbon::today())
            ->get();

        $proyectos = MiembroProyecto::where('usuario_id', $user->id)
            ->join('proyectos', 'miembro_proyectos.proyecto_id', '=', 'proyectos.id')
            ->select('proyectos.nombre', 'proyectos.descripcion', 'proyectos.id', 'proyectos.estado')
            ->get();

        $proyectosA = Proyecto::where('responsable_id', $user->id)
            ->select('proyectos.nombre', 'proyectos.descripcion', 'proyectos.id', 'proyectos.estado')
            ->get();

        return response()->json([
            "id" => $user-> id,
            "nombres"       => $user->nombres,
            "apellidos"     => $user->apellidos,
            "rol"           => $user->rol,
            "tiene_entrada" => $user->rol,
            "asistencias"   => $asistencias,
            "proyectos"     => $proyectos->merge($proyectosA),
        ]);
    }

    // POST - Establecer la hora de entrada
    public function registrarEntrada(Request $request){
        $request -> validate([
            'user_id' => 'required|integer',
            'laboratorio_id' => 'required|integer',
        ]);

        $data = $request->all();
        $usuario_id = $data['user_id'];
        $proyecto_id = $data['proyecto_id'];
        $laboratorio_id = $data['laboratorio_id'];

        try {

            Asistencia::create([
                'id'           => null,
                'usuario_id'   => $usuario_id,
                'hora_entrada' => now(),
                'tarea'        => '',
                'proyecto_id'  => $proyecto_id,
                'laboratorio_id'  => $laboratorio_id,
            ]);
        } catch (\Throwable $th) {
            return response($th, 270);
        }

        return response('SE REGISTRO LA ENTRADA', 201);
    }

    public function registrarAsistenciaCompleta(Request $request){
        $request -> validate([
            'usuario_id' => 'required|integer',
            // 'laboratorio_id' => 'required|integer',
        ]);

        $data = $request->all();
        $usuario_id = $data['usuario_id'];
        $proyecto_id = $data['proyecto_id'];
        $laboratorio_id = $data['laboratorio_id'];

        $hora_entrada = $data['hora_entrada'];
        $hora_salida = $data['hora_salida'];

        try {

            Asistencia::create([
                'id'           => null,
                'usuario_id'   => $usuario_id,
                'hora_entrada' => $hora_entrada,
                'hora_salida' => $hora_salida,
                'tarea'        => '',
                'proyecto_id'  => $proyecto_id,
                'laboratorio_id'  => 1,
            ]);

            return response('SE REGISTRO LA ENTRADA', 201);
        } catch (\Throwable $th) {
            return response($th, 270);
        }

    }

    // PUT - Registra la salida
    public function registrarSalida(Request $request){
        $user_id = $request -> input('user_id');

        $asistencia = Asistencia::where('usuario_id', $user_id)->latest('hora_entrada')->first();
        $asistencia -> hora_salida = now();
        $asistencia -> save();
        return response('SE REGISTRO SU SALIDA EXITOSAMENTE', 200);

    }
    public function listar(Request $request){

        return response()->json(Asistencia::all())->get();

    }
    // DELETE
    function eliminarAsistencia(Request $request, $id){
        $asistencia = Asistencia::find($id);

        $asistencia -> is_active = 0;
        $asistencia -> save();

        // return response()->json(['message' => 'SE ELIMINO CORRECTAMENTE'], 200);
    }
    function editarSalida(Request $request, $id, $date){
        $asistencia = Asistencia::find($id);

        $asistencia -> hora_salida = $date;
        $asistencia -> save();

        // return response()->json(['message' => 'SE EACTUALIZO LA HORA DE SALIDA'], 200);

    }
}
