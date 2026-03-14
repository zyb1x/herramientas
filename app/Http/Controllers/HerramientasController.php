<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Herramientas;
use Illuminate\Http\Request;

class HerramientasController extends Controller
{
    public function index()
    {
        $herramientas = Herramientas::all();
        return view('herramientas.herramientas', compact('herramientas'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        return view('herramientas.formulario-crear', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'id_categoria' => 'required',
                'nombre_herramienta' => 'required',
                'existencia' => 'required|integer',
                // 'estado' => 'required',
                // 'disponible' => 'required',
                'imagen' => 'nullable|image|max:2048|mimes:jpeg,png,jpg'
            ],
            [
                'id_categoria.required' => 'La categoría es obligatoria.',
                'nombre_herramienta.required' => 'El nombre de la herramienta es obligatorio.',
                'existencia.required' => 'La existencia es obligatoria.',
                // 'existencia.integer' => 'La existencia debe ser un número entero.',
                // 'estado.required' => 'El estado es obligatorio.',
                'disponible.required' => 'La disponibilidad es obligatoria.',
                'imagen.mimes' => 'El archivo debe ser una imagen.',
                'imagen.max' => 'La imagen no debe superar los 2MB.'
            ]
        );

        $herramienta = new Herramientas();
        $herramienta->id_categoria = $request->id_categoria;
        $herramienta->nombre_herramienta = $request->nombre_herramienta;
        $herramienta->existencia = $request->existencia;
        $herramienta->estado = "Buen Estado";
        $herramienta->disponible = 1;
        $herramienta->imagen = '/imagenes/herramientas/producto_default.png'; // Ruta de la imagen por defecto

        $herramienta->save();

        if ($request->has('imagen')) {
            $imagen = $request->imagen;
            $nuevo_nombre = 'herramienta_' . $herramienta->id_herramienta . '.jpg';
            $ruta = $imagen->storeAs('imagenes/herramientas', $nuevo_nombre, 'public');
            $herramienta->imagen = '/storage/' . $ruta;
            $herramienta->save();
        }

        return redirect()->route('herramientas.index')->with('success', 'Herramienta añadida exitosamente.');
    }

    public function edit($id)
    {
        $herramienta = Herramientas::find($id);
        $categorias = Categoria::all();
        return view('herramientas.formulario-editar', compact('herramienta', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $herramienta = Herramientas::find($id);

        $request->validate(
            [
                'id_categoria' => 'required',
                'nombre_herramienta' => 'required',
                'existencia' => 'required|integer',
                'estado' => 'required',
                'disponible' => 'required',
                'imagen' => 'nullable|mimes:jpeg,png,jpg'
            ],
            [
                'id_categoria.required' => 'La categoría es obligatoria.',
                'nombre_herramienta.required' => 'El nombre de la herramienta es obligatorio.',
                'existencia.required' => 'La existencia es obligatoria.',
                'existencia.integer' => 'La existencia debe ser un número entero.',
                'estado.required' => 'El estado es obligatorio.',
                'disponible.required' => 'La disponibilidad es obligatoria.',
                'imagen.mimes' => 'La imagen debe ser un archivo de tipo: jpeg, png, jpg.'
            ]
        );

        $herramienta->id_categoria = $request->id_categoria;
        $herramienta->nombre_herramienta = $request->nombre_herramienta;
        $herramienta->existencia = $request->existencia;
        $herramienta->estado = $request->estado;
        $herramienta->disponible = $request->disponible;

        if ($request->has('imagen')) {
            $imagen = $request->imagen;
            $nuevo_nombre = 'herramienta_' . $herramienta->id . '.jpg';
            $ruta = $imagen->storeAs('imagenes/herramientas', $nuevo_nombre, 'public');
            $herramienta->imagen = '/storage/' . $ruta;
        }

        $herramienta->save();

        return redirect()->route('herramientas.index')->with('success', 'Herramienta actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $herramienta = Herramientas::find($id);

        if ($herramienta) {
            $herramienta->update(['disponible' => 0]);

            return redirect()->route('herramientas.index')->with('success', 'Estatus cambiado a no disponible exitosamente.');
        }

        return redirect()->route('herramientas.index')->with('error', 'Herramienta no encontrada.');
    }
}
