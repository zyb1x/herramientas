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

    private function http()
    {
        return Http::withToken(session('api_token'))->timeout(60);
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
                ->asMultipart()
                ->attach('imagen', file_get_contents($request->file('imagen')->getRealPath()), $request->file('imagen')->getClientOriginalName(), ['Content-Type' => $request->file('imagen')->getMimeType()])
                ->attach('nombre',          (string) $request->nombre)
                ->attach('correo',          (string) $request->correo)
                ->attach('usuario',         (string) $request->usuario)
                ->attach('contrasena',      (string) $request->contrasena)
                ->attach('conf_contrasena', (string) $request->conf_contrasena)
                ->attach('rol',             (string) $request->rol)
                ->attach('turno',           (string) $request->turno)
                ->post("{$this->apiUrl}/usuarios");
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
                ->asMultipart()
                ->attach('imagen', file_get_contents($request->file('imagen')->getRealPath()), $request->file('imagen')->getClientOriginalName(), ['Content-Type' => $request->file('imagen')->getMimeType()])
                ->attach('nombre',          (string) $request->nombre)
                ->attach('correo',          (string) $request->correo)
                ->attach('usuario',         (string) $request->usuario)
                ->attach('contrasena',      (string) $request->contrasena ?? '')
                ->attach('conf_contrasena', (string) $request->conf_contrasena ?? '')
                ->attach('rol',             (string) $request->rol)
                ->attach('turno',           (string) $request->turno)
                ->put("{$this->apiUrl}/usuarios/{$id}");
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
    
    public function perfil()
{
    return view('usuarios.perfil');
}
 
// POST /perfil/actualizar
   public function actualizarPerfil(Request $request)
{
    $id = session('usuario')['id'];
    $rol = session('usuario')['rol'];

    $request->validate([
        'nombre'          => 'required',
        'correo'          => 'required|email',
        'usuario'         => 'required',
        'contrasena'      => 'nullable|min:6',
        'conf_contrasena' => 'nullable|same:contrasena',
        'turno'           => 'required|in:Matutino,Vespertino,Nocturno',
        'imagen'          => 'nullable|image|max:2048|mimes:jpeg,png,jpg',
    ]);

    $multipart = [
        ['name' => 'nombre',          'contents' => $request->input('nombre', '')],
        ['name' => 'correo',          'contents' => $request->input('correo', '')],
        ['name' => 'usuario',         'contents' => $request->input('usuario', '')],
        ['name' => 'contrasena',      'contents' => $request->input('contrasena', '')],
        ['name' => 'conf_contrasena', 'contents' => $request->input('conf_contrasena', '')],
        ['name' => 'rol',             'contents' => $rol],
        ['name' => 'turno',           'contents' => $request->input('turno', '')],
    ];

    if ($request->hasFile('imagen')) {
        $archivo = $request->file('imagen');
        $multipart[] = [
            'name'     => 'imagen',
            'contents' => fopen($archivo->getRealPath(), 'r'),
            'filename' => $archivo->getClientOriginalName(),
            'headers'  => ['Content-Type' => $archivo->getMimeType()],
        ];
    }

    try {
        $client = new \GuzzleHttp\Client();
        $guzzleResponse = $client->post("{$this->apiUrl}/usuarios/{$id}/actualizar", [
            'headers' => [
                'Authorization' => 'Bearer ' . session('api_token'),
                'Accept'        => 'application/json',
            ],
            'multipart' => $multipart,
        ]);

        $data = json_decode($guzzleResponse->getBody()->getContents(), true);

    } catch (\GuzzleHttp\Exception\ClientException $e) {
        $body = json_decode($e->getResponse()->getBody()->getContents(), true);
        return back()->withErrors(['error' => $body['mensaje'] ?? 'Error de validación en la API.'])->withInput();
    } catch (\Exception $e) {
        return back()->withErrors(['error' => $e->getMessage()])->withInput();
    }

    if (!($data['success'] ?? false)) {
        return back()->withErrors(['error' => $data['mensaje'] ?? 'Error al actualizar.'])->withInput();
    }

    $usuarioActualizado = $data['data'];

    session(['usuario' => [
        'id'      => $usuarioActualizado['id'],
        'nombre'  => $usuarioActualizado['nombre'],
        'correo'  => $usuarioActualizado['correo'],
        'usuario' => $usuarioActualizado['usuario'],
        'rol'     => $usuarioActualizado['rol'],
        'turno'   => $usuarioActualizado['turno'],
        'imagen'  => $usuarioActualizado['imagen'] . '?v=' . time(),
    ]]);

    return redirect('/perfil')->with('success', '¡Perfil actualizado correctamente!');
}
}
