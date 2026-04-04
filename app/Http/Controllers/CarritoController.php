<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Gloudemans\Shoppingcart\Facades\Cart;


class CarritoController extends Controller
{
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://127.0.0.1:8000/api');
    }

    // GET /carrito
    public function index()
    {
        $items = Cart::content();
        return view('carrito.carrito', compact('items'));
    }

    // POST /carrito/agregar/herramienta
    public function agregarHerramienta(Request $request)
    {
        $request->validate([
            'id'       => 'required|integer',
            'cantidad' => 'required|integer|min:1',
        ]);

        // Obtener herramienta de la API
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/herramientas/{$request->id}");

        if (!$response->successful()) {
            return back()->with('error', 'Herramienta no encontrada.');
        }

        $herramienta = $response->json()['data'];

        if (!$herramienta['disponible']) {
            return back()->with('error', "La herramienta \"{$herramienta['nombre_herramienta']}\" no está disponible.");
        }

        // Verificar cantidad en carrito
        $enCarrito = 0;
        foreach (Cart::content() as $item) {
            if ($item->options->tipo === 'herramienta' && $item->id == $herramienta['id_herramienta']) {
                $enCarrito = $item->qty;
                break;
            }
        }

        if (($enCarrito + $request->cantidad) > $herramienta['existencia']) {
            return back()->with('error', 'La cantidad solicitada excede la existencia disponible.');
        }

        Cart::add([
            'id'      => $herramienta['id_herramienta'],
            'name'    => $herramienta['nombre_herramienta'],
            'qty'     => $request->cantidad,
            'price'   => 0,
            'options' => [
                'tipo'       => 'herramienta',
                'existencia' => $herramienta['existencia'],
                'estado'     => $herramienta['estado'],
                'imagen'     => $herramienta['imagen'],
            ],
        ]);

        return redirect()->route('carrito.index')->with('success', 'Herramienta agregada al carrito.');
    }

    // POST /carrito/agregar/material
    public function agregarMaterial(Request $request)
    {
        $request->validate([
            'id'       => 'required|integer',
            'cantidad' => 'required|integer|min:1',
        ]);

        // Obtener material de la API
        $response = Http::withToken(session('api_token'))
            ->get("{$this->apiUrl}/materiales/{$request->id}");

        if (!$response->successful()) {
            return back()->with('error', 'Material no encontrado.');
        }

        $material = $response->json()['data'];

        if ($material['estatus'] === 'Agotado') {
            return back()->with('error', "El material \"{$material['nombre_material']}\" está agotado.");
        }

        // Verificar cantidad en carrito
        $enCarrito = 0;
        foreach (Cart::content() as $item) {
            if ($item->options->tipo === 'material' && $item->id == $material['id_material']) {
                $enCarrito = $item->qty;
                break;
            }
        }

        if (($enCarrito + $request->cantidad) > $material['existencia']) {
            return back()->with('error', 'La cantidad solicitada excede la existencia disponible.');
        }

        Cart::add([
            'id'      => $material['id_material'],
            'name'    => $material['nombre_material'],
            'qty'     => $request->cantidad,
            'price'   => 0,
            'options' => [
                'tipo'       => 'material',
                'existencia' => $material['existencia'],
                'estatus'    => $material['estatus'],
            ],
        ]);

        return redirect()->route('carrito.index')->with('success', 'Material agregado al carrito.');
    }

    // DELETE /carrito/eliminar/{rowId}
    public function eliminar($rowId)
    {
        Cart::remove($rowId);
        return redirect()->route('carrito.index')->with('success', 'Artículo eliminado del carrito.');
    }

    // DELETE /carrito/vaciar
    public function vaciar()
    {
        Cart::destroy();
        return redirect()->route('carrito.index')->with('success', 'Carrito vaciado.');
    }

    // POST /carrito/confirmar — manda el prestamo completo a la API
    public function confirmar(Request $request)
    {
        $request->validate([
            'id_empleado'  => 'required|integer',
            'cantidades'   => 'required|array',
            'cantidades.*' => 'required|integer|min:1',
        ]);

        $items = Cart::content();

        if ($items->isEmpty()) {
            return back()->with('error', 'El carrito está vacío.');
        }

        // actualizar cantidades en el carrito
        foreach ($request->cantidades as $rowId => $cantidad) {
            $item = Cart::get($rowId);
            if ($item && $cantidad >= 1 && $cantidad <= $item->options->existencia) {
                Cart::update($rowId, (int) $cantidad);
            }
        }

        $items = Cart::content();

        // array de articulos para la API uwu
        $articulos = [];
        foreach ($items as $item) {
            $articulos[] = [
                'tipo'     => $item->options->tipo,
                'id'       => $item->id,
                'cantidad' => $item->qty,
            ];
        }

        // mandar el prestamo a la API
        $response = Http::withToken(session('api_token'))
            ->post("{$this->apiUrl}/prestamos", [
                'id_empleado' => $request->id_empleado,
                'articulos'   => $articulos,
            ]);

        $data = $response->json();

        if (!$data['success']) {
            return back()->with('error', $data['mensaje']);
        }

        Cart::destroy();

        $idPrestamo = $data['data']['id_prestamo'];

        return redirect()->route('carrito.index')
            ->with('success', "Préstamo #{$idPrestamo} registrado correctamente.");
    }
}
