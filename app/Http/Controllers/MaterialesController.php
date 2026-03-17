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
                'estatus' => 'required',
            ],
            [
                'nombre_material.required' => 'El nombre es obligatorio.',
                'existencia.required' => 'La existencia es obligatoria.',
                'estatus.required' => 'El estatus es obligatorio.'
            ]
        );

        $materiales->nombre_material = $request->nombre_material;
        $materiales->existencia = $request->existencia;
        $materiales->estatus = $request->estatus;

        $materiales->save();

        return view('/materiales/listado');
    }

    public function edit($id)
    {
        $materiales = Materiales::find($id);
        return view('materiales.formulario-editar')->with('materiales', $materiales);
    }

    public function update(Request $request, $id){
        $materiales = Materiales::find($id);

        $materiales->nombre_material = $request->nombre_material;
        $materiales->existencia = $request->existencia;
        $materiales->estatus = $request->estatus;

        $materiales-> save();


        return redirect('/materiales/listado')->with('hecho', 'Material actualizado');
    }

    public function destroy($id){
        $materiales = Materiales::find($id);

        $materiales->delete();

        return redirect('/materiales/listado')->with('hecho', 'Material eliminado');
    }
}
