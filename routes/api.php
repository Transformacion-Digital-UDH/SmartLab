<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsistenciaController;

// Las rutas que serán accedidas por aplicaciones externas se definen en api.php,
// las que se usarán internamente en web.php

// Rutas de API
Route::prefix('/asistencia')->middleware('auth:sanctum')->group(function () {
    Route::get('/user/{codigo}', [AsistenciaController::class, 'info'])->where('codigo', '^(\d{8}|\d{10})$');
    Route::post('/registrar_entrada', [AsistenciaController::class, 'registrarEntrada']);
    Route::put('/registrar_salida', [AsistenciaController::class, 'registrarSalida']);
    Route::put('/eliminar/{id}', [AsistenciaController::class, 'eliminar']);
    Route::get('/test', [AsistenciaController::class, 'test']);
});
 