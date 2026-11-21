<?php

use App\Http\Controllers\Admin\AnalisisColasController;
use App\Http\Controllers\Admin\AuditoriaController;
use App\Http\Controllers\Admin\CatalogoAdminController;
use App\Http\Controllers\Admin\ChatbotAdminController;
use App\Http\Controllers\Admin\CicloVidaClienteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FinanzasController;
use App\Http\Controllers\Admin\FlotaController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\IoGestionController;
use App\Http\Controllers\Admin\PersonalController;
use App\Http\Controllers\Admin\ReservasAdminController;
use App\Http\Controllers\Admin\SalidasController;
use App\Http\Controllers\Auth\CodigoCorreoController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\CuentaTuristaController;
use App\Http\Controllers\NovedadesController;
use App\Http\Controllers\Pagos\PagoController;
use App\Http\Controllers\Reservas\ConsultaReservaController;
use App\Http\Controllers\Reservas\ResenaController;
use App\Http\Controllers\Reservas\ReservaController;
use App\Models\User;
use App\Services\Io\IoDemoData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

// La ruta nombrada genera la URL nueva. La anterior conserva el método y sus permisos.
$rutaLegible = static function (array $metodos, string $ruta, string $anterior, mixed $accion, array $middleware = []) {
    $nombreAnterior = 'legacy.'.str_replace('/', '.', trim($anterior, '/')).'.'.strtolower($metodos[0]);
    Route::match($metodos, $anterior, $accion)->middleware($middleware)->name($nombreAnterior);

    return Route::match($metodos, $ruta, $accion)->middleware($middleware);
};

// Compatibilidad con los formularios de acceso anteriores a las rutas de Fortify en español.
$guardiaFortify = config('fortify.guard');
$invitadoFortify = 'guest:'.$guardiaFortify;
if (config('fortify.views', true)) {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->middleware($invitadoFortify)->name('legacy.login');
}
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware(array_filter([
    $invitadoFortify, config('fortify.limiters.login') ? 'throttle:'.config('fortify.limiters.login') : null,
]));
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware(config('fortify.auth_middleware', 'auth').':'.$guardiaFortify);
if (Features::enabled(Features::registration())) {
    if (config('fortify.views', true)) {
        Route::get('/register', [RegisteredUserController::class, 'create'])->middleware($invitadoFortify)->name('legacy.register');
    }
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware($invitadoFortify);
}

Route::view('/recuperar-contrasena', 'auth.recuperar-codigo')->middleware('guest')->name('codigo.recuperar');
Route::post('/recuperar-contrasena/codigo', [CodigoCorreoController::class, 'solicitar'])->middleware(['guest', 'throttle:5,1'])->name('codigo.solicitar');
Route::post('/recuperar-contrasena', [CodigoCorreoController::class, 'restablecer'])->middleware(['guest', 'throttle:10,1'])->name('codigo.restablecer');
Route::post('/email/verificar-codigo', [CodigoCorreoController::class, 'verificar'])->middleware(['auth', 'throttle:10,1'])->name('codigo.verificar');
Route::get('/auth/google', [GoogleController::class, 'redirect'])->middleware(['guest', 'throttle:10,1'])->name('google.redirect');
Route::get('/auth/google/vincular', [GoogleController::class, 'redirect'])->middleware(['auth', 'verified', 'password.confirm'])->name('google.vincular');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->middleware('throttle:10,1')->name('google.callback');
Route::post('/novedades', [NovedadesController::class, 'store'])->middleware(['guest', 'throttle:3,1'])->name('novedades.store');
Route::match(['get', 'post'], '/novedades/{id}/confirmar', [NovedadesController::class, 'confirmar'])->middleware(['signed', 'throttle:10,1'])->name('novedades.confirmar');
Route::match(['get', 'post'], '/novedades/{id}/baja', [NovedadesController::class, 'baja'])->middleware(['signed', 'throttle:10,1'])->name('novedades.baja');

Route::get('/', [CatalogoController::class, 'index'])->name('home');
Route::get('/creditos-imagenes', fn () => view('tours.creditos', ['creditos' => json_decode(file_get_contents(public_path('images/tours/creditos.json')), true, flags: JSON_THROW_ON_ERROR)]))->name('imagenes.creditos');
Route::get('/categorias/{categoria:rta_amg}', [CatalogoController::class, 'categoria'])->name('categorias.show');
Route::get('/guias/perfil/{guia:rta_amg}', [CatalogoController::class, 'guia'])->name('guias.perfil');
Route::get('/tours/{tour:rta_amg}', [CatalogoController::class, 'show'])->name('tours.show');

/*
|--------------------------------------------------------------------------
| Motor de reservas (Módulo 05) y pagos (Módulo 06): públicos, admiten invitados
|--------------------------------------------------------------------------
*/

Route::get('/tours/{tour:rta_amg}/reservar', [ReservaController::class, 'salida'])->name('reservas.salida');
Route::post('/tours/{tour:rta_amg}/reservar', [ReservaController::class, 'guardarSalida'])->name('reservas.salida.store');

Route::prefix('reservas')->name('reservas.')->group(function () {
    Route::get('/participantes', [ReservaController::class, 'participantes'])->name('participantes');
    Route::post('/participantes', [ReservaController::class, 'guardarParticipantes'])->name('participantes.store');
    Route::get('/equipo', [ReservaController::class, 'equipo'])->name('equipo');
    Route::post('/equipo', [ReservaController::class, 'guardarEquipo'])->name('equipo.store');
    Route::get('/ficha-medica', [ReservaController::class, 'ficha'])->name('ficha');
    Route::post('/ficha-medica', [ReservaController::class, 'guardarFicha'])->name('ficha.store');
    Route::get('/resumen', [ReservaController::class, 'resumen'])->name('resumen');
    Route::post('/confirmar', [ReservaController::class, 'confirmar'])->name('confirmar');

    Route::get('/consultar', [ConsultaReservaController::class, 'consultar'])->name('consultar');
    Route::post('/consultar', [ConsultaReservaController::class, 'buscar'])->middleware('throttle:10,1')->name('consultar.buscar');

    Route::get('/{reserva}/confirmada', [ReservaController::class, 'confirmada'])->name('confirmada');
    Route::get('/{reserva}', [ConsultaReservaController::class, 'estado'])->name('estado');
    Route::post('/{reserva}/cancelar', [ConsultaReservaController::class, 'cancelar'])->name('cancelar');
    Route::post('/{reserva}/resena', [ResenaController::class, 'store'])->middleware('throttle:10,1')->name('resena');
    Route::get('/{reserva}/boletos.pdf', [ConsultaReservaController::class, 'boletosPdf'])->name('boletos');
    Route::get('/{reserva}/comprobante.pdf', [ConsultaReservaController::class, 'comprobantePdf'])->name('comprobante');
});

Route::get('/boletos/{boleto}.pdf', [ConsultaReservaController::class, 'boletoPdf'])->name('boletos.pdf');

Route::get('/reservas/{reserva}/pagar', [PagoController::class, 'create'])->name('pagos.create');
Route::post('/reservas/{reserva}/pagar', [PagoController::class, 'store'])->middleware('throttle:20,1')->name('pagos.store');

// Pasarela simulada (imita al proveedor externo) y su webhook firmado con HMAC.
Route::get('/pasarela/{ref}', [PagoController::class, 'pasarela'])->name('pasarela.show');
Route::post('/pasarela/{ref}', [PagoController::class, 'resolver'])->name('pasarela.resolver');
Route::post('/webhooks/pasarela', [PagoController::class, 'webhook'])->name('webhooks.pasarela');

// Asistente turístico: público (turistas e invitados, RF-28); IA en n8n con respaldo local.
$rutaLegible(['GET', 'HEAD'], '/asistente-turistico', '/chatbot', [ChatbotController::class, 'index'])->name('chatbot');
$rutaLegible(['POST'], '/asistente-turistico/mensajes', '/chatbot/mensajes', [ChatbotController::class, 'enviar'], ['throttle:20,1'])->name('chatbot.enviar');
$rutaLegible(['GET', 'HEAD'], '/asistente-turistico/mensajes', '/chatbot/mensajes', [ChatbotController::class, 'mensajes'], ['throttle:60,1'])->name('chatbot.mensajes');

/*
|--------------------------------------------------------------------------
| Rutas autenticadas, protegidas por rol (middleware `rol:`)
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () use ($rutaLegible) {
    // Inicio genérico: cada rol va a su propia pantalla.
    Route::get('/mis-reservas', [ConsultaReservaController::class, 'historial'])->name('reservas.historial');
    Route::middleware('rol:'.User::ROL_TURISTA)->group(function () {
        Route::get('/mi-cuenta', [CuentaTuristaController::class, 'show'])->name('turista.cuenta');
        Route::put('/mi-cuenta', [CuentaTuristaController::class, 'update'])->name('turista.cuenta.update');
    });
    $rutaLegible(['GET', 'HEAD'], '/mi-panel', '/dashboard', fn (Request $request) => redirect($request->user()->rutaInicio()))->name('dashboard');

    // Panel administrativo: administrador y personal de operaciones.
    Route::prefix('admin')->name('admin.')->middleware('rol:'.User::ROL_ADMIN.','.User::ROL_OPERACIONES)->group(function () use ($rutaLegible) {
        Route::get('/mis-reservas', [ConsultaReservaController::class, 'historial'])->name('reservas.historial');
        $rutaLegible(['GET', 'HEAD'], '/panel', '/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

        // Operaciones y salidas (M04)
        Route::get('/operaciones', [SalidasController::class, 'index'])->name('operaciones');
        Route::post('/salidas', [SalidasController::class, 'store'])->name('salidas.store');
        $rutaLegible(['GET', 'HEAD'], '/salidas/{salida}/editar', '/salidas/{salida}', [SalidasController::class, 'edit'])->name('salidas.edit');
        Route::put('/salidas/{salida}', [SalidasController::class, 'update'])->name('salidas.update');
        Route::post('/salidas/{salida}/reprogramar', [SalidasController::class, 'reprogramar'])->name('salidas.reprogramar');
        Route::post('/salidas/{salida}/cancelar', [SalidasController::class, 'cancelar'])->name('salidas.cancelar');

        // Personal y recursos (M03)
        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');
        Route::post('/inventario/tipos/{tipo?}', [InventarioController::class, 'guardarTipo'])->name('inventario.tipo');
        Route::post('/inventario/tipos/{tipo}/items', [InventarioController::class, 'guardarItem'])->name('inventario.item');
        Route::post('/inventario/tours/{tour}', [InventarioController::class, 'equipoTour'])->name('inventario.tour');
        Route::get('/transporte', [FlotaController::class, 'index'])->name('transporte');
        Route::post('/transporte', [FlotaController::class, 'store'])->middleware('rol:'.User::ROL_ADMIN)->name('transporte.store');
        Route::put('/transporte/{vehiculo}', [FlotaController::class, 'update'])->name('transporte.update');
        // Catálogo (M02): tours, categorías, galería y trazado
        Route::get('/tours', [CatalogoAdminController::class, 'index'])->name('tours');
        Route::get('/catalogo/tours/nuevo', [CatalogoAdminController::class, 'create'])->name('catalogo.create');
        Route::post('/catalogo/tours', [CatalogoAdminController::class, 'store'])->name('catalogo.store');
        $rutaLegible(['GET', 'HEAD'], '/catalogo/tours/{tour}/editar', '/catalogo/tours/{tour}', [CatalogoAdminController::class, 'edit'])->name('catalogo.edit');
        Route::put('/catalogo/tours/{tour}', [CatalogoAdminController::class, 'update'])->name('catalogo.update');
        Route::delete('/catalogo/tours/{tour}', [CatalogoAdminController::class, 'destroy'])->name('catalogo.destroy');
        Route::post('/catalogo/tours/{tour}/fotos', [CatalogoAdminController::class, 'subirFotos'])->name('catalogo.fotos');
        Route::post('/catalogo/tours/{tour}/fotos/orden', [CatalogoAdminController::class, 'ordenarFotos'])->name('catalogo.fotos.orden');
        Route::delete('/catalogo/tours/{tour}/fotos/{foto}', [CatalogoAdminController::class, 'eliminarFoto'])->name('catalogo.fotos.eliminar');
        Route::post('/catalogo/tours/{tour}/ruta', [CatalogoAdminController::class, 'guardarRuta'])->name('catalogo.ruta');
        Route::post('/catalogo/categorias/{categoria?}', [CatalogoAdminController::class, 'guardarCategoria'])->name('catalogo.categoria');
        Route::delete('/catalogo/categorias/{categoria}', [CatalogoAdminController::class, 'eliminarCategoria'])->name('catalogo.categoria.eliminar');
        Route::get('/personal', [PersonalController::class, 'index'])->name('personal');
        Route::post('/personal/{tipo}', [PersonalController::class, 'store'])->middleware('rol:'.User::ROL_ADMIN)->whereIn('tipo', ['guia', 'chofer'])->name('personal.store');
        Route::get('/personal/{tipo}/{id}', [PersonalController::class, 'edit'])->whereIn('tipo', ['guia', 'chofer'])->name('legacy.personal.edit');
        Route::get('/personal/{tipo}/{id}/editar', [PersonalController::class, 'edit'])->whereIn('tipo', ['guia', 'chofer'])->name('personal.edit');
        Route::put('/personal/{tipo}/{id}', [PersonalController::class, 'update'])->whereIn('tipo', ['guia', 'chofer'])->name('personal.update');
        Route::post('/personal/{tipo}/{id}/estado', [PersonalController::class, 'estado'])->whereIn('tipo', ['guia', 'chofer'])->name('personal.estado');
        Route::get('/reservas', [ReservasAdminController::class, 'index'])->name('reservas');
        Route::get('/reservas/{reserva}', [ReservasAdminController::class, 'show'])->name('reservas.show');
        Route::post('/reservas/{reserva}/menores/{participante}/verificar', [ReservasAdminController::class, 'verificarMenor'])->name('reservas.menor.verificar');
        Route::post('/reservas/{reserva}/cancelar', [ReservasAdminController::class, 'cancelar'])->name('reservas.cancelar');

        Route::get('/finanzas', [FinanzasController::class, 'index'])->middleware('rol:'.User::ROL_ADMIN)->name('finanzas');

        // Chatbot: atención humana de conversaciones derivadas (RF-31)
        $rutaLegible(['GET', 'HEAD'], '/asistente-turistico', '/chatbot', [ChatbotAdminController::class, 'index'])->name('chatbot');
        $rutaLegible(['GET', 'HEAD'], '/asistente-turistico/conversaciones/{conversacion}', '/chatbot/{conversacion}', [ChatbotAdminController::class, 'show'])->name('chatbot.show');
        $rutaLegible(['POST'], '/asistente-turistico/conversaciones/{conversacion}/responder', '/chatbot/{conversacion}/responder', [ChatbotAdminController::class, 'responder'])->name('chatbot.responder');
        $rutaLegible(['POST'], '/asistente-turistico/conversaciones/{conversacion}/cerrar', '/chatbot/{conversacion}/cerrar', [ChatbotAdminController::class, 'cerrar'])->name('chatbot.cerrar');
        Route::post('/finanzas/reservas/{reserva}/efectivo', [FinanzasController::class, 'efectivo'])->name('finanzas.efectivo');
        Route::get('/finanzas/comprobantes/{comprobante}.pdf', [FinanzasController::class, 'comprobantePdf'])->name('finanzas.comprobante');
        Route::post('/finanzas/comprobantes/{comprobante}/anular', [FinanzasController::class, 'anular'])
            ->middleware('rol:'.User::ROL_ADMIN)->name('finanzas.anular');
        Route::get('/analitica', [DashboardController::class, 'analitica'])->middleware('rol:'.User::ROL_ADMIN)->name('analitica');

        // Módulo 07 — Investigación Operativa II: paneles de teoría de colas y cadenas de Markov
        Route::view('/io', 'admin.io.index')->name('legacy.io.index');
        Route::view('/investigacion-operativa', 'admin.io.index')->name('io.index');
        $rutaLegible(['GET', 'HEAD'], '/investigacion-operativa/colas', '/io/colas', [AnalisisColasController::class, 'index'])->name('io.colas');
        $rutaLegible(['GET', 'HEAD'], '/investigacion-operativa/ciclo-vida', '/io/ciclo-vida', [CicloVidaClienteController::class, 'index'])->name('io.ciclo-vida');
        $rutaLegible(['GET', 'HEAD'], '/investigacion-operativa/parametros', '/io/parametros', [IoGestionController::class, 'parametros'])->name('io.parametros');
        $rutaLegible(['POST'], '/investigacion-operativa/parametros/{tour}', '/io/parametros/{tour}', [IoGestionController::class, 'guardarParametros'])->name('io.parametros.store');
        $rutaLegible(['GET', 'HEAD'], '/investigacion-operativa/propuestas', '/io/propuestas', [IoGestionController::class, 'propuestas'])->name('io.propuestas');
        $rutaLegible(['POST'], '/investigacion-operativa/propuestas', '/io/propuestas', [IoGestionController::class, 'generarPropuestas'])->name('io.propuestas.generar');
        $rutaLegible(['POST'], '/investigacion-operativa/propuestas/{propuesta}/aplicar', '/io/propuestas/{propuesta}/aplicar', [IoGestionController::class, 'aplicarPropuesta'])->name('io.propuestas.aplicar');
        $rutaLegible(['POST'], '/investigacion-operativa/propuestas/{propuesta}/rechazar', '/io/propuestas/{propuesta}/rechazar', [IoGestionController::class, 'rechazarPropuesta'])->name('io.propuestas.rechazar');
        $rutaLegible(['GET', 'HEAD'], '/investigacion-operativa/eoq', '/io/eoq', [IoGestionController::class, 'eoq'])->name('io.eoq');
        $rutaLegible(['POST'], '/investigacion-operativa/eoq/alertas', '/io/eoq/alertas', [IoGestionController::class, 'alertasEoq'])->name('io.eoq.alertas');

        // Bitácora de auditoría: solo administrador.
        Route::middleware('rol:'.User::ROL_ADMIN)->group(function () {
            Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria');
            Route::get('/auditoria/registros/{registro}', [AuditoriaController::class, 'show'])->name('auditoria.show');
            Route::get('/auditoria/notificaciones', [AuditoriaController::class, 'notificaciones'])->name('auditoria.notificaciones');
            Route::post('/auditoria/notificaciones/{registro}/reintentar', [AuditoriaController::class, 'reintentar'])->name('auditoria.reintentar');
            Route::get('/auditoria/usuarios', [AuditoriaController::class, 'usuarios'])->name('auditoria.usuarios');
            Route::put('/auditoria/usuarios/{usuario}', [AuditoriaController::class, 'actualizarUsuario'])->name('auditoria.usuarios.update');
        });
    });

    // Operación en campo: guías y choferes (y supervisión de operaciones/admin).
    Route::middleware('rol:'.User::ROL_GUIA.','.User::ROL_CHOFER.','.User::ROL_OPERACIONES.','.User::ROL_ADMIN)->group(function () use ($rutaLegible) {
        $rutaLegible(['GET', 'HEAD'], '/guias/sin-conexion', '/guias/offline', fn () => response()->file(public_path('checkin-offline.html')))->name('guias.offline');
        $rutaLegible(['GET', 'HEAD'], '/guias/sin-conexion/sesion', '/guias/offline/sesion', [CheckinController::class, 'sesionOffline'])->name('guias.offline.sesion');
        $rutaLegible(['GET', 'HEAD'], '/guias/mis-salidas', '/guias/checkin', [CheckinController::class, 'index'])->name('guias.checkin');
        Route::get('/guias/salidas/{salida}', [CheckinController::class, 'show'])->name('guias.salida');
        Route::get('/guias/salidas/{salida}/manifiesto', [CheckinController::class, 'manifiestoJson'])->name('guias.manifiesto');
        Route::post('/guias/salidas/{salida}/validar', [CheckinController::class, 'validar'])->middleware('throttle:120,1')->name('guias.validar');
        Route::post('/guias/salidas/{salida}/sincronizar', [CheckinController::class, 'sincronizar'])->name('guias.sincronizar');
        Route::post('/guias/salidas/{salida}/iniciar', [CheckinController::class, 'iniciar'])->name('guias.iniciar');
        Route::post('/guias/salidas/{salida}/finalizar', [CheckinController::class, 'finalizar'])->name('guias.finalizar');
    });
});

/*
|--------------------------------------------------------------------------
| Maquetas de frontend (solo en entorno local)
|--------------------------------------------------------------------------
| Vistas con datos de ejemplo para revisar el diseño mientras se construye el
| backend. No se registran fuera de APP_ENV=local.
*/

if (app()->environment('local')) {
    // Motor de Reservas (Módulo 05). Aceptan GET y POST para recorrer el flujo sin backend.
    Route::prefix('reservas/preview')->name('reservas.preview.')->group(function () {
        foreach (['salida', 'participantes', 'equipo', 'ficha', 'resumen', 'confirmada', 'consultar', 'estado'] as $vista) {
            Route::match(['get', 'post'], $vista, fn () => view("reservas.$vista"))->name($vista);
        }
    });

    // Paneles de IO II (Módulo 07) con datos de maqueta, sin iniciar sesión.
    Route::prefix('admin/io/preview')->name('admin.io.preview.')->group(function () {
        Route::get('colas', fn (Request $r) => view('admin.io.colas', app(IoDemoData::class)->colas(
            $r->integer('tour') ?: null, $r->filled('c') ? $r->integer('c') : null, $r->filled('k') ? $r->integer('k') : null,
            $r->filled('lambda') ? (float) $r->input('lambda') : null) + ['formAction' => url('admin/io/preview/colas')]))->name('colas');
        Route::get('ciclo-vida', fn (Request $r) => view('admin.io.ciclo-vida', app(IoDemoData::class)->cicloVida(
            $r->input('inicio')) + ['formAction' => url('admin/io/preview/ciclo-vida')]))->name('ciclo-vida');
    });
}
