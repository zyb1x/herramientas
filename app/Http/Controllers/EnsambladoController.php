<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;
use Illuminate\Http\Request;
use App\Models\Ensamblado;
use App\Models\Materiales;

class EnsambladoController extends Controller
{

    public function index()
    {
        $ensamblados = Ensamblado::with('empleado')
            ->orderByDesc('fecha_registro')
            ->paginate(10);

        return view('ensamblados.index', compact('ensamblados'));
    }

    public function create()
    {
        $supervisores = Usuarios::where('rol', 'Supervisor')
            ->orderBy('nombre')
            ->get();

        return view('ensamblados.formulario-crear', compact('supervisores'));
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
        $request->validate(
            [
                'id_empleado'    => 'required|integer',
                'nombre'         => 'required|string|max:255',
                'cantidad'       => 'required|integer|min:1',
                'fecha_registro' => 'nullable',
            ],
            [
                'id_empleado.required' => 'El supervisor es obligatorio.',
                'id_empleado.integer' => 'El supervisor es obligatorio.',
                'nombre.required' => 'El nombre es obligatorio.',
                'cantidad.required' => 'La cantidad es obligatorio.',
                'cantidad.min' => 'La cantidad debe ser mayor a 0',

            ]
        );

        Ensamblado::create([
            'id_empleado'    => $request->id_empleado,
            'nombre'         => $request->nombre,
            'cantidad'       => $request->cantidad,
            // 'fecha_registro' => $request->fecha_registro,
        ]);

        return redirect()->route('ensamblados.index')
            ->with('success', 'Ensamblado registrado correctamente.');
    }

    public function ingresar()
    {
        $supervisores = Usuarios::where('rol', 'Supervisor')
            ->orderBy('nombre')
            ->get();

        $ensamblados = Ensamblado::orderBy('nombre')->get();

        return view('ensamblados.formulario-ingresar', compact('supervisores', 'ensamblados'));
    }

    public function ingresarStore(Request $request)
    {
        $request->validate(
            [
                'id_ensamblado' => 'required|integer|exists:ensamblados,id_ensamblado',
                'id_empleado'   => 'required|integer',
                'cantidad'      => 'required|integer|min:1',
            ],
            [
                'id_empleado.required' => 'El supervisor es obligatorio.',
                'id_empleado.integer' => 'El supervisor es obligatorio.',
                'nombre.required' => 'El nombre es obligatorio.',
                'cantidad.required' => 'La cantidad es obligatorio.',
                'cantidad.min' => 'La cantidad debe ser mayor a 0',

            ]
        );

        $ensamblado = Ensamblado::findOrFail($request->id_ensamblado);
        $ensamblado->cantidad += (int) $request->cantidad;
        $ensamblado->save();

        return redirect()->route('ensamblados.index')
            ->with('success', 'Cantidad ingresada correctamente.');
    }
}
