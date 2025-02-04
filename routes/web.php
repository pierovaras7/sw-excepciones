<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\DatabaseController;



Route::post('/inicio-sesion', [LoginController::class, 'login'])->name('logearse');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Rutas protegidas que requieren autenticación

    Route::resource('/usuarios', UsersController::class);
    Route::get('/usuarios/{id}/eliminar', [UsersController::class, 'destroy'])->name('usuarios.eliminar');

    Route::get('/informacion', [DatabaseController::class, 'infodb'])->name('infodb');



    // Ruta para mostrar el formulario de conexión
    Route::get('/connect', [DatabaseController::class, 'showConnectForm'])->name('conexion');
    //Route::get('/conexiones', [DatabaseController::class, 'conexiones'])->name('conexiones');
    Route::get('/disconnect', [DatabaseController::class, 'disconnect'])->name('disconnect');
    Route::get('/cargar-infos/{table}', [DatabaseController::class, 'load'])->name('cargarInfo');
    Route::get('/cargar-info/{table}', [DatabaseController::class, 'loadInfo'])->name('cargarInfoTable');
    Route::get('/cargar-info-rel/{table}', [DatabaseController::class, 'loadInfoRel'])->name('cargarInfoTableRel');
    Route::get('/obtenerTablas', [DatabaseController::class, 'obtenerTablas'])->name('obtenerTablas');

    Route::get('/registros',[DatabaseController::class, 'exRegistrosShow'])->name('registros');
    Route::get('/campos',[DatabaseController::class, 'exCamposShow'])->name('campos');
    Route::get('/tablas',[DatabaseController::class, 'exTablasShow'])->name('tablas');
    Route::get('/historial',[DatabaseController::class, 'viewHistorial'])->name('historial');

    // Route::post('/registrosResult',[DatabaseController::class, 'exRegistrosResult'])->name('registrosResult');
    Route::post('/registrosResult',[DatabaseController::class, 'resultSecuencialidad'])->name('registrosResult');
    Route::post('/camposResult',[DatabaseController::class, 'resultCampos'])->name('camposResult');
    Route::post('/tablasResult',[DatabaseController::class, 'resultTablas'])->name('tablasResult');
    Route::get('/filtrarHistoriales/{tipo}',[DatabaseController::class, 'filtrarHistorialPor'])->name('filtrarHistorialPor');

    /// Reportes
    Route::post('/reporteSecuencialidad',[DatabaseController::class,'reporteSec'])->name('reporteS');
    Route::post('/reporteUnicidad',[DatabaseController::class,'reporteUni'])->name('reporteU');
    Route::post('/reporteCampos',[DatabaseController::class,'reporteCam'])->name('reporteC');
    Route::post('/reporteTablas',[DatabaseController::class,'reporteTab'])->name('reporteT');
    Route::post('/reporteSQL',[DatabaseController::class,'reporteSQL'])->name('reporteSQL');
 



    Route::get('/scriptsql',[DatabaseController::class, 'vistaConsultaSQL'])->name('consultaSQL');
    Route::POST('/consultar',[DatabaseController::class, 'consultar'])->name('consultar');


    Route::post('/connect', [DatabaseController::class, 'connect'])->name('connect');


});

Route::get('/', function () {
    return view('login2');
})->name('login');








Route::get('/x', function () {
    return view('pdfs.pdfRegistro');
})->name('x');

Route::get('return', function () {
    return view('usuarios.index');
});

