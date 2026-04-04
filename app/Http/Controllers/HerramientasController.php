<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class HerramientasController extends Controller
{

    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://127.0.0.1:8000/api');
    }

    // GET /herramientas
    public function index(Request $request)
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/herramientas");

        $herramientas = $response->successful() ? $response->json()['data'] : [];

        return view('herramientas.herramientas', compact('herramientas'));
    }

    // GET /herramientas/buscar — devuelve JSON para búsqueda en tiempo real
    public function buscar(Request $request)
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/herramientas");

        $herramientas = $response->successful() ? $response->json()['data'] : [];

        // Filtrar por nombre si viene el parámetro q
        if ($request->filled('q')) {
            $q = strtolower($request->q);
            $herramientas = array_filter($herramientas, function ($h) use ($q) {
                return str_contains(strtolower($h['nombre_herramienta']), $q);
            });
        }

        return response()->json(array_values($herramientas));
    }

    // GET /herramientas/listado
    public function listado(Request $request)
    {
        $responseH = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/herramientas");

        $herramientas = $responseH->successful() ? $responseH->json()['data'] : [];

        // obtener categorias
        $responseC = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/categorias");

        $categorias = $responseC->successful() ? $responseC->json()['data'] : [];

        // filtrar por nombre
        if ($request->filled('q')) {
            $q = strtolower($request->q);
            $herramientas = array_filter($herramientas, function ($h) use ($q) {
                return str_contains(strtolower($h['nombre_herramienta']), $q);
            });
        }

        // filtrar por categorias
        if ($request->filled('categorias')) {
            $herramientas = array_filter($herramientas, function ($h) use ($request) {
                return in_array($h['id_categoria'], $request->categorias);
            });
        }

        $herramientas = array_values($herramientas);

        return view('herramientas.listado', compact('herramientas', 'categorias'));
    }

    // GET /herramientas/registro
    public function create()
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/categorias");

        $categorias = $response->successful() ? $response->json()['data'] : [];

        return view('herramientas.formulario-crear', compact('categorias'));
    }

    // POST /herramientas/store
    public function store(Request $request)
    {
        $request->validate([
            'id_categoria'       => 'required',
            'nombre_herramienta' => 'required',
            'existencia'         => 'required|integer',
            'imagen'             => 'required|image|max:2048|mimes:jpeg,png,jpg',
        ], [
            'id_categoria.required' => 'La categoría es obligatoria.',
            'nombre_herramienta.required' => 'El nombre de la herramienta es obligatorio.',
            'existencia.required' => 'La existencia es obligatoria.',
            'disponible.required' => 'La disponibilidad es obligatoria.',
            'imagen.required' => 'La imagen es obligatoria.',
            'imagen.mimes' => 'El archivo debe ser una imagen.',
            'imagen.max' => 'La imagen no debe superar los 2MB.'
        ]);

        $response = Http::withToken(session('api_token'))
            ->attach(
                'imagen',
                file_get_contents($request->file('imagen')->getRealPath()),
                $request->file('imagen')->getClientOriginalName()
            )
            ->post("{$this->apiUrl}/herramientas", [
                'id_categoria'       => $request->id_categoria,
                'nombre_herramienta' => $request->nombre_herramienta,
                'existencia'         => $request->existencia,
            ]);

        $data = $response->json();

        if (!$data['success']) {
            return back()->withErrors(['error' => $data['mensaje']])->withInput();
        }

        return redirect()->route('herramientas.listado')
            ->with('success', 'Herramienta añadida exitosamente.');
    }


    // GET /herramientas/{id}/edit
    public function edit($id)
    {
        $responseH = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/herramientas/{$id}");

        $responseC = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/categorias");

        if (!$responseH->successful()) {
            return redirect()->route('herramientas.listado')
                ->with('error', 'Herramienta no encontrada.');
        }

        $herramienta = $responseH->json()['data'];
        $categorias  = $responseC->successful() ? $responseC->json()['data'] : [];

        return view('herramientas.formulario-editar', compact('herramienta', 'categorias'));
    }

    // POST /herramientas/{id}/actualizar
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_categoria'       => 'required',
            'nombre_herramienta' => 'required',
            'existencia'         => 'required|integer',
            'imagen'             => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ], [
            'id_categoria.required'       => 'La categoría es obligatoria.',
            'nombre_herramienta.required' => 'El nombre de la herramienta es obligatorio.',
            'existencia.required'         => 'La existencia es obligatoria.',
            'existencia.integer'          => 'La existencia debe ser un número entero.',
            'imagen.mimes'                => 'La imagen debe ser un archivo de tipo: jpeg, png, jpg.',
            'imagen.max'                  => 'La imagen no debe superar los 2MB.',
        ]);

        $peticion = Http::withToken(session('api_token'));

        if ($request->hasFile('imagen')) {
            $response = $peticion
                ->attach(
                    'imagen',
                    file_get_contents($request->file('imagen')->getRealPath()),
                    $request->file('imagen')->getClientOriginalName()
                )
                ->put("{$this->apiUrl}/herramientas/{$id}", [
                    'id_categoria'       => $request->id_categoria,
                    'nombre_herramienta' => $request->nombre_herramienta,
                    'existencia'         => $request->existencia,
                ]);
        } else {
            $response = $peticion->put("{$this->apiUrl}/herramientas/{$id}", [
                'id_categoria'       => $request->id_categoria,
                'nombre_herramienta' => $request->nombre_herramienta,
                'existencia'         => $request->existencia,
            ]);
        }

        $data = $response->json();

        if (!$data['success']) {
            return back()->withErrors(['error' => $data['mensaje']])->withInput();
        }

        return redirect()->route('herramientas.listado')
            ->with('success', 'Herramienta actualizada exitosamente.');
    }

    // DELETE /herramientas/destroy/{id}
    public function destroy($id)
    {
        $response = Http::withToken(session('api_token'))
            ->delete("{$this->apiUrl}/herramientas/{$id}");

        $data = $response->json();

        if (!$data['success']) {
            return redirect()->route('herramientas.listado')
                ->with('error', $data['mensaje']);
        }

        return redirect()->route('herramientas.listado')
            ->with('success', 'Estatus cambiado a no disponible exitosamente.');
    }
}
