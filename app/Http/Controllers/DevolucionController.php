<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class DevolucionController extends Controller
{
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://127.0.0.1:8000/api');
    }

    // GET /devoluciones
    public function index(Request $request)
    {
        // traer todos los prestamos de la API uwuww
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/prestamos");

        $todosPrestamos = $response->successful() ? $response->json()['data'] : [];

        // muesta solo los que tienen herramientas pendientes a devolucion
        $prestamos = array_filter($todosPrestamos, function ($p) {
            if (!in_array($p['estatus_general'], ['Activo', 'Devuelto Parcial'])) {
                return false;
            }
            foreach ($p['detalles'] as $detalle) {
                if ($detalle['id_herramienta'] && $detalle['estatus_articulo'] === 'Prestado') {
                    return true;
                }
            }
            return false;
        });

        $prestamos = array_values($prestamos);

        if (!$request->filled('id_prestamo')) {
            return view('devoluciones.index', compact('prestamos'));
        }

        // Buscar prestamo específico
        $prestamo = $this->obtenerPrestamoPendiente($request->id_prestamo);

        if (!$prestamo || empty($prestamo['detalles'])) {
            return view('devoluciones.index', compact('prestamos'))
                ->with('error', 'Este préstamo no tiene herramientas pendientes de devolución.');
        }

        return view('devoluciones.index', compact('prestamo', 'prestamos'));
    }

    // POST /devoluciones/buscar
    public function buscarPrestamo(Request $request)
    {
        $request->validate([
            'id_prestamo' => 'required|integer',
        ], [
            'id_prestamo.required' => 'Selecciona el préstamo.',
        ]);

        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/prestamos");

        $todosPrestamos = $response->successful() ? $response->json()['data'] : [];

        $prestamos = array_filter($todosPrestamos, function ($p) {
            if (!in_array($p['estatus_general'], ['Activo', 'Devuelto Parcial'])) {
                return false;
            }
            foreach ($p['detalles'] as $detalle) {
                if ($detalle['id_herramienta'] && $detalle['estatus_articulo'] === 'Prestado') {
                    return true;
                }
            }
            return false;
        });

        $prestamos = array_values($prestamos);

        $prestamo = $this->obtenerPrestamoPendiente($request->id_prestamo);

        if (!$prestamo || empty($prestamo['detalles'])) {
            return redirect()->route('devoluciones.index')
                ->with('error', 'Este préstamo no tiene herramientas pendientes de devolución.')
                ->withInput();
        }

        return view('devoluciones.index', compact('prestamo', 'prestamos'));
    }


    // POST /devoluciones/store
    public function store(Request $request)
    {
        $request->validate([
            'id_prestamo'                        => 'required|integer',
            'devoluciones'                       => 'required|array',
            'devoluciones.*.id_herramienta'      => 'required|integer',
            'devoluciones.*.cantidad'            => 'required|integer|min:1',
            'devoluciones.*.estatus_herramienta' => 'required|in:Nuevo,Buen Estado,Danado,Reparacion',
        ], [
            'id_prestamo.required' => 'Selecciona un préstamo.',
        ]);

        // Mandar la devoluion al sr API 
        $response = Http::withToken(session('api_token'))
            ->post("{$this->apiUrl}/devoluciones", [
                'id_prestamo'  => $request->id_prestamo,
                'devoluciones' => $request->devoluciones,
            ]);

        $data = $response->json();

        // ← Agrega esto para ver qué devuelve la API
        if (!$data) {
            return redirect()->route('devoluciones.index')
                ->with('error', 'Error de conexión con la API. Status: ' . $response->status());
        }

        if (!$data['success']) {
            return redirect()->route('devoluciones.index')
                ->with('error', $data['mensaje'] . ' — ' . ($data['error'] ?? 'sin detalle'));
        }

        $registradas = count($request->devoluciones);

        return redirect()->route('devoluciones.index')
            ->with('success', "Devolución registrada correctamente. ({$registradas} herramienta(s))");
    }

    // Método auxiliar — obtiene un préstamo con detalles pendientes de devolución
    private function obtenerPrestamoPendiente(int $id): ?array
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/prestamos/{$id}");

        if (!$response->successful()) {
            return null;
        }

        $prestamo = $response->json()['data'];

        $prestamo['detalles'] = array_values(array_filter(
            $prestamo['detalles'],
            fn($d) => $d['id_herramienta'] && $d['estatus_articulo'] === 'Prestado'
        ));

        return $prestamo;
    }
}
