<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\MiembroProyecto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AsistenciaController extends Controller
{
    public function index(Request $request){
        $cantidad = $request -> query('cantidad', 20);

        $asistencias = DB::table('asistencias')
            ->where('asistencias.is_active',1)
            ->join('users','users.id','=','asistencias.usuario_id')
            ->select('asistencias.*', 'users.dni','users.nombres','users.rol')
            ->orderBy('asistencias.hora_entrada', 'desc')
            ->paginate($cantidad);


        return Inertia::render('Asistencia/Index', [
            'token' => csrf_token(),
            'asistencias' => $asistencias
        ]);
    }
    function mis_asistencias(){
        $asistencias = DB::table('asistencias')
        ->join('users','users.id','=','asistencias.usuario_id')
        ->where('asistencias.usuario_id', '=', '2')
        ->select('asistencias.*', 'users.dni','users.nombres','users.rol')
        ->get();

        return Inertia::render('Asistencia/MiAsistencia', [
            'token' => csrf_token(),
            'asistencias' => $asistencias
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
    public function info(Request $request, $codigo) {
        $user = null;
        if (strlen($codigo) == 8) {
            $user = User::where('dni', $codigo)->first();
        } else {
            $user = User::where('codigo', $codigo)->first();

            if (!$user) {
                return response('USTED NO ESTA REGISTRADO', 300);
            }
        }


        if (!$user) {
            $dni = strlen($codigo) == 8 ? $codigo : null;
            $user = $this->crearUsuarioInvitado($dni, $codigo);
        } 
        
        $asistencias = Asistencia::where('usuario_id', $user->id)
            ->whereDate('hora_entrada', Carbon::today())
            ->get();
        
        $proyectos = MiembroProyecto::where('usuario_id', $user->id)
            ->join('proyectos', 'miembro_proyectos.proyecto_id', '=', 'proyectos.id')
            ->select('proyectos.nombre', 'proyectos.descripcion', 'proyectos.id')
            ->get();

        return response()->json([
            "id" => $user-> id,
            "nombres"       => $user->nombres,
            "apellidos"     => $user->apellidos,
            "rol"           => $user->rol,
            "tiene_entrada" => $user->rol,
            "asistencias"   => $asistencias,
            "proyectos"     => $proyectos,
            // "\$user" => $user
        ]);
    }

    // POST - Establecer la hora de entrada
    public function registrarEntrada(Request $request){
        $request -> validate([
            'user_id' => 'required|integer'
        ]);

        $data = $request->all();
        $usuario_id = $data['user_id'];
        $proyecto_id = $data['proyecto_id'];

        try {
            
            Asistencia::create([
                'id'           => null,
                'usuario_id'   => $usuario_id,
                'hora_entrada' => now(),
                'tarea'        => '',
                'proyecto_id'  => $proyecto_id
            ]);
        } catch (\Throwable $th) {
            return response($th, 270);
        }

        return response('SE REGISTRO LA ENTRADA', 201);
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
    function eliminar_asistencia(Request $request, $id){
        $asistencia = Asistencia::find($id);

        $asistencia -> is_active = 0;
        $asistencia -> save();

        // return response()->json(['message' => 'SE ELIMINO CORRECTAMENTE'], 200);
    }
    function editar_salida(Request $request, $id, $date){
        $asistencia = Asistencia::find($id);

        $asistencia -> hora_salida = $date;
        $asistencia -> save();

        // return response()->json(['message' => 'SE EACTUALIZO LA HORA DE SALIDA'], 200);

    }
}
