<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrestamoController extends Controller
{
    
    public function index()
    {
        $prestamos = Prestamo::with(['empleado', 'usuario'])
            ->where('id_usuario', Auth::id())
            ->orderByDesc('fecha_prestamo')
            ->paginate(10);

        return view('pedidos.prestamos', compact('prestamos'));
    }

    public function show(int $id)
    {
        $prestamo = Prestamo::with(['empleado', 'usuario', 'detalles'])
            ->where('id_prestamo', $id)
            ->where('id_usuario', Auth::id())
            ->firstOrFail();

        return view('prestamos_detalle', compact('prestamo'));
    }
}
