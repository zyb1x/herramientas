<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EnsambladoController extends Controller
{
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://127.0.0.1:8000/api');
    }

    // GET /ensamblados
    public function index()
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/ensamblados");

        $ensamblados = $response->successful() ? $response->json()['data'] : [];

        return view('ensamblados.index', compact('ensamblados'));
    }

    // GET /ensamblados/crear
    public function create()
    {
       
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/usuarios");

        $usuarios = $response->successful() ? $response->json()['data'] : [];

        $supervisores = array_values(array_filter($usuarios, function ($u) {
            return $u['rol'] === 'Supervisor';
        }));

        return view('ensamblados.formulario-crear', compact('supervisores'));
    }

    // GET /ensamblados/{id}/json
    public function show($id)
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/ensamblados/{$id}");

        if (!$response->successful()) {
            return response()->json(['error' => 'Ensamblado no encontrado.'], 404);
        }

        $ensamblado = $response->json()['data'];

        return response()->json([
            'id_ensamblado'  => $ensamblado['id_ensamblado'],
            'id_empleado'    => $ensamblado['id_empleado'],
            'nombre'         => $ensamblado['nombre'],
            'cantidad'       => $ensamblado['cantidad'],
            'fecha_registro' => $ensamblado['fecha_registro'],
        ]);
    }

    // POST /ensamblados/store
    public function store(Request $request)
    {
        $request->validate([
            'id_empleado' => 'required|integer',
            'nombre'      => 'required|string|max:255',
            'cantidad'    => 'required|integer|min:1',
        ], [
            'id_empleado.required' => 'El supervisor es obligatorio.',
            'nombre.required'      => 'El nombre es obligatorio.',
            'cantidad.required'    => 'La cantidad es obligatoria.',
            'cantidad.min'         => 'La cantidad debe ser mayor a 0.',
        ]);

        $response = Http::withToken(session('api_token'))
            ->post("{$this->apiUrl}/ensamblados", [
                'id_empleado' => $request->id_empleado,
                'nombre'      => $request->nombre,
                'cantidad'    => $request->cantidad,
            ]);

        $data = $response->json();

        if (!$data['success']) {
            return back()->withErrors(['error' => $data['mensaje']])->withInput();
        }

        return redirect()->route('ensamblados.index')
            ->with('success', 'Ensamblado registrado correctamente.');
    }

    // GET /ensamblados/ingresar
    public function ingresar()
    {
       
        $responseU = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/usuarios");

        $usuarios = $responseU->successful() ? $responseU->json()['data'] : [];

        $supervisores = array_values(array_filter($usuarios, function ($u) {
            return $u['rol'] === 'Supervisor';
        }));

        
        $responseE = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/ensamblados");

        $ensamblados = $responseE->successful() ? $responseE->json()['data'] : [];

        return view('ensamblados.formulario-ingresar', compact('supervisores', 'ensamblados'));
    }

    // POST /ensamblados/ingresar
    public function ingresarStore(Request $request)
    {
        $request->validate([
            'id_ensamblado' => 'required|integer',
            'id_empleado'   => 'required|integer',
            'cantidad'      => 'required|integer|min:1',
        ], [
            'id_empleado.required'   => 'El supervisor es obligatorio.',
            'id_ensamblado.required' => 'El ensamblado es obligatorio.',
            'cantidad.required'      => 'La cantidad es obligatoria.',
            'cantidad.min'           => 'La cantidad debe ser mayor a 0.',
        ]);

        
        $responseE = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/ensamblados/{$request->id_ensamblado}");

        if (!$responseE->successful()) {
            return back()->with('error', 'Ensamblado no encontrado.');
        }

        $ensamblado   = $responseE->json()['data'];
        $nuevaCantidad = $ensamblado['cantidad'] + (int) $request->cantidad;

        
        $response = Http::withToken(session('api_token'))
            ->put("{$this->apiUrl}/ensamblados/{$request->id_ensamblado}", [
                'cantidad' => $nuevaCantidad,
            ]);

        $data = $response->json();

        if (!$data['success']) {
            return back()->withErrors(['error' => $data['mensaje']])->withInput();
        }

        return redirect()->route('ensamblados.index')
            ->with('success', 'Cantidad ingresada correctamente.');
    }
}
