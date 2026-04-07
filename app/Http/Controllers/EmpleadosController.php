<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class EmpleadosController extends Controller
{

    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://127.0.0.1:8000/api');
    }

    private function http()
    {
        return Http::withToken(session('api_token'))->timeout(60);
    }

    // GET /empleados
    public function index()
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/empleados");

        $empleados = $response->successful() ? $response->json()['data'] : [];

        return view('empleados.index', compact('empleados'));
    }

    // GET /empleados/create
    public function create()
    {
        return view('empleados.create');
    }

    // POST /empleados/store
    public function store(Request $request)
    {
        $request->validate([
            'nombre'     => 'required',
            'apellido_p' => 'required',
            'apellido_m' => 'nullable',
            'correo'     => 'nullable|email',
            'puesto'     => 'nullable',
            'turno'      => 'nullable',
            'imagen'     => 'nullable|image|max:2048',
        ], [
            'nombre.required'     => 'El nombre es obligatorio.',
            'apellido_p.required' => 'El apellido paterno es obligatorio.',
            'correo.required'     => 'El correo electrónico es opcional.',
            'correo.email'        => 'El correo electrónico debe ser una dirección válida.',
            'turno.required'      => 'Favor de seleccionar un turno.',
        ]);

        $peticion = Http::withToken(session('api_token'));

        if ($request->hasFile('imagen')) {
            $response = $peticion
                ->asMultipart()
                ->attach('imagen', file_get_contents($request->file('imagen')->getRealPath()), $request->file('imagen')->getClientOriginalName(), ['Content-Type' => $request->file('imagen')->getMimeType()])
                ->attach('nombre',     (string) $request->nombre)
                ->attach('apellido_p', (string) $request->apellido_p)
                ->attach('apellido_m', (string) $request->apellido_m ?? '')
                ->attach('correo',     (string) $request->correo ?? '')
                ->attach('puesto',     (string) $request->puesto ?? '')
                ->attach('turno',      (string) $request->turno ?? '')
                ->post("{$this->apiUrl}/empleados");
        } else {
            $response = $peticion->post("{$this->apiUrl}/empleados", [
                'nombre'     => $request->nombre,
                'apellido_p' => $request->apellido_p,
                'apellido_m' => $request->apellido_m,
                'correo'     => $request->correo,
                'puesto'     => $request->puesto,
                'turno'      => $request->turno,
            ]);
        }

        $data = $response->json();

        if (!$data['success']) {
            return back()->withErrors(['error' => $data['mensaje']])->withInput();
        }

        return redirect()->route('empleados.index')
            ->with('success', 'Empleado creado exitosamente.');
    }

    // GET /empleados/{id}/edit
    public function edit($id)
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/empleados/{$id}");

        if (!$response->successful()) {
            return redirect()->route('empleados.index')
                ->with('error', 'Empleado no encontrado.');
        }

        $empleado = $response->json()['data'];

        return view('empleados.formulario-editar', compact('empleado'));
    }

    // POST /empleados/{id}/actualizar
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'     => 'required',
            'apellido_p' => 'required',
            'apellido_m' => 'nullable',
            'correo'     => 'nullable|email',
            'puesto'     => 'nullable',
            'turno'      => 'nullable',
            'imagen'     => 'nullable|image|max:2048',
        ], [
            'nombre.required'     => 'El nombre es obligatorio.',
            'apellido_p.required' => 'El apellido paterno es obligatorio.',
            'correo.required'     => 'El correo electrónico es opcional.',
            'correo.email'        => 'El correo electrónico debe ser una dirección válida.',
            'turno.required'      => 'Favor de seleccionar un turno.',
        ]);

        $peticion = Http::withToken(session('api_token'));

        if ($request->hasFile('imagen')) {
            $response = $peticion
                ->asMultipart()
                ->attach('imagen', file_get_contents($request->file('imagen')->getRealPath()), $request->file('imagen')->getClientOriginalName(), ['Content-Type' => $request->file('imagen')->getMimeType()])
                ->attach('nombre',     (string) $request->nombre)
                ->attach('apellido_p', (string) $request->apellido_p)
                ->attach('apellido_m', (string) $request->apellido_m ?? '')
                ->attach('correo',     (string) $request->correo ?? '')
                ->attach('puesto',     (string) $request->puesto ?? '')
                ->attach('turno',      (string) $request->turno ?? '')
                ->put("{$this->apiUrl}/empleados/{$id}");
        } else {
            $response = $peticion->put("{$this->apiUrl}/empleados/{$id}", [
                'nombre'     => $request->nombre,
                'apellido_p' => $request->apellido_p,
                'apellido_m' => $request->apellido_m,
                'correo'     => $request->correo,
                'puesto'     => $request->puesto,
                'turno'      => $request->turno,
            ]);
        }

        $data = $response->json();

        if (!$data['success']) {
            return back()->withErrors(['error' => $data['mensaje']])->withInput();
        }

        return redirect()->route('empleados.index')
            ->with('success', 'Empleado actualizado exitosamente.');
    }

    // DELETE /empleados/{id}
    public function destroy($id)
    {
        $response = Http::withToken(session('api_token'))
            ->delete("{$this->apiUrl}/empleados/{$id}");

        $data = $response->json();

        if (!$data['success']) {
            return redirect()->route('empleados.index')
                ->with('error', $data['mensaje']);
        }

        return redirect()->route('empleados.index')
            ->with('success', 'Empleado eliminado exitosamente.');
    }
}
