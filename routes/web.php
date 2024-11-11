<?php

use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\LaboratorioController;
use App\Http\Controllers\RecursoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\UserController;

// Las rutas que serán accedidas por aplicaciones externas se definen en api.php,
// las que se usarán internamente en esta app laravel se definen en web.php

// Página principal
Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        // 'laravelVersion' => Application::VERSION,
        // 'phpVersion' => PHP_VERSION,
    ]);
});

// Grupo de rutas con middleware de autenticación (sesión interna)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Vistas de Asistencia
    Route::get('/asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index');
    Route::get('/mis_asistencias', [AsistenciaController::class, 'mis_asistencias']);

    // Laboratorios
    Route::get('/laboratorios', [LaboratorioController::class, 'index'])->name('laboratorios.index');
    Route::post('/laboratorios', [LaboratorioController::class, 'store'])->name('laboratorios.store');
    Route::put('/laboratorios/{laboratorio}', [LaboratorioController::class, 'update'])->name('laboratorios.update');
    Route::delete('/laboratorios/{laboratorio}', [LaboratorioController::class, 'destroy'])->name('laboratorios.destroy');
    Route::get('/laboratorios/{laboratorio}/miembros', [LaboratorioController::class, 'mostrarMiembros'])->name('laboratorios.miembros');

    // Usuarios
    Route::get('/responsables', [UserController::class, 'getResponsables'])->name('responsables.index');

    // Recursos
    Route::get('/recursos', [RecursoController::class, 'index'])->name('recursos.index');
    Route::post('/recursos', [RecursoController::class, 'store'])->name('recursos.store');
    Route::put('/recursos/{recurso}', [RecursoController::class, 'update'])->name('recursos.update');
    Route::delete('/recursos/{recurso}', [RecursoController::class, 'destroy'])->name('recursos.destroy');

    // Áreas
    // Route::apiResource('areas', AreaController::class);
    Route::get('/laboratorios/{laboratorio_id}/areas', [AreaController::class, 'index']);
    Route::post('/areas', [AreaController::class, 'store']);


});
