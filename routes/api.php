<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\LaboratorioController;
use App\Http\Controllers\ReservaApiController;
use App\Models\Asistencia;

// Las rutas que serán accedidas por aplicaciones externas se definen en api.php,
// las que se usarán internamente en web.php

// Rutas de API
Route::prefix('/asistencia')->group(function () {
    Route::get('/user/{codigo}', [AsistenciaController::class, 'info'])->where('codigo', '^(\d{8}|\d{10})$');
    Route::post('/registrar_entrada', [AsistenciaController::class, 'registrarEntrada']);
    Route::put('/registrar_salida', [AsistenciaController::class, 'registrarSalida']);
    Route::put('/eliminar/{id}', [AsistenciaController::class, 'eliminar']);
});

Route::prefix('/users')->group(function () {
    Route::get('{dni}', [AsistenciaController::class, 'info'])->where('codigo', '^\d{8}$');
});

Route::prefix('/laboratorios')->group(function () {
    Route::get('/', [LaboratorioController::class, 'info']);
    Route::post('/auth', [LaboratorioController::class, 'validar_lab']);
});

Route::prefix('/reservas')->group(function () {
    Route::get('/{tipo}/{id}', [ReservaApiController::class, 'index']);
});