<?php

namespace App\Http\Controllers;

use App\Models\Materiales;
use Illuminate\Http\Request;

class MaterialesController extends Controller
{
    public function index()
    {
        $materiales = Materiales::all();
        return view('materiales.materiales', compact('materiales'));
    }

    public function listado(Request $request)
    {
        $query = Materiales::query();

        if ($request->filled('q')) {
            $query->where('nombre_material', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('estatus')) {
            $query->whereIn('estatus', $request->estatus);
        }

        $materiales = $query->get();

        return view('materiales.listado', compact('materiales'));
    }
    public function buscar(Request $request)
    {
        $materiales = Materiales::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where('nombre_material', 'like', '%' . $request->q . '%');
            })
            ->get();
        return response()->json($materiales);
    }

    public function create()
    {
        return view('materiales.formulario-crear');
    }

    public function store(Request $request)
    {

        $materiales  = new Materiales();
        $request->validate(
            [
                'nombre_material' => 'required',
                'existencia' => 'required',
                // 'estatus' => 'nullable',
            ],
            [
                'nombre_material.required' => 'El nombre es obligatorio.',
                'existencia.required' => 'La existencia es obligatoria.',
                // 'estatus.required' => 'El estatus es obligatorio.'
            ]
        );

        $materiales->nombre_material = $request->nombre_material;
        $materiales->existencia = $request->existencia;
        $materiales->estatus = $request->estatus;

        $materiales->save();

        return redirect()->route('materiales.listado')->with('hecho', 'Material añadida exitosamente.');
    }

    public function edit($id)
    {
        $materiales = Materiales::find($id);
        return view('materiales.formulario-editar')->with('materiales', $materiales);
    }

    public function update(Request $request, $id)
    {
        $materiales = Materiales::find($id);

        $materiales->nombre_material = $request->nombre_material;
        $materiales->existencia = $request->existencia;
        $materiales->estatus = $request->estatus;

        $materiales->save();

        return redirect()->route('materiales.listado')->with('hecho', 'Material actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $material = Materiales::find($id);

        if ($material) {
            $material->update(['estatus' => 'Agotado']);

            return redirect()->route('materiales.listado')->with('success', 'Estatus cambiado a no disponible exitosamente.');
        }

        return redirect()->route('materiales.listado')->with('error', 'Material no encontrada.');
    }
}
