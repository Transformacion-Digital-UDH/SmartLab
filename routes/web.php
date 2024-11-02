<?php

use App\Http\Controllers\AsistenciaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\LaboratorioController;
use App\Http\Controllers\UserController;


Route::get('/', function () {
    // return Inertia::render('Welcome', [
    //     'canLogin' => Route::has('login'),
    //     'canRegister' => Route::has('register'),
    //     'laravelVersion' => Application::VERSION,
    //     'phpVersion' => PHP_VERSION,
    // ]);
    return Inertia::render('Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

// Seccion Asistencia
Route::prefix('api/asistencia') -> controller(AsistenciaController::class) -> group(function() {
    Route::get('/user/{codigo}', 'info')
        -> where('codigo', '^(\d{8}|\d{10})$');
    Route::post('/registrar_entrada', 'registrarEntrada');
    Route::put('/registrar_salida', 'registrarSalida');
    Route::put('/eliminar/{id}', 'eliminar');
});

// Seccion Asistencia Vista
Route::get('/asistencias', [AsistenciaController::class, 'index']);

// Rutas para pruebas sin middlewares
Route::get('/laboratorios', [LaboratorioController::class, 'index'])->name('laboratorios.index');
Route::post('/laboratorios', [LaboratorioController::class, 'store'])->name('laboratorios.store');
Route::put('/laboratorios/{laboratorio}', [LaboratorioController::class, 'update'])->name('laboratorios.update');
Route::delete('/laboratorios/{laboratorio}', [LaboratorioController::class, 'destroy'])->name('laboratorios.destroy');


Route::get('/areas', function () {
    return Inertia::render('Areas/AreaList');
});

Route::get('/areas/create', function () {
    return Inertia::render('Areas/AreasCreate');
});

Route::get('/areas/edit/{id}', function ($id) {
    return Inertia::render('Areas/AreasEdit', ['id' => $id]);
});


Route::get('/laboratorios/{laboratorio}/miembros', [LaboratorioController::class, 'mostrarMiembros'])->name('laboratorios.miembros');


Route::get('/responsables', [UserController::class, 'getResponsables'])->name('responsables.index');
