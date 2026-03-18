<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devolucion;
use App\Models\Herramientas;
use App\Models\Prestamo;
use App\Models\DetallePrestamo;

class DevolucionController extends Controller
{
    public function index()
    {
        return view('devoluciones.index');
    }

    // Buscar préstamo por ID y devolver sus herramientas activas
    public function buscarPrestamo(Request $request)
    {
        $request->validate([
            'id_prestamo' => 'required|integer|exists:prestamos,id_prestamo',
        ]);

        $prestamo = Prestamo::with([
            'detalles' => function ($q) {
                $q->whereNotNull('id_herramienta')
                    ->where('estatus_articulo', 'Prestado')
                    ->with('herramienta');
            }
        ])->findOrFail($request->id_prestamo);

        if ($prestamo->detalles->isEmpty()) {
            return back()->with('error', 'Este préstamo no tiene herramientas pendientes de devolución.');
        }

        return view('devoluciones.index', compact('prestamo'));
    }

    // Registrar la devolución
    public function store(Request $request)
    {
        $request->validate([
            'id_prestamo'    => 'required|integer|exists:prestamos,id_prestamo',
            'id_empleado'    => 'required|integer',
            'devoluciones'   => 'required|array',
            'devoluciones.*.id_herramienta' => 'required|integer|exists:herramientas,id_herramienta',
            'devoluciones.*.cantidad'       => 'required|integer|min:0',
        ]);

        $registradas = 0;

        foreach ($request->devoluciones as $item) {
            $cantidad = (int) $item['cantidad'];

            // Si cantidad es 0 se omite
            if ($cantidad <= 0) continue;

            $herramienta = Herramientas::findOrFail($item['id_herramienta']);
            $existenciaAntes   = $herramienta->existencia;
            $existenciaDespues = $existenciaAntes + $cantidad;

            // Registrar devolución
            Devolucion::create([
                'id_prestamo'        => $request->id_prestamo,
                'id_herramienta'     => $herramienta->id_herramienta,
                'id_empleado'        => $request->id_empleado,
                'cantidad_devuelta'  => $cantidad,
                'existencia_antes'   => $existenciaAntes,
                'existencia_despues' => $existenciaDespues,
                'estatus_herramienta' => 'Buen Estado',
            ]);

            // Sumar existencia
            $herramienta->increment('existencia', $cantidad);

            // Actualizar estatus del detalle del préstamo
            DetallePrestamo::where('id_prestamo', $request->id_prestamo)
                ->where('id_herramienta', $herramienta->id_herramienta)
                ->where('estatus_articulo', 'Prestado')
                ->update([
                    'estatus_articulo'      => 'Devuelto',
                    'fecha_devolucion_real' => now(),
                ]);

            $registradas++;
        }

        if ($registradas === 0) {
            return back()->with('error', 'Ingresa al menos una cantidad mayor a 0.');
        }

        return redirect()->route('devoluciones.index')
            ->with('success', "Devolución registrada correctamente. ({$registradas} herramienta(s))");
    }
}
