<?php

use App\Http\Controllers\HerramientasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MaterialesController;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Usuarios;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\EnsambladoController;
use App\Http\Controllers\PrestamoController;

// Rutas publicas
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/registro', [LoginController::class, 'create'])->name('registro');
Route::post('/registro/store', [LoginController::class, 'store'])->name('registro.store');
Route::view('/aviso-de-privacidad', 'aviso_de_privacidad.aviso')->name('aviso.privacidad');

// Rutas de herramientas
Route::prefix('herramientas')->group(function () {
    Route::get('/herramientas/buscar', [HerramientasController::class, 'buscar'])->name('herramientas.buscar');
    Route::get('/', [App\Http\Controllers\HerramientasController::class, 'index'])->name('herramientas.index');
    Route::get('/registro', [App\Http\Controllers\HerramientasController::class, 'create'])->name('herramientas.create');
    Route::post('/store', [App\Http\Controllers\HerramientasController::class, 'store'])->name('herramientas.store');
    Route::get('/{id_herramienta}/edit', [App\Http\Controllers\HerramientasController::class, 'edit'])->name('herramientas.edit');
    Route::post('/{id_herramienta}/actualizar', [HerramientasController::class, 'update'])->name('herramientas.update');
    Route::post('/show', [App\Http\Controllers\HerramientasController::class, 'show'])->name('herramientas.show');
    Route::delete('/destroy/{id_herramienta}', [App\Http\Controllers\HerramientasController::class, 'destroy'])->name('herramientas.destroy');
    Route::get('/listado', [App\Http\Controllers\HerramientasController::class, 'listado'])->name('herramientas.listado');
});

//Rutas de materiales
Route::prefix('materiales')->group(function () {
    Route::get('/', [MaterialesController::class, 'index'])->name('materiales.index');
    Route::get('/registro', [MaterialesController::class, 'create'])->name('materiales.create');
    Route::post('/store', [MaterialesController::class, 'store'])->name('materiales.store');
    Route::get('/{id_material}/edit', [MaterialesController::class, 'edit'])->name('materiales.edit');
    Route::post('/{id_material}/actualizar', [MaterialesController::class, 'update'])->name('materiales.update');
    Route::post('/show', [MaterialesController::class, 'show'])->name('materiales.show');
    Route::delete('/destroy/{id_material}', [MaterialesController::class, 'destroy'])->name('materiales.destroy');
    Route::get('/listado', [MaterialesController::class, 'listado'])->name('materiales.listado');
});


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

    Route::get('/prestamos', [PrestamoController::class, 'index'])->name('prestamos.index');
    Route::get('/prestamos/{id}', [PrestamoController::class, 'show'])->name('prestamos.show');

    Route::get('/ensamblados',              [EnsambladoController::class, 'index'])->name('ensamblados.index');
    Route::get('/ensamblados/crear',        [EnsambladoController::class, 'create'])->name('ensamblados.create');
    Route::post('/ensamblados/store',       [EnsambladoController::class, 'store'])->name('ensamblados.store');
    Route::get('/ensamblados/{id}/json',    [EnsambladoController::class, 'show'])->name('ensamblados.show');
    Route::get('/ensamblados/listado',      [EnsambladoController::class, 'listado'])->name('ensamblados.listado');
    Route::get('ensamblados/ingresar', [EnsambladoController::class, 'ingresar'])->name('ensamblados.ingresar');
    Route::post('ensamblados/ingresar', [EnsambladoController::class, 'ingresarStore'])->name('ensamblados.ingresar.store');

    Route::prefix('carrito')->name('carrito.')->group(function () {
        Route::get('/',                         [CarritoController::class, 'index'])->name('index');
        Route::post('/agregar/herramienta',     [CarritoController::class, 'agregarHerramienta'])->name('agregar.herramienta');
        Route::post('/agregar/material',        [CarritoController::class, 'agregarMaterial'])->name('agregar.material');
        Route::delete('/eliminar/{rowId}',      [CarritoController::class, 'eliminar'])->name('eliminar');
        Route::delete('/vaciar',                [CarritoController::class, 'vaciar'])->name('vaciar');
        Route::post('/confirmar',               [CarritoController::class, 'confirmar'])->name('confirmar');
    });

    // ─── Devoluciones ─────────────────────────────────────────────────────────────
    Route::prefix('devoluciones')->name('devoluciones.')->group(function () {
        Route::get('/',         [DevolucionController::class, 'index'])->name('index');
        Route::post('/buscar',  [DevolucionController::class, 'buscarPrestamo'])->name('buscar');
        Route::post('/store',   [DevolucionController::class, 'store'])->name('store');
    });

    // ─── Ensamblados ──────────────────────────────────────────────────────────────
    Route::prefix('ensamblados')->name('ensamblados.')->group(function () {
        Route::get('/',       [EnsambladoController::class, 'index'])->name('index');
        Route::post('/store', [EnsambladoController::class, 'store'])->name('store');
    });



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

// Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
