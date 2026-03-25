<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ensamblado;
use App\Models\Materiales;

class EnsambladoController extends Controller
{

    public function index()
    {
        return view('ensamblados.index');
    }


    public function create(Request $request)
    {
        $materiales = Materiales::orderBy('nombre_material')->get();
        $modo       = $request->query('modo', null); // 'existente' | 'nuevo' | null
        return view('ensamblados.formulario-crear', compact('materiales', 'modo'));
    }


    public function show($id)
    {
        $ensamblado = Ensamblado::with('material')->findOrFail($id);

        return response()->json([
            'id_ensamblado'     => $ensamblado->id_ensamblado,
            'id_empleado'       => $ensamblado->id_empleado,
            'id_material'       => $ensamblado->id_material,
            'nombre_material'   => optional($ensamblado->material)->nombre_material,
            'existencia_actual' => optional($ensamblado->material)->existencia,
            'cantidad_sobrante' => $ensamblado->cantidad_sobrante,
            'existencia_antes'  => $ensamblado->existencia_antes,
            'existencia_despues' => $ensamblado->existencia_despues,
            'fecha_registro'    => $ensamblado->fecha_registro,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_empleado'             => 'required|integer',
            'articulos'               => 'required|array|min:1',
            'articulos.*.id_material' => 'required|integer|exists:materiales,id_material',
            'articulos.*.cantidad'    => 'required|integer|min:1',
        ]);

        foreach ($request->articulos as $item) {
            $material          = Materiales::findOrFail($item['id_material']);
            $existenciaAntes   = $material->existencia;
            $existenciaDespues = $existenciaAntes + (int) $item['cantidad'];

            Ensamblado::create([
                'id_material'        => $material->id_material,
                'id_empleado'        => $request->id_empleado,
                'cantidad_sobrante'  => $item['cantidad'],
                'existencia_antes'   => $existenciaAntes,
                'existencia_despues' => $existenciaDespues,
            ]);

            $material->increment('existencia', (int) $item['cantidad']);
        }

        return redirect()->route('ensamblados.index')
            ->with('success', 'Ensamblado registrado correctamente.');
    }
}
