<?php

use App\Http\Controllers\HerramientasController;
use App\Http\Controllers\EmpleadosController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MaterialesController;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
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

// Google Auth
Route::get('auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('auth.google');

Route::get('/auth/google/callback', function () {
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Todo pasa por la API — sin tocar BD directamente
        $response = Http::post(config('services.api.url') . '/auth/google', [
            'nombre'    => $googleUser->getName(),
            'correo'    => $googleUser->getEmail(),
            'usuario'   => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'imagen'    => $googleUser->getAvatar(),
        ]);

        if (!$response->successful() || !$response->json()['success']) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Error al iniciar sesión con Google.']);
        }

        $data = $response->json()['data'];

        session([
            'api_token' => $data['token'],
            'usuario'   => $data['usuario'],
        ]);

        request()->session()->regenerate();

        return redirect()->route('inicio');
    } catch (\Exception $e) {
        return redirect()->route('login')
            ->withErrors(['error' => 'Error: ' . $e->getMessage()]);
    }
});


//rutas protegidas (requieren token de API en sesión)
Route::middleware('sesion.api')->group(function () {

    Route::get('/inicio', function () {
        return view('inicio.inicio');
    })->name('inicio');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Prestamos
    Route::get('/prestamos',      [PrestamoController::class, 'index'])->name('prestamos.index');
    Route::get('/prestamos/{id}', [PrestamoController::class, 'show'])->name('prestamos.show');
    
    //perfil
    Route::get('/perfil', [UsuariosController::class, 'perfil'])->name('perfil');
    Route::post('/perfil/actualizar', [UsuariosController::class, 'actualizarPerfil'])->name('perfil.actualizar');

    // Herramientas
    Route::prefix('herramientas')->group(function () {
        Route::get('/',                 [HerramientasController::class, 'index'])->name('herramientas.index');
        Route::get('/listado',          [HerramientasController::class, 'listado'])->name('herramientas.listado');
        Route::get('/buscar',           [HerramientasController::class, 'buscar'])->name('herramientas.buscar');
        Route::get('/registro',         [HerramientasController::class, 'create'])->name('herramientas.create');
        Route::post('/store',           [HerramientasController::class, 'store'])->name('herramientas.store');
        Route::get('/{id}/edit',        [HerramientasController::class, 'edit'])->name('herramientas.edit');
        Route::post('/{id}/actualizar', [HerramientasController::class, 'update'])->name('herramientas.update');
        Route::delete('/destroy/{id}',  [HerramientasController::class, 'destroy'])->name('herramientas.destroy');
    });

    // Materiales
    Route::prefix('materiales')->group(function () {
        Route::get('/',                 [MaterialesController::class, 'index'])->name('materiales.index');
        Route::get('/listado',          [MaterialesController::class, 'listado'])->name('materiales.listado');
        Route::get('/buscar',           [MaterialesController::class, 'buscar'])->name('materiales.buscar');
        Route::get('/registro',         [MaterialesController::class, 'create'])->name('materiales.create');
        Route::post('/store',           [MaterialesController::class, 'store'])->name('materiales.store');
        Route::get('/{id}/edit',        [MaterialesController::class, 'edit'])->name('materiales.edit');
        Route::post('/{id}/actualizar', [MaterialesController::class, 'update'])->name('materiales.update');
        Route::delete('/destroy/{id}',  [MaterialesController::class, 'destroy'])->name('materiales.destroy');
    });

    // Empleados
    Route::prefix('empleados')->group(function () {
        Route::get('/',                 [EmpleadosController::class, 'index'])->name('empleados.index');
        Route::get('/create',           [EmpleadosController::class, 'create'])->name('empleados.create');
        Route::post('/store',           [EmpleadosController::class, 'store'])->name('empleados.store');
        Route::get('/{id}/edit',        [EmpleadosController::class, 'edit'])->name('empleados.edit');
        Route::post('/{id}/actualizar', [EmpleadosController::class, 'update'])->name('empleados.update');
        Route::delete('/destroy/{id}',  [EmpleadosController::class, 'destroy'])->name('empleados.destroy');
    });

    // Usuarios
    Route::prefix('usuarios')->group(function () {
        Route::get('/',                 [UsuariosController::class, 'index'])->name('usuarios.index');
        Route::get('/create',           [UsuariosController::class, 'create'])->name('usuarios.create');
        Route::post('/store',           [UsuariosController::class, 'store'])->name('usuarios.store');
        Route::get('/{id}',             [UsuariosController::class, 'show'])->name('usuarios.show');
        Route::get('/{id}/edit',        [UsuariosController::class, 'edit'])->name('usuarios.edit');
        Route::post('/{id}/actualizar', [UsuariosController::class, 'update'])->name('usuarios.update');
        Route::delete('/destroy/{id}',  [UsuariosController::class, 'destroy'])->name('usuarios.destroy');
    });

    // Carrito
    Route::prefix('carrito')->name('carrito.')->group(function () {
        Route::get('/',                     [CarritoController::class, 'index'])->name('index');
        Route::post('/agregar/herramienta', [CarritoController::class, 'agregarHerramienta'])->name('agregar.herramienta');
        Route::post('/agregar/material',    [CarritoController::class, 'agregarMaterial'])->name('agregar.material');
        Route::delete('/eliminar/{rowId}',  [CarritoController::class, 'eliminar'])->name('eliminar');
        Route::delete('/vaciar',            [CarritoController::class, 'vaciar'])->name('vaciar');
        Route::post('/confirmar',           [CarritoController::class, 'confirmar'])->name('confirmar');
    });

    // Devoluciones
    Route::prefix('devoluciones')->name('devoluciones.')->group(function () {
        Route::get('/',        [DevolucionController::class, 'index'])->name('index');
        Route::post('/buscar', [DevolucionController::class, 'buscarPrestamo'])->name('buscar');
        Route::post('/store',  [DevolucionController::class, 'store'])->name('store');
    });

    // Ensamblados
    Route::prefix('ensamblados')->name('ensamblados.')->group(function () {
        Route::get('/',          [EnsambladoController::class, 'index'])->name('index');
        Route::get('/crear',     [EnsambladoController::class, 'create'])->name('create');
        Route::post('/store',    [EnsambladoController::class, 'store'])->name('store');
        Route::get('/{id}/json', [EnsambladoController::class, 'show'])->name('show');
        Route::get('/ingresar',  [EnsambladoController::class, 'ingresar'])->name('ingresar');
        Route::post('/ingresar', [EnsambladoController::class, 'ingresarStore'])->name('ingresar.store');
    });
});


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/aviso-de-privacidad/pdf', function () {
    $pdf = Pdf::loadView('aviso_de_privacidad.pdf');
    return $pdf->download('aviso_de_privacidad.pdf');
})->name('aviso.privacidad.pdf');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
