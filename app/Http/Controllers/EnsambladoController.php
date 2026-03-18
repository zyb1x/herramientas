<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ensamblado;
use App\Models\Herramientas;

class EnsambladoController extends Controller
{
    public function index()
    {
        return view('ensamblados.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_empleado'  => 'required|integer',
            'articulos'    => 'required|array|min:1',
            'articulos.*.id_herramienta' => 'required|integer|exists:herramientas,id_herramienta',
            'articulos.*.cantidad'       => 'required|integer|min:1',
        ]);

        foreach ($request->articulos as $item) {
            $herramienta = Herramientas::findOrFail($item['id_herramienta']);
            $existenciaAntes   = $herramienta->existencia;
            $existenciaDespues = $existenciaAntes + (int) $item['cantidad'];

            Ensamblado::create([
                'id_herramienta'     => $herramienta->id_herramienta,
                'id_empleado'        => $request->id_empleado,
                'cantidad_sobrante'  => $item['cantidad'],
                'existencia_antes'   => $existenciaAntes,
                'existencia_despues' => $existenciaDespues,
            ]);

            // Sumar la existencia
            $herramienta->increment('existencia', (int) $item['cantidad']);
        }

        return redirect()->route('ensamblados.index')
            ->with('success', 'Artículos ensamblados registrados correctamente.');
    }
}