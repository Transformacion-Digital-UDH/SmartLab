<?php

use App\Actions\Fortify\CompletarRegistro;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\LaboratorioController;
use App\Http\Controllers\RecursoController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\MiembroController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UserController;
use App\Models\Proyecto;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Las rutas que serán accedidas por aplicaciones externas se definen en api.php,
// las que se usarán internamente en esta app laravel se definen en web.php

// Página principal
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware('guest')->controller(GoogleController::class)->group(function () {
    Route::get('/google/redirect', 'redirect')->name('google');
    Route::get('/google/callback', 'callback');
});

Route::middleware('auth')->controller(CompletarRegistro::class)->group(function () {
    Route::get('/completar-registro', 'create')->name('completar.registro');
    Route::post('/completar-registro', 'store');
});

// Grupo de rutas con middleware de autenticación (sesión interna)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'registro',
    'verified',
])->group(function () {

    // Dashboard
    Route::get('/panel', [PanelController::class, 'index'])->name('dashboard');

    // Vistas de Asistencia
    Route::get('/asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index');
    Route::get('/mis-asistencias', [AsistenciaController::class, 'misAsistencias'])->name('asistencias.user');
    Route::delete('/asistencias/eliminar/{id}', [AsistenciaController::class, 'eliminarAsistencia'])->name("asistencia.eliminar");
    Route::put('/asistencias/editar-salida/{id}/{date}', [AsistenciaController::class, 'editarSalida'])->name('asistencia.editar.salida');

    Route::get('/asistencias/completas', [AsistenciaController::class, 'asistenciasCompletas'])->name('asistencias.completas');
    Route::get('/asistencias/incompletas', [AsistenciaController::class, 'asistenciasIncompletas'])->name('asistencias.incompletas');
    Route::post('/asistencias/registro-completo', [AsistenciaController::class, 'registrarAsistenciaCompleta'])->name('asistencias.registroCompleto');

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
    Route::get('/inventario', [RecursoController::class, 'index'])->name('recursos.index');
    Route::post('/recursos', [RecursoController::class, 'store'])->name('recursos.store');
    Route::put('/recursos/{recurso}', [RecursoController::class, 'update'])->name('recursos.update');
    Route::delete('/recursos/{recurso}', [RecursoController::class, 'destroy'])->name('recursos.destroy');

    // Equipos
    Route::post('/equipos', [EquipoController::class, 'store'])->name('equipos.store');
    Route::put('/equipos/{equipo}', [EquipoController::class, 'update'])->name('equipos.update');
    Route::delete('/equipos/{equipo}', [EquipoController::class, 'destroy'])->name('equipos.destroy');

    // Catalogo
    Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');
    Route::get('/catalogo/reservas/{tipo}/{id}', [CatalogoController::class, 'listaDeReservados'])->name('catalogo.horarios');

    // Áreas
    Route::get('/laboratorios/{laboratorio_id}/areas', [AreaController::class, 'index'])->name('areas.json');
    Route::post('/areas', [AreaController::class, 'store'])->name('areas.store');
    Route::put('/areas/{area_id}', [AreaController::class, 'update'])->name('areas.update');
    Route::delete('/areas/{area_id}', [AreaController::class, 'destroy'])->name('areas.destroy');

    // Reservas
    Route::get('/reservas', [ReservaController::class, 'index'])->name('reservas.index');
    Route::post('/reserva/create', [ReservaController::class, 'create'])->name('reserva.create');
    Route::post('/reservas', [ReservaController::class, 'store'])->name('reservas.store');
    Route::put('/reservas/{reserva}', [ReservaController::class, 'update'])->name('reservas.update');
    Route::delete('/reservas/{reserva}', [ReservaController::class, 'destroy'])->name('reservas.destroy');

    // Proyectos
    Route::resource('proyectos', ProyectoController::class);
    Route::get('/proyectos/{proyecto}/participantes', [ProyectoController::class, 'obtenerParticipantes'])->name('proyectos.participantes');
    Route::post('/proyectos/{proyecto}/participantes', [ProyectoController::class, 'agregarParticipante'])->name('proyectos.agregar-participantes');
    Route::delete('/proyectos/{proyecto}/participantes/{participanteId}', [ProyectoController::class, 'quitarParticipante'])->name('proyectos.quitar-participante');
});


// Landing page
Route::get('/galeria', function () {
    return Inertia::render('Landing/Galeria');
})->name('galeria');

Route::get('/landing', function () {
    return Inertia::render('LandingDev/Index');
});

Route::get('/projects', function () {
    $proyectos = Proyecto::with('responsable')->get();
    return Inertia::render('LandingDev/Proyectos', ['magos' => 12, 'proyectos' => $proyectos]);
});
