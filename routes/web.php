<?php

use App\Http\Controllers\AsistenciaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\LaboratorioController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/login', function () {
    return Inertia::render('Login', []);
});

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return Inertia::render('Dashboard');
//     })->name('dashboard');
// });

// Route::get('/api/asistencia/user', [AsistenciaController::class, 'informacionDelUsuario']);
// Route::post('/api/asistencia/registrar_entrada', [AsistenciaController::class, 'registrarEntrada']);
// Route::put('/api/asistencia/registrar_salida', [AsistenciaController::class, 'registrarSalida']);

Route::prefix('api/asistencia') -> controller(AsistenciaController::class) -> group(function() {
    Route::get('/user/{codigo}', 'index')
        -> where('codigo', '^(\d{8}|\d{10})$');
    Route::post('/registrar_entrada', 'registrarEntrada');
    Route::put('/registrar_salida', 'registrarSalida');
});

// Rutas para pruebas sin middlewares
Route::get('/laboratorios', [LaboratorioController::class, 'index'])->name('laboratorios.index');
Route::post('/laboratorios', [LaboratorioController::class, 'store'])->name('laboratorios.store');
Route::put('/laboratorios/{laboratorio}', [LaboratorioController::class, 'update'])->name('laboratorios.update');
Route::delete('/laboratorios/{laboratorio}', [LaboratorioController::class, 'destroy'])->name('laboratorios.destroy');
