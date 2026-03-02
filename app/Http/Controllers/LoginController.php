<?php

namespace App\Http\Controllers;

use App\Models\Empleados;
use Illuminate\Http\Request;
use App\Models\Usuarios;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{

    public function create()
    {
        return view('login.formulario-crear');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'nombre' => 'required',
                'apellido_p' => 'required',
                'apellido_m' => 'required',
                'correo' => 'required|email|unique:Empleados,correo',
                'contrasena' => 'required|min:6',
                'conf_contrasena' => 'required|same:contrasena',
                'puesto' => 'required',
                'area' => 'required',
                'turno' => 'required',
                'imagen' => 'nullable|image|max:2048'
            ],
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'apellido_p.required' => 'El apellido paterno es obligatorio.',
                'apellido_m.required' => 'El apellido materno es obligatorio.',
                'correo.required' => 'El correo electrónico es obligatorio.',
                'correo.email' => 'El correo electrónico debe ser una dirección válida.',
                'correo.unique' => 'Ya existe un empleado con ese correo electrónico.',
                'contrasena.required' => 'La contraseña es obligatoria.',
                'contrasena.min' => 'La contraseña debe tener al menos 6 caracteres.',
                'conf_contrasena.required' => 'La confirmación de la contraseña es obligatoria.',
                'conf_contrasena.same' => 'Las contraseñas no coinciden.',
                'puesto.required' => 'El puesto es obligatorio.',
                'area.required' => 'El área es obligatoria.',
                'turno.required' => 'El turno es obligatorio.',
                // 'imagen.image' => 'El archivo debe ser una imagen.',
                // 'imagen.max' => 'La imagen no debe superar los 2MB.'
            ]
        );

        $empleado = new Empleados();
        $empleado->nombre = $request->nombre;
        $empleado->apellido_p = $request->apellido_p;
        $empleado->apellido_m = $request->apellido_m;
        $empleado->correo = $request->correo;
        $empleado->contrasena = Hash::make($request->contrasena);
        $empleado->puesto = $request->puesto;
        $empleado->area = $request->area;
        $empleado->turno = $request->turno;

        $empleado->save();

        if ($request->has('imagen')) {
            $imagen = $request->imagen;
            $nuevo_nombre = 'empleado_' . $empleado->id . '.jpg';
            $ruta = $imagen->storeAs('imagenes/empleados', $nuevo_nombre, 'public');
            $empleado->imagen = '/storage/' . $ruta;
            $empleado->save();
        }



        return redirect()->route('login')->with('success', 'Empleado creado exitosamente.');
    }

    public function showLoginForm()
    {
        return view('login.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $empleado = Empleados::where('correo', $request->email)->first();

        if (!$empleado) {
            return back()->withErrors([
                'error' => 'No existe una cuenta con ese correo electrónico.'
            ])->withInput();
        }

        if (!Hash::check($request->password, $empleado->contrasena)) {
            return back()->withErrors([
                'error' => 'La contraseña es incorrecta.'
            ])->withInput();
        }

        Auth::guard('empleados')->login($empleado);
        $request->session()->regenerate();

        return redirect('/inicio');
    }

    public function logout(Request $request)
    {
        Auth::guard('empleados')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
