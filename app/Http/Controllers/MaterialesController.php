<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class MaterialesController extends Controller
{

    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://127.0.0.1:8000/api');
    }

    // GET /materiales
    public function index()
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/materiales");

        $materiales = $response->successful() ? $response->json()['data'] : [];

        return view('materiales.materiales', compact('materiales'));
    }

    // GET /materiales/listado
    public function listado(Request $request)
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/materiales");

        $materiales = $response->successful() ? $response->json()['data'] : [];

        if ($request->filled('q')) {
            $q = strtolower($request->q);
            $materiales = array_filter($materiales, function ($m) use ($q) {
                return str_contains(strtolower($m['nombre_material']), $q);
            });
        }

        if ($request->filled('estatus')) {
            $materiales = array_filter($materiales, function ($m) use ($request) {
                return in_array($m['estatus'], $request->estatus);
            });
        }

        $materiales = array_values($materiales);

        return view('materiales.listado', compact('materiales'));
    }

    // GET /materiales/buscar -  devuelve JSON para busqueda dinamica
    public function buscar(Request $request)
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/materiales");

        $materiales = $response->successful() ? $response->json()['data'] : [];

        if ($request->filled('q')) {
            $q = strtolower($request->q);
            $materiales = array_filter($materiales, function ($m) use ($q) {
                return str_contains(strtolower($m['nombre_material']), $q);
            });
        }

        return response()->json(array_values($materiales));
    }

    // GET /materiales/registro
    public function create()
    {
        return view('materiales.formulario-crear');
    }

    // POST /materiales/store
    public function store(Request $request)
    {
        $request->validate([
            'nombre_material' => 'required',
            'existencia'      => 'required|integer|min:0',
        ], [
            'nombre_material.required' => 'El nombre es obligatorio.',
            'existencia.required'      => 'La existencia es obligatoria.',
            'existencia.min'           => 'La existencia no puede ser menor a 0.',
        ]);

        $response = Http::withToken(session('api_token'))
            ->post("{$this->apiUrl}/materiales", [
                'nombre_material' => $request->nombre_material,
                'existencia'      => $request->existencia,
            ]);

        $data = $response->json();

        if (!$data['success']) {
            return back()->withErrors(['error' => $data['mensaje']])->withInput();
        }

        return redirect()->route('materiales.listado')
            ->with('hecho', 'Material añadido exitosamente.');
    }

    // GET /materiales/{id}/edit
    public function edit($id)
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/materiales/{$id}");

        if (!$response->successful()) {
            return redirect()->route('materiales.listado')
                ->with('error', 'Material no encontrado.');
        }

        $materiales = $response->json()['data'];

        return view('materiales.formulario-editar', compact('materiales'));
    }

    // POST /materiales/{id}/actualizar
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_material' => 'required',
            'existencia'      => 'required|integer|min:0',
        ], [
            'nombre_material.required' => 'El nombre es obligatorio.',
            'existencia.required'      => 'La existencia es obligatoria.',
            'existencia.min'           => 'La existencia no puede ser menor a 0.',
        ]);

        $response = Http::withToken(session('api_token'))
            ->put("{$this->apiUrl}/materiales/{$id}", [
                'nombre_material' => $request->nombre_material,
                'existencia'      => $request->existencia,
            ]);

        $data = $response->json();

        if (!$data['success']) {
            return back()->withErrors(['error' => $data['mensaje']])->withInput();
        }

        return redirect()->route('materiales.listado')
            ->with('hecho', 'Material actualizado exitosamente.');
    }

    // DELETE /materiales/destroy/{id}
    public function destroy($id)
    {
        $response = Http::withToken(session('api_token'))
            ->delete("{$this->apiUrl}/materiales/{$id}");

        $data = $response->json();

        if (!$data['success']) {
            return redirect()->route('materiales.listado')
                ->with('error', $data['mensaje']);
        }

        return redirect()->route('materiales.listado')
            ->with('success', 'Estatus cambiado a agotado exitosamente.');
    }
}
