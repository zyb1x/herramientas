<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuarios;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function create()
    {
        return view('login.formulario-crear');
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

        $usuario = Usuarios::where('correo', $request->email)->first();

        if (!$usuario) {
            return back()->withErrors([
                'error' => 'No existe una cuenta con ese correo electrónico.'
            ])->withInput();
        }

        $credenciales = [
            'correo' => $request->email,
            'password' => $request->password
        ];

        if (Auth::guard('usuarios')->attempt($credenciales)) {
            $request->session()->regenerate();
            return redirect()->intended('/inicio');
        }

        return back()->withErrors([
            'error' => 'La contraseña es incorrecta.'
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('usuarios')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
