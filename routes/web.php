<?php

use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\LaboratorioController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\RecursoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\MiembroController;
use App\Http\Controllers\UserController;

// Las rutas que serán accedidas por aplicaciones externas se definen en api.php,
// las que se usarán internamente en esta app laravel se definen en web.php

// Página principal
Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
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
    Route::delete('/asistencias/eliminar/{id}', [AsistenciaController::class, 'eliminar_asistencia'])->name("asistencia.eliminar");
    Route::put('/asistencias/editar_salida/{id}/{date}', [AsistenciaController::class, 'editar_salida'])->name('asistencia.editar.salida');

    // Laboratorios
    Route::resource('laboratorios', LaboratorioController::class)->except(['show', 'create', 'edit']);
    Route::get('/laboratorios/{laboratorio}/miembros', [LaboratorioController::class, 'obtenerMiembros'])->name('laboratorios.miembros');

    // Usuarios
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/json', [UserController::class, 'getUsuarios'])->name('usuarios.json');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{usuario}', [UserController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{usuario}', [UserController::class, 'destroy'])->name('usuarios.destroy');

    // miembros
    Route::get('/miembros', [MiembroController::class, 'index'])->name('miembros.index');
    Route::post('/miembros', [MiembroController::class, 'store'])->name('miembros.store');
    Route::put('/miembros/{usuario}', [MiembroController::class, 'update'])->name('miembros.update');
    Route::delete('/miembros/{usuario}', [MiembroController::class, 'destroy'])->name('miembros.destroy');


    // Recursos
    Route::get('/recursos', [RecursoController::class, 'index'])->name('recursos.index');
    Route::post('/recursos', [RecursoController::class, 'store'])->name('recursos.store');
    Route::put('/recursos/{recurso}', [RecursoController::class, 'update'])->name('recursos.update');
    Route::delete('/recursos/{recurso}', [RecursoController::class, 'destroy'])->name('recursos.destroy');

    // Áreas
    // Route::apiResource('areas', AreaController::class);
    Route::get('/laboratorios/{laboratorio_id}/areas', [AreaController::class, 'index'])->name('areas.json');
    Route::post('/areas', [AreaController::class, 'store'])->name('areas.store');
    Route::put('/areas/{area_id}', [AreaController::class, 'update'])->name('areas.update');
    Route::delete('/areas/{area_id}', [AreaController::class, 'destroy'])->name('areas.destroy');

    // Proyectos
    Route::resource('proyectos', ProyectoController::class);
    Route::get('/proyectos/{proyecto}/participantes', [ProyectoController::class, 'obtenerParticipantes'])->name('proyectos.participantes');
    Route::post('/proyectos/{proyecto}/participantes', [ProyectoController::class, 'agregarParticipante'])->name('proyectos.agregar-participantes');
    Route::delete('/proyectos/{proyecto}/participantes/{participanteId}', [ProyectoController::class, 'quitarParticipante'])->name('proyectos.quitar-participante');

});
