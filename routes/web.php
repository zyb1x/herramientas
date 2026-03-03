<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EmpleadosController;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Empleados;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;



Route::get('inicio', function () {
    return view('inicio.inicio');
})->middleware(['auth', 'verified'])->name('inicio');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/registro', [LoginController::class, 'create'])->name('registro');
Route::post('/registro/store', [LoginController::class, 'store'])->name('registro.store');

Route::get('auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('auth.google');

Route::get('/auth/google/callback', function () {
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $empleado = Empleados::where('correo', $googleUser->getEmail())->first();

        if (!$empleado) {
            $empleado = Empleados::create([
                'nombre'     => $googleUser->getName(),
                'apellido_p' => 'Google',   // valor por defecto
                'correo'     => $googleUser->getEmail(),
                'contrasena' => Hash::make(Str::random(16)),
                'puesto'     => 'Empleado',
                'turno'      => 'Matutino',
            ]);
        }
        Auth::guard('empleados')->login($empleado);
        return redirect()->route('inicio');
    } catch (Exception $e) {
        dd($e->getMessage());
        return redirect()->route('login')->withErrors(['error' => 'Error al iniciar sesión con Google. Por favor, inténtalo de nuevo.']);
    }
});

//rutas de autenticacion
Route::middleware('auth:empleados')->group(function () {

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
