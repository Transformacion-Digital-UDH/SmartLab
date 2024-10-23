<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\MiembroProyecto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AsistenciaController extends Controller
{
    public function index(){
        $asistencias = DB::table('asistencias')
        ->join('users','users.id','=','asistencias.usuario_id')
        ->select('asistencias.*', 'users.dni','users.nombres','users.rol')
        ->get();

        // $asistencias = DB::table('asistencias')
        // ->join('users','users.id','=','asistencias.usuario_id')
        // ->where('asistencias.usuario_id', '=', '2')
        // ->select('asistencias.*', 'users.dni','users.nombres','users.rol')
        // ->get();

        return Inertia::render('Asistencia/Main', [
            'token' => csrf_token(),
            'asistencias' => $asistencias
        ]);
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
        
        $proyectos = MiembroProyecto::where('usuario_id', $user->id)->get();

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
            'usuario_id' => 'required|integer'
        ]);

        $data = $request->all();
        $usuario_id = $data['usuario_id'];
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

        return response('SE REGISTRO LA ENTRADA', 200);
    }

    // PUT - Registra la salida
    public function registrarSalida(Request $request){
        $user_id = $request -> input('usuario_id');
        
        $asistencia = Asistencia::where('usuario_id', $user_id)->latest('hora_entrada')->first();
        $asistencia -> hora_salida = now();
        $asistencia -> save();
        return response('SE REGISTRO SU SALIDA EXITOSAMENTE', 200);

    }
    public function listar(Request $request){
        
        return response()->json(Asistencia::all())->get();

    }
}
