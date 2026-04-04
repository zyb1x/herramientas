<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PrestamoController extends Controller
{
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://127.0.0.1:8000/api');
    }

    // GET /prestamos
    public function index()
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/prestamos");

        $todosPrestamos = $response->successful() ? $response->json()['data'] : [];

        // Filtrar solo los préstamos del usuario autenticado
        $idUsuario = session('usuario')['id'];

        $prestamos = array_values(array_filter($todosPrestamos, function ($p) use ($idUsuario) {
            return $p['id_usuario'] === $idUsuario;
        }));

        return view('pedidos.prestamos', compact('prestamos'));
    }

    // GET /prestamos/{id}
    public function show(int $id)
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/prestamos/{$id}");

        if (!$response->successful()) {
            return redirect()->route('prestamos.index')
                ->with('error', 'Préstamo no encontrado.');
        }

        $prestamo = $response->json()['data'];

        // Verificar que el préstamo pertenece al usuario autenticado
        $idUsuario = session('usuario')['id'];

        if ($prestamo['id_usuario'] !== $idUsuario) {
            return redirect()->route('prestamos.index')
                ->with('error', 'No tienes permiso para ver este préstamo.');
        }

        return view('pedidos.detalle_prestamo', compact('prestamo'));
    }
}
