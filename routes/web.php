<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\DatabaseController;


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
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
    Route::get('/registros',[DatabaseController::class, 'exRegistrosShow'])->name('registros');
    Route::get('/campos',[DatabaseController::class, 'exCamposShow'])->name('campos');
    // Route::post('/registrosResult',[DatabaseController::class, 'exRegistrosResult'])->name('registrosResult');
    Route::post('/registrosResult',[DatabaseController::class, 'prueba'])->name('registrosResult');
    Route::post('/connect', [DatabaseController::class, 'connect'])->name('connect');

});
    // // Ruta para procesar la conexión

    // Route::get('/ga', [DatabaseController::class, 'instanciar']);


    // Ruta para desconectarse






Route::get('/', function () {
    return view('login2');
});

// Route::get('login', function () {
//     return view('login2');
// })->name('login');


Route::get('/x', function () {
    return view('layout.layout');
})->name('x');

Route::get('return', function () {
    return view('usuarios.index');
});

// Route::POST('login', function () {
//     return view('login');
// })->name('login');

// Route::get('login', function () {
//     return view('login');
// })->name('password.request');