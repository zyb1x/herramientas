<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EmpleadosController;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Empleados;
use App\Models\Usuarios;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;



Route::get('inicio', function () {
    return view('inicio.inicio');
})->middleware(['auth', 'verified'])->name('inicio');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/registro', [LoginController::class, 'create'])->name('registro');
Route::post('/registro/store', [LoginController::class, 'store'])->name('registro.store');
Route::view('/aviso-de-privacidad', 'aviso_de_privacidad.aviso')->name('aviso.privacidad');

Route::get('auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('auth.google');

Route::get('/auth/google/callback', function () {
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $usuario = Usuarios::where('correo', $googleUser->getEmail())->first();

        if (!$usuario) {
            $usuario = Usuarios::create([
                'nombre'     => $googleUser->getName(),
                'correo' => $googleUser->getEmail(),   // valor por defecto
                'usuario'     => $googleUser->getEmail(),
                'contrasena' => Hash::make(Str::random(16)),
                'rol'     => 'Almacenista',// valor por defecto
                'turno'      => 'Matutino',// valor por defecto
            ]);
        }
        Auth::guard('usuarios')->login($usuario);
        return redirect()->route('inicio');
    } catch (Exception $e) {
        dd($e->getMessage());
        return redirect()->route('login')->withErrors(['error' => 'Error al iniciar sesión con Google. Por favor, inténtalo de nuevo.']);
    }
});


Route::middleware('auth:usuarios')->group(function () {

    
    Route::get('/inicio', function () {
        return view('inicio.inicio');
    })->name('inicio');


    
    Route::post('/logout', [App\Http\Controllers\LoginController::class, 'logout'])->name('logout');
});




Route::get('/', function () {
    return view('welcome');
});
