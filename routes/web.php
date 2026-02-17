<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/registro', [LoginController::class, 'create'])->name('registro');  
Route::post('/registro', [LoginController::class, 'store'])->name('registro.store');

//rutas de autenticacion
Route::middleware('auth:usuarios')->group(function () {

    //inicio
    Route::get('/inicio', function () {
        return view('inicio.inicio');
    })->name('inicio');


    //cerrar sesion
    Route::post('/logout', [App\Http\Controllers\LoginController::class, 'logout'])->name('logout');


});




Route::get('/', function () {
    return view('welcome');
});