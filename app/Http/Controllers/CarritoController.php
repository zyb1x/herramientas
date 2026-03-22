<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Herramientas;
use App\Models\Materiales;
use App\Models\Prestamo;
use App\Models\DetallePrestamo;

class CarritoController extends Controller
{
    public function index()
    {
        $items = Cart::content();
        return view('carrito.carrito', compact('items'));
    }

    public function agregarHerramienta(Request $request)
    {
        $request->validate([
            'id'       => 'required|exists:herramientas,id_herramienta',
            'cantidad' => 'required|integer|min:1',
        ]);

        $herramienta = Herramientas::findOrFail($request->id);

        $enCarrito = 0;
        foreach (Cart::content() as $item) {
            if ($item->options->tipo === 'herramienta' && $item->id == $herramienta->id_herramienta) {
                $enCarrito = $item->qty;
                break;
            }
        }

        if (($enCarrito + $request->cantidad) > $herramienta->existencia) {
            return back()->with('error', 'La cantidad solicitada excede la existencia disponible.');
        }

        Cart::add([
            'id'      => $herramienta->id_herramienta,
            'name'    => $herramienta->nombre_herramienta,
            'qty'     => $request->cantidad,
            'price'   => 0,
            'options' => [
                'tipo'       => 'herramienta',
                'existencia' => $herramienta->existencia,
                'estado'     => $herramienta->estado,
                'imagen'     => $herramienta->imagen,
            ],
        ]);

        return redirect()->route('carrito.index')->with('success', 'Herramienta agregada al carrito.');
    }

    public function agregarMaterial(Request $request)
    {
        $request->validate([
            'id'       => 'required|exists:materiales,id_material',
            'cantidad' => 'required|integer|min:1',
        ]);

        $material = Materiales::findOrFail($request->id);

        $enCarrito = 0;
        foreach (Cart::content() as $item) {
            if ($item->options->tipo === 'material' && $item->id == $material->id_material) {
                $enCarrito = $item->qty;
                break;
            }
        }

        if (($enCarrito + $request->cantidad) > $material->existencia) {
            return back()->with('error', 'La cantidad solicitada excede la existencia disponible.');
        }

        Cart::add([
            'id'      => $material->id_material,
            'name'    => $material->nombre_material,
            'qty'     => $request->cantidad,
            'price'   => 0,
            'options' => [
                'tipo'       => 'material',
                'existencia' => $material->existencia,
                'estatus'    => $material->estatus,
            ],
        ]);

        return redirect()->route('carrito.index')->with('success', 'Material agregado al carrito.');
    }

    public function eliminar($rowId)
    {
        Cart::remove($rowId);
        return redirect()->route('carrito.index')->with('success', 'Artículo eliminado del carrito.');
    }

    public function vaciar()
    {
        Cart::destroy();
        return redirect()->route('carrito.index')->with('success', 'Carrito vaciado.');
    }

    public function confirmar(Request $request)
    {
        $request->validate([
            'id_empleado'  => 'required|integer|exists:empleados,id_empleado',
            'cantidades'   => 'required|array',
            'cantidades.*' => 'required|integer|min:1',
        ]);

        $items = Cart::content();

        if ($items->isEmpty()) {
            return back()->with('error', 'El carrito está vacío.');
        }

        foreach ($request->cantidades as $rowId => $cantidad) {
            $item = Cart::get($rowId);
            if ($item && $cantidad >= 1 && $cantidad <= $item->options->existencia) {
                Cart::update($rowId, (int) $cantidad);
            }
        }

        $items = Cart::content();

        foreach ($items as $item) {
            if ($item->options->tipo === 'herramienta') {
                $herramienta = Herramientas::findOrFail($item->id);
                if ($item->qty > $herramienta->existencia) {
                    return back()->with('error', "Existencia insuficiente para: {$herramienta->nombre_herramienta}");
                }
            } else {
                $material = Materiales::findOrFail($item->id);
                if ($item->qty > $material->existencia) {
                    return back()->with('error', "Existencia insuficiente para: {$material->nombre_material}");
                }
            }
        }

        $prestamo = Prestamo::create([
            'id_empleado' => $request->id_empleado,
            'id_usuario'  => Auth::id(),
        ]);

        foreach ($items as $item) {
            if ($item->options->tipo === 'herramienta') {
                $herramienta = Herramientas::findOrFail($item->id);

                DetallePrestamo::create([
                    'id_prestamo'      => $prestamo->id_prestamo,
                    'id_herramienta'   => $herramienta->id_herramienta,
                    'id_material'      => null,
                    'cantidad'         => $item->qty,
                    'estatus_articulo' => 'Prestado',
                ]);

                $herramienta->decrement('existencia', $item->qty);
            } else {
                $material = Materiales::findOrFail($item->id);

                DetallePrestamo::create([
                    'id_prestamo'      => $prestamo->id_prestamo,
                    'id_herramienta'   => null,
                    'id_material'      => $material->id_material,
                    'cantidad'         => $item->qty,
                    'estatus_articulo' => 'Prestado',
                ]);

                $material->decrement('existencia', $item->qty);

                if ($material->fresh()->existencia <= 0) {
                    $material->update(['estatus' => 'Agotado']);
                }
            }
        }

        Cart::destroy();

        return redirect()->route('carrito.index')
            ->with('success', "Préstamo #{$prestamo->id_prestamo} registrado correctamente.");
    }
}
