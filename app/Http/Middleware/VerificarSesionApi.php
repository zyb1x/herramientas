<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarSesionApi
{
    // Verifica que haya un token de API en sesion, sino, redirige al login
    public function handle(Request $request, Closure $next): mixed
    {
        if (!session('api_token') || !session('usuario')) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Debes iniciar sesión para continuar.']);
        }

        return $next($request);
    }
}