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
        // Obtener supervisores de la API
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/usuarios");

        $usuarios = $response->successful() ? $response->json()['data'] : [];

        // Filtrar solo supervisores
        $supervisores = array_values(array_filter($usuarios, function ($u) {
            return $u['rol'] === 'Supervisor';
        }));

        // Obtener materiales disponibles
        $responseM = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/materiales");

        $materiales = $responseM->successful() ? $responseM->json()['data'] : [];

        // Filtrar solo disponibles
        $materiales = array_values(array_filter($materiales, function ($m) {
            return $m['estatus'] === 'Disponible';
        }));

        return view('ensamblados.formulario-crear', compact('supervisores', 'materiales'));
    }

    // GET /ensamblados/{id}/json - devuelve JSON con detalles de ensamblado para mostrar en modal
    public function show($id)
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/ensamblados/{$id}");

        if (!$response->successful()) {
            return response()->json(['error' => 'Ensamblado no encontrado.'], 404);
        }

        $ensamblado = $response->json()['data'];

        return response()->json([
            'id_ensamblado'      => $ensamblado['id_ensamblado'],
            'id_empleado'        => $ensamblado['id_empleado'],
            'id_material'        => $ensamblado['id_material'],
            'nombre_material'    => $ensamblado['material']['nombre_material'] ?? null,
            'existencia_actual'  => $ensamblado['material']['existencia'] ?? null,
            'cantidad_sobrante'  => $ensamblado['cantidad_sobrante'],
            'existencia_antes'   => $ensamblado['existencia_antes'],
            'existencia_despues' => $ensamblado['existencia_despues'],
            'fecha_registro'     => $ensamblado['fecha_registro'],
        ]);
    }

    // POST /ensamblados/store
    public function store(Request $request)
    {
        $request->validate([
            'id_empleado'       => 'required|integer',
            'id_material'       => 'required|integer',
            'cantidad_sobrante' => 'required|integer|min:0',
        ], [
            'id_empleado.required'       => 'El supervisor es obligatorio.',
            'id_material.required'       => 'El material es obligatorio.',
            'cantidad_sobrante.required' => 'La cantidad sobrante es obligatoria.',
            'cantidad_sobrante.min'      => 'La cantidad sobrante no puede ser menor a 0.',
        ]);

        $response = Http::withToken(session('api_token'))
            ->post("{$this->apiUrl}/ensamblados", [
                'id_empleado'       => $request->id_empleado,
                'id_material'       => $request->id_material,
                'cantidad_sobrante' => $request->cantidad_sobrante,
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
        // Obtener supervisores
        $responseU = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/usuarios");

        $usuarios = $responseU->successful() ? $responseU->json()['data'] : [];

        $supervisores = array_values(array_filter($usuarios, function ($u) {
            return $u['rol'] === 'Supervisor';
        }));

        // Obtener ensamblados
        $responseE = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/ensamblados");

        $ensamblados = $responseE->successful() ? $responseE->json()['data'] : [];

        return view('ensamblados.formulario-ingresar', compact('supervisores', 'ensamblados'));
    }

    // POST /ensamblados/ingresar
    public function ingresarStore(Request $request)
    {
        $request->validate([
            'id_ensamblado'     => 'required|integer',
            'id_empleado'       => 'required|integer',
            'cantidad_sobrante' => 'required|integer|min:1',
        ], [
            'id_empleado.required'       => 'El supervisor es obligatorio.',
            'id_ensamblado.required'     => 'El ensamblado es obligatorio.',
            'cantidad_sobrante.required' => 'La cantidad es obligatoria.',
            'cantidad_sobrante.min'      => 'La cantidad debe ser mayor a 0.',
        ]);

        // orimero obtener el ensamblado actual para sumar la cantidad
        $responseE = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/ensamblados/{$request->id_ensamblado}");

        if (!$responseE->successful()) {
            return back()->with('error', 'Ensamblado no encontrado.');
        }

        $ensamblado        = $responseE->json()['data'];
        $nuevaCantidad     = $ensamblado['cantidad_sobrante'] + (int) $request->cantidad_sobrante;

        // actualizar ensamblado con la nueva cantidad
        $response = Http::withToken(session('api_token'))
            ->put("{$this->apiUrl}/ensamblados/{$request->id_ensamblado}", [
                'cantidad_sobrante' => $nuevaCantidad,
            ]);

        $data = $response->json();

        if (!$data['success']) {
            return back()->withErrors(['error' => $data['mensaje']])->withInput();
        }

        return redirect()->route('ensamblados.index')
            ->with('success', 'Cantidad ingresada correctamente.');
    }
}
