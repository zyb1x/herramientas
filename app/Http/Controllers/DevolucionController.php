<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devolucion;
use App\Models\Herramientas;
use App\Models\Prestamo;
use App\Models\DetallePrestamo;

class DevolucionController extends Controller
{
    public function index(Request $request)
    {
        $prestamos = Prestamo::with('empleado')
            ->whereIn('estatus_general', ['Activo', 'Devuelto Parcial'])
            ->whereHas('detalles', function ($q) {
                $q->whereNotNull('id_herramienta')
                    ->where('estatus_articulo', 'Prestado');
            })
            ->orderByDesc('id_prestamo')
            ->get();

        if (!$request->filled('id_prestamo')) {
            return view('devoluciones.index', compact('prestamos'));
        }

        $prestamo = Prestamo::with([
            'empleado',
            'detalles' => function ($q) {
                $q->whereNotNull('id_herramienta')
                    ->where('estatus_articulo', 'Prestado')
                    ->with('herramienta');
            }
        ])->find($request->id_prestamo);

        if (!$prestamo || $prestamo->detalles->isEmpty()) {
            return view('devoluciones.index', compact('prestamos'))
                ->with('error', 'Este préstamo no tiene herramientas pendientes de devolución.');
        }

        return view('devoluciones.index', compact('prestamo', 'prestamos'));
    }

    public function buscarPrestamo(Request $request)
    {
        $prestamos = Prestamo::with('empleado')
            ->whereIn('estatus_general', ['Activo', 'Devuelto Parcial'])
            ->whereHas('detalles', function ($q) {
                $q->whereNotNull('id_herramienta')
                    ->where('estatus_articulo', 'Prestado');
            })
            ->orderByDesc('id_prestamo')
            ->get();

        $request->validate([
            'id_prestamo' => 'required|integer|exists:prestamos,id_prestamo',
        ], [

            'id_prestamo.required' => 'Selecciona el préstamo.',
        ]);

        $prestamo = Prestamo::with([
            'empleado',
            'detalles' => function ($q) {
                $q->whereNotNull('id_herramienta')
                    ->where('estatus_articulo', 'Prestado')
                    ->with('herramienta');
            }
        ])->findOrFail($request->id_prestamo);

        if ($prestamo->detalles->isEmpty()) {
            return redirect()->route('devoluciones.index')
                ->with('error', 'Este préstamo no tiene herramientas pendientes de devolución.')
                ->withInput();
        }

        return view('devoluciones.index', compact('prestamo', 'prestamos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_prestamo'                        => 'required|integer|exists:prestamos,id_prestamo',
            'devoluciones'                       => 'required|array',
            'devoluciones.*.id_herramienta'      => 'required|integer|exists:herramientas,id_herramienta',
            'devoluciones.*.cantidad'            => 'required|integer|min:1',
            'devoluciones.*.estatus_herramienta' => 'required|in:Nuevo,Buen Estado,Dañado,Reparacion',
        ], [
            'id_prestamo.required' => 'Selecciona un prestamo.',
        ]);

        $prestamo = Prestamo::with([
            'empleado',
            'detalles' => function ($q) {
                $q->whereNotNull('id_herramienta')
                    ->where('estatus_articulo', 'Prestado');
            }
        ])->findOrFail($request->id_prestamo);

        foreach ($request->devoluciones as $item) {
            $detalle = $prestamo->detalles
                ->firstWhere('id_herramienta', $item['id_herramienta']);

            if (!$detalle) {
                return redirect()->route('devoluciones.index')
                    ->with('error', 'Una de las herramientas no corresponde a este préstamo.');
            }

            if ((int) $item['cantidad'] !== (int) $detalle->cantidad) {
                $herramienta = Herramientas::find($item['id_herramienta']);
                return redirect()->route('devoluciones.index')
                    ->with('error', "Debes devolver exactamente {$detalle->cantidad} unidad(es) de \"{$herramienta->nombre_herramienta}\".");
            }
        }

        $registradas = 0;

        foreach ($request->devoluciones as $item) {
            $cantidad    = (int) $item['cantidad'];
            $herramienta = Herramientas::findOrFail($item['id_herramienta']);

            $existenciaAntes   = $herramienta->existencia;
            $existenciaDespues = $existenciaAntes + $cantidad;

            Devolucion::create([
                'id_prestamo'         => $request->id_prestamo,
                'id_herramienta'      => $herramienta->id_herramienta,
                'id_empleado'         => $prestamo->id_empleado,
                'cantidad_devuelta'   => $cantidad,
                'existencia_antes'    => $existenciaAntes,
                'existencia_despues'  => $existenciaDespues,
                'estatus_herramienta' => $item['estatus_herramienta'],
            ]);

            $herramienta->increment('existencia', $cantidad);
            $herramienta->update(['estado' => $item['estatus_herramienta']]);

            DetallePrestamo::where('id_prestamo', $request->id_prestamo)
                ->where('id_herramienta', $herramienta->id_herramienta)
                ->where('estatus_articulo', 'Prestado')
                ->update([
                    'estatus_articulo'      => 'Devuelto',
                    'fecha_devolucion_real' => now(),
                ]);

            $registradas++;
        }

        $pendientes = DetallePrestamo::where('id_prestamo', $request->id_prestamo)
            ->whereNotNull('id_herramienta')
            ->where('estatus_articulo', 'Prestado')
            ->count();

        if ($pendientes === 0) {
            Prestamo::where('id_prestamo', $request->id_prestamo)
                ->update(['estatus_general' => 'Cerrado']);
        } else {
            Prestamo::where('id_prestamo', $request->id_prestamo)
                ->update(['estatus_general' => 'Devuelto Parcial']);
        }

        if ($registradas === 0) {
            return redirect()->route('devoluciones.index')
                ->with('error', 'No se registró ninguna devolución.');
        }

        return redirect()->route('devoluciones.index')
            ->with('success', "Devolución registrada correctamente. ({$registradas} herramienta(s))");
    }
}
