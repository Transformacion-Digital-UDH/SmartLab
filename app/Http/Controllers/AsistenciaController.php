<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\MiembroProyecto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    private function crearUsuarioInvitado($dni) {
        return User::create([
            'id' => null,
            'dni' => $dni,
            'codigo' => null,
            'rol' => 'Invitado'
        ]);
    }
    // GET
    public function index(Request $request, $codigo) {
        $user = null;
        if (strlen($codigo) == 8) {
            $user = User::where('dni', $codigo)->first();
        } else {
            $user = User::where('codigo', $codigo)->first();

            if (!$user) {
                return response('USTED NO ESTA REGISTRADO', 200);
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

    // POST
    public function registrarEntrada(Request $request){
        // $request -> validate([
        //     'usuario_id' => 'required|integer'
        // ]);

        // $data = $request->all();
        // $usuario_id = $data['usuario_id'];
        // $proyecto_id = $data['proyecto_id'];

        try {
            
            Asistencia::create([
                'id'           => null,
                'usuario_id'   => 2,
                'hora_entrada' => now(),
                'tarea'        => null,
                'proyecto_id'  => null
            ]);
        } catch (\Throwable $th) {
            return response($th, 270);
            
        }

        return response('SE REGISTRO LA ENTRADA', 200);
    }

    // PUT
    public function registrarSalida(Request $request){
        return response('SE REGISTRO EXITOSAMENTE LA ASISTENCIA', 200);
        $user_id = $request -> input('user_id');

        $asistencia = Asistencia::where('user_id', $user_id)->latest('hora_entrada')->first();
        $asistencia -> hora_salida = now();

    }
}
