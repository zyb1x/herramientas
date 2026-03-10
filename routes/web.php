<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Usuarios;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;


// Rutas publicas
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/registro', [LoginController::class, 'create'])->name('registro');
Route::post('/registro/store', [LoginController::class, 'store'])->name('registro.store');
Route::view('/aviso-de-privacidad', 'aviso_de_privacidad.aviso')->name('aviso.privacidad');

// Google Auth
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
                'correo' => $googleUser->getEmail(),
                'usuario'     => $googleUser->getEmail(),
                'contrasena' => Hash::make(Str::random(16)),
                'rol'     => 'Almacenista',
                'turno'      => 'Matutino',
                'imagen'     => $googleUser->getAvatar(),
            ]);
        } else {
            // Actualizar avatar por si cambia en Google
            $usuario->update(['imagen' => $googleUser->getAvatar()]);
        }

        Auth::guard('usuarios')->login($usuario);
        return redirect()->route('inicio');
    } catch (\Exception $e) {
        return redirect()->route('login')
            ->withErrors(['error' => 'Error al iniciar sesión con Google: ' . $e->getMessage()]);
    }
});

//rutas protegidas
Route::middleware('auth:usuarios')->group(function () {


    Route::get('/inicio', function () {
        return view('inicio.inicio');
    })->name('inicio');

    Route::post('/logout', [App\Http\Controllers\LoginController::class, 'logout'])->name('logout');
});


Route::get('/', function () {
    return redirect()->route('login');
});


Route::get('/aviso-de-privacidad/pdf', function () {
    $pdf = Pdf::loadView('aviso_de_privacidad.pdf'); // <- busca aviso_de_privacidad/pdf.blade.php
    return $pdf->download('aviso_de_privacidad.pdf');
})->name('aviso.privacidad.pdf');