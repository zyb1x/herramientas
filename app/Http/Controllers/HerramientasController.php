<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Herramientas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HerramientasController extends Controller
{
    public function index(Request $request)
    {
        $query = Herramientas::query();

        if ($request->filled('q')) {
            $query->where('nombre_herramienta', 'like', '%' . $request->q . '%');
        }

        $herramientas = $query->get();

        return view('herramientas.herramientas', compact('herramientas'));
    }

    public function buscar(Request $request)
    {
        $herramientas = Herramientas::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where('nombre_herramienta', 'like', '%' . $request->q . '%');
            })
            ->get();

        return response()->json($herramientas);
    }

    public function listado(Request $request)
    {
        $query = Herramientas::query();

        if ($request->filled('q')) {
            $query->where('nombre_herramienta', 'like', '%' . $request->q . '%');
            // Agrega más columnas si quieres buscar en más campos:
            // ->orWhere('descripcion', 'like', '%' . $request->q . '%')
        }

        $herramientas = $query->get();

        return view('herramientas.listado', compact('herramientas'));
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
                'imagen' => 'required|image|max:2048|mimes:jpeg,png,jpg'
            ],
            [
                'id_categoria.required' => 'La categoría es obligatoria.',
                'nombre_herramienta.required' => 'El nombre de la herramienta es obligatorio.',
                'existencia.required' => 'La existencia es obligatoria.',
                // 'existencia.integer' => 'La existencia debe ser un número entero.',
                // 'estado.required' => 'El estado es obligatorio.',
                'disponible.required' => 'La disponibilidad es obligatoria.',
                'imagen.required' => 'La imagen es obligatoria.',
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

        return redirect()->route('herramientas.listado')->with('success', 'Herramienta añadida exitosamente.');
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
                'id_categoria'       => 'required',
                'nombre_herramienta' => 'required',
                'existencia'         => 'required|integer',
                'estado'             => 'required',
                'imagen'             => 'nullable|mimes:jpeg,png,jpg|max:2048'
            ],
            [
                'id_categoria.required'       => 'La categoría es obligatoria.',
                'nombre_herramienta.required' => 'El nombre de la herramienta es obligatorio.',
                'existencia.required'         => 'La existencia es obligatoria.',
                'existencia.integer'          => 'La existencia debe ser un número entero.',
                'imagen.mimes'                => 'La imagen debe ser un archivo de tipo: jpeg, png, jpg.',
                'imagen.max'                  => 'La imagen no debe superar los 2MB.'
            ]
        );

        $herramienta->id_categoria       = $request->id_categoria;
        $herramienta->nombre_herramienta = $request->nombre_herramienta;
        $herramienta->existencia         = $request->existencia;
        $herramienta->estado             = $request->estado;

        if ($request->hasFile('imagen')) {
            if ($herramienta->imagen && !str_contains($herramienta->imagen, 'producto_default')) {
                $ruta_anterior = str_replace('/storage/', '', $herramienta->imagen);
                Storage::disk('public')->delete($ruta_anterior);
            }
            $nuevo_nombre = 'herramienta_' . $id . '.jpg';
            $ruta = $request->file('imagen')->storeAs('imagenes/herramientas', $nuevo_nombre, 'public');
            $herramienta->imagen = '/storage/' . $ruta;
        } elseif ($request->input('eliminar_imagen') == '1') {
            if ($herramienta->imagen && !str_contains($herramienta->imagen, 'herramienta_default')) {
                $ruta_anterior = str_replace('/storage/', '', $herramienta->imagen);
                Storage::disk('public')->delete($ruta_anterior);
            }
            $herramienta->imagen = '/imagenes/herramientas/producto_default.png';
        }

        $herramienta->save();

        return redirect()->route('herramientas.listado')->with('success', 'Herramienta actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $herramienta = Herramientas::find($id);

        if ($herramienta) {
            $herramienta->update(['disponible' => 0]);

            return redirect()->route('herramientas.listado')->with('success', 'Estatus cambiado a no disponible exitosamente.');
        }

        return redirect()->route('herramientas.listado')->with('error', 'Herramienta no encontrada.');
    }
}
