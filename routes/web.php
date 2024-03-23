<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UsersController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/inicio-sesion', [LoginController::class, 'login'])->name('logearse');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::resource('/usuarios', UsersController::class);


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