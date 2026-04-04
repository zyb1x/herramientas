<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class UsuariosController extends Controller
{

    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://127.0.0.1:8000/api');
    }

    // GET /usuarios
    public function index()
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/usuarios");

        $usuarios = $response->successful() ? $response->json()['data'] : [];

        return view('usuarios.index', compact('usuarios'));
    }

    // GET /usuarios/{id}
    public function show($id)
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/usuarios/{$id}");

        if (!$response->successful()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Usuario no encontrado.');
        }

        $usuario = $response->json()['data'];

        return view('usuarios.show', compact('usuario'));
    }

    // GET /usuarios/create
    public function create()
    {
        return view('usuarios.create');
    }

    // POST /usuarios/store
    public function store(Request $request)
    {
        $request->validate([
            'nombre'          => 'required',
            'correo'          => 'required|email',
            'usuario'         => 'required',
            'contrasena'      => 'required|min:6',
            'conf_contrasena' => 'required|same:contrasena',
            'rol'             => 'required|in:Administrador,Almacenista,Supervisor',
            'turno'           => 'required|in:Matutino,Vespertino,Nocturno',
            'imagen'          => 'nullable|image|max:2048|mimes:jpeg,png,jpg',
        ], [
            'nombre.required'          => 'El nombre es obligatorio.',
            'correo.required'          => 'El correo es obligatorio.',
            'correo.email'             => 'El correo debe ser una dirección válida.',
            'usuario.required'         => 'El nombre de usuario es obligatorio.',
            'contrasena.required'      => 'La contraseña es obligatoria.',
            'contrasena.min'           => 'La contraseña debe tener al menos 6 caracteres.',
            'conf_contrasena.required' => 'La confirmación de contraseña es obligatoria.',
            'conf_contrasena.same'     => 'Las contraseñas no coinciden.',
            'rol.required'             => 'El rol es obligatorio.',
            'rol.in'                   => 'El rol debe ser Administrador, Almacenista o Supervisor.',
            'turno.required'           => 'El turno es obligatorio.',
            'turno.in'                 => 'El turno debe ser Matutino, Vespertino o Nocturno.',
            'imagen.mimes'             => 'La imagen debe ser jpeg, png o jpg.',
            'imagen.max'               => 'La imagen no debe superar 2MB.',
        ]);

        $peticion = Http::withToken(session('api_token'));

        if ($request->hasFile('imagen')) {
            $response = $peticion
                ->attach(
                    'imagen',
                    file_get_contents($request->file('imagen')->getRealPath()),
                    $request->file('imagen')->getClientOriginalName()
                )
                ->post("{$this->apiUrl}/usuarios", [
                    'nombre'          => $request->nombre,
                    'correo'          => $request->correo,
                    'usuario'         => $request->usuario,
                    'contrasena'      => $request->contrasena,
                    'conf_contrasena' => $request->conf_contrasena,
                    'rol'             => $request->rol,
                    'turno'           => $request->turno,
                ]);
        } else {
            $response = $peticion->post("{$this->apiUrl}/usuarios", [
                'nombre'          => $request->nombre,
                'correo'          => $request->correo,
                'usuario'         => $request->usuario,
                'contrasena'      => $request->contrasena,
                'conf_contrasena' => $request->conf_contrasena,
                'rol'             => $request->rol,
                'turno'           => $request->turno,
            ]);
        }

        $data = $response->json();

        if (!$data['success']) {
            return back()->withErrors(['error' => $data['mensaje']])->withInput();
        }

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    // GET /usuarios/{id}/edit
    public function edit($id)
    {
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/usuarios/{$id}");

        if (!$response->successful()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Usuario no encontrado.');
        }

        $usuario = $response->json()['data'];

        return view('usuarios.formulario-editar', compact('usuario'));
    }

    // PUT /usuarios/{id}
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'          => 'required',
            'correo'          => 'required|email',
            'usuario'         => 'required',
            'contrasena'      => 'nullable|min:6',
            'conf_contrasena' => 'nullable|same:contrasena',
            'rol'             => 'required|in:Administrador,Almacenista,Supervisor',
            'turno'           => 'required|in:Matutino,Vespertino,Nocturno',
            'imagen'          => 'nullable|image|max:2048|mimes:jpeg,png,jpg',
        ], [
            'nombre.required'      => 'El nombre es obligatorio.',
            'correo.required'      => 'El correo es obligatorio.',
            'correo.email'         => 'El correo debe ser una dirección válida.',
            'usuario.required'     => 'El nombre de usuario es obligatorio.',
            'contrasena.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'conf_contrasena.same' => 'Las contraseñas no coinciden.',
            'rol.required'         => 'El rol es obligatorio.',
            'rol.in'               => 'El rol debe ser Administrador, Almacenista o Supervisor.',
            'turno.required'       => 'El turno es obligatorio.',
            'turno.in'             => 'El turno debe ser Matutino, Vespertino o Nocturno.',
            'imagen.mimes'         => 'La imagen debe ser jpeg, png o jpg.',
            'imagen.max'           => 'La imagen no debe superar 2MB.',
        ]);

        $peticion = Http::withToken(session('api_token'));

        if ($request->hasFile('imagen')) {
            $response = $peticion
                ->attach(
                    'imagen',
                    file_get_contents($request->file('imagen')->getRealPath()),
                    $request->file('imagen')->getClientOriginalName()
                )
                ->put("{$this->apiUrl}/usuarios/{$id}", [
                    'nombre'          => $request->nombre,
                    'correo'          => $request->correo,
                    'usuario'         => $request->usuario,
                    'contrasena'      => $request->contrasena,
                    'conf_contrasena' => $request->conf_contrasena,
                    'rol'             => $request->rol,
                    'turno'           => $request->turno,
                ]);
        } else {
            $response = $peticion->put("{$this->apiUrl}/usuarios/{$id}", [
                'nombre'          => $request->nombre,
                'correo'          => $request->correo,
                'usuario'         => $request->usuario,
                'contrasena'      => $request->contrasena,
                'conf_contrasena' => $request->conf_contrasena,
                'rol'             => $request->rol,
                'turno'           => $request->turno,
            ]);
        }

        $data = $response->json();

        if (!$data['success']) {
            return back()->withErrors(['error' => $data['mensaje']])->withInput();
        }

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    // DELETE /usuarios/{id}
    public function destroy($id)
    {
        $response = Http::withToken(session('api_token'))
            ->delete("{$this->apiUrl}/usuarios/{$id}");

        $data = $response->json();

        if (!$data['success']) {
            return redirect()->route('usuarios.index')
                ->with('error', $data['mensaje']);
        }

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}
