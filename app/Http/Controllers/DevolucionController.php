<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devolucion;
use App\Models\Herramientas;
use App\Models\Materiales;
use App\Models\Prestamo;
use App\Models\DetallePrestamo;
use App\Models\Ensamblado;

class DevolucionController extends Controller
{
    public function index(Request $request)
    {
        $prestamosActivos = Prestamo::with([
            'empleado',
            'detalles' => function ($q) {
                $q->where('estatus_articulo', 'Prestado')
                  ->with(['herramienta', 'material']);
            }
        ])
            ->whereIn('estatus_general', ['Activo', 'Devuelto Parcial'])
            ->orderBy('id_prestamo', 'desc')
            ->get();

        if ($request->has('id_prestamo')) {
            $prestamo = Prestamo::with([
                'empleado',
                'detalles' => function ($q) {
                    $q->where('estatus_articulo', 'Prestado')
                      ->with(['herramienta', 'material']);
                }
            ])->find($request->id_prestamo);

            if ($prestamo && $prestamo->detalles->isEmpty()) {
                return view('devoluciones.index', compact('prestamosActivos'))
                    ->with('error', 'Este préstamo no tiene artículos pendientes de devolución.');
            }

            if ($prestamo) {
                return view('devoluciones.index', compact('prestamosActivos', 'prestamo'));
            }
        }

        return view('devoluciones.index', compact('prestamosActivos'));
    }

    public function buscarPrestamo(Request $request)
    {
        $request->validate([
            'id_prestamo' => 'required|integer|exists:prestamos,id_prestamo',
        ]);

        return redirect()->route('devoluciones.index', ['id_prestamo' => $request->id_prestamo]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_prestamo'                        => 'required|integer|exists:prestamos,id_prestamo',
            'ensamblado_nombre'                  => 'nullable|string|max:255',
            'ensamblado_cantidad'                => 'nullable|integer|min:1',
            'devoluciones'                       => 'nullable|array',
            'materiales_sobrantes'               => 'nullable|array',
        ]);

        $prestamo = Prestamo::with([
            'empleado',
            'detalles' => function ($q) {
                $q->where('estatus_articulo', 'Prestado');
            }
        ])->findOrFail($request->id_prestamo);

        $registradas = 0;

        // 1. Registrar Ensamblado y Consumir Materiales
        // Automáticamente dar por consumidos todos los materiales prestados de este préstamo
        $detallesMateriales = $prestamo->detalles->whereNotNull('id_material')->where('estatus_articulo', 'Prestado');
        foreach ($detallesMateriales as $detalle) {
            DetallePrestamo::where('id_detalle', $detalle->id_detalle)->update([
                'estatus_articulo'      => 'Devuelto',
                'fecha_devolucion_real' => now(),
            ]);
            $registradas++;
        }

        if (!empty($request->ensamblado_nombre)) {
            Ensamblado::create([
                'id_empleado'         => $prestamo->id_empleado,
                'nombre_ensamblado'   => $request->ensamblado_nombre,
                'cantidad_ensamblada' => $request->ensamblado_cantidad ?? 1,
            ]);
            $registradas++;
        }

        // 2. Registrar Devoluciones de Herramientas
        if ($request->has('devoluciones')) {
            foreach ($request->devoluciones as $item) {
                $detalle = $prestamo->detalles->firstWhere('id_herramienta', $item['id_herramienta']);
                if (!$detalle) continue;

                $cantidad = (int) $item['cantidad'];
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

                DetallePrestamo::where('id_detalle', $detalle->id_detalle)
                    ->update([
                        'estatus_articulo'      => 'Devuelto',
                        'fecha_devolucion_real' => now(),
                    ]);

                $registradas++;
            }
        }

        // 3. Actualizar Estatus General del Préstamo
        $pendientes = DetallePrestamo::where('id_prestamo', $request->id_prestamo)
            ->where('estatus_articulo', 'Prestado')
            ->count();

        if ($pendientes === 0) {
            Prestamo::where('id_prestamo', $request->id_prestamo)
                ->update(['estatus_general' => 'Cerrado']);
        } else {
            Prestamo::where('id_prestamo', $request->id_prestamo)
                ->update(['estatus_general' => 'Devuelto Parcial']);
        }

        if ($registradas === 0 && empty($request->ensamblado_nombre)) {
            return redirect()->route('devoluciones.index')
                ->with('error', 'No se registró ninguna devolución ni ensamble.');
        }

        return redirect()->route('devoluciones.index')
            ->with('success', "Devolución/Ensamble registrado correctamente.");
    }
}
