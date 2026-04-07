<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Models\Usuarios;


class LoginController extends Controller
{

    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://127.0.0.1:8000/api');
    }

    // Vista del formulario de login
    public function showLoginForm()
    {
        return view('login.login');
    }

    // consume la API y guarda token en sesión
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Ingrese un correo válido.',
            'password.required' => 'Ingrese una contraseña válida.',
        ]);

        $response = Http::post("{$this->apiUrl}/login", [
            'correo'     => $request->email,
            'contrasena' => $request->password,
        ]);

        if ($response->serverError()) {
            return back()->withErrors([
                'error' => 'Error en el servidor. Intentalo más tarde',
            ])->withInput();
        }

        $data = $response->json();

        // xredenciales incorrectas
        if (!$data['success']) {
            return back()->withErrors([
                'error' => $data['mensaje'],
            ])->withInput();
        }

        // guarda token y datos del usuario en sesion
        session([
            'api_token' => $data['data']['token'],
            'usuario'   => $data['data']['usuario'],
        ]);

        $request->session()->regenerate();

        return redirect()->route('inicio');
    }


    public function create()
    {
        return view('login.formulario-crear');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'nombre' => 'required',
                'correo' => 'required|email',
                'usuario' => 'required',
                'contrasena' => 'required|min:6',
                'conf_contrasena' => 'required|same:contrasena',
                'rol'   => 'required|in:Administrador,Almacenista,Supervisor',
                'turno' => 'required|in:Matutino,Vespertino,Nocturno',
                'imagen' => 'required|nullable|max:2048|mimes:jpeg,png,jpg'
            ],
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'usuario.required' => 'El nombre de usuario es obligatorio.',
                'usuario.unique' => 'Ya existe un empleado con ese nombre de usuario.',
                'correo.required' => 'El correo electrónico es obligatorio.',
                'correo.email' => 'El correo electrónico debe ser una dirección válida.',
                'correo.unique' => 'Ya existe un empleado con ese correo electrónico.',
                'contrasena.required' => 'La contraseña es obligatoria.',
                'contrasena.min' => 'La contraseña debe tener al menos 6 caracteres.',
                'conf_contrasena.required' => 'La confirmación de la contraseña es obligatoria.',
                'conf_contrasena.same' => 'Las contraseñas no coinciden.',
                'rol.required' => 'El rol es obligatorio.',
                'rol.in'       => 'Debes seleccionar un rol válido.',
                'turno.required' => 'El turno es obligatorio.',
                'turno.in'       => 'Debes seleccionar un turno válido.',
                'imagen.required' => 'La imagen es obligatoria.',
                'imagen.max' => 'La imagen no debe superar los 2MB.',
                'imagen.mimes' => 'La imagen debe ser un archivo de tipo: jpeg, png, jpg.'
            ]
        );

        // Construir petición multipart si hay imagen
        $peticion = Http::withToken(session('api_token'));

        if ($request->hasFile('imagen')) {
            $response = $peticion->attach(
                'imagen',
                file_get_contents($request->file('imagen')->getRealPath()),
                $request->file('imagen')->getClientOriginalName()
            )->post("{$this->apiUrl}/usuarios", [
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
            return back()->withErrors(['error' => $data['mensaje']])
                ->withInput();
        }



        return redirect()->route('inicio')->with('success', 'Usuario creado exitosamente.');
    }

    // POST /logout — revoca token en la API y limpia sesión
    public function logout(Request $request)
    {
        // Revocar token en la API
        Http::withToken(session('api_token'))
            ->post("{$this->apiUrl}/logout");

        // Limpiar sesión
        $request->session()->forget(['api_token', 'usuario']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
