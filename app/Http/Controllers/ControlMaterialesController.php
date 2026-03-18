<?php

namespace App\Http\Controllers;

use App\Models\Materiales;
use App\Models\Prestamos;
use App\Models\DetallePrestamos;
use App\Models\Empleados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ControlMaterialesController extends Controller
{
    public function create()
    {
        $materiales = Materiales::where('estatus', 'Disponible')
                                ->where('existencia', '>', 0)
                                ->orderBy('nombre_material')
                                ->get();

        $empleados = Empleados::orderBy('nombre')->get();

        return view('materiales.control', compact('materiales', 'empleados'));
    }

    /**
     * 
     */
    public function store(Request $request)
    {
        //VALIACION
        $request->validate([
            'id_empleado'                            => 'required|exists:empleados,id_empleado',
            'turno'                                  => 'required|in:Matutino,Vespertino,Nocturno',
            'linea_produccion'                       => 'nullable|string|max:100',

            //salida
            'materiales'                             => 'required|array|min:1',
            'materiales.*.id_material'               => 'required|exists:materiales,id_material',
            'materiales.*.cantidad_salida'           => 'required|integer|min:0',
            'materiales.*.existencia_antes'          => 'required|integer|min:0',
            'materiales.*.existencia_actualizada'    => 'required|integer|min:0',

            //devolucion
            'devolucion'                             => 'nullable|array',
            'devolucion.*.id_material'               => 'required|exists:materiales,id_material',
            'devolucion.*.cantidad_devuelta'         => 'required|integer|min:0',
            'devolucion.*.existencia_final'          => 'required|integer|min:0',
        ], [
            'id_empleado.required'  => 'Debes seleccionar un empleado.',
            'turno.required'        => 'El turno es obligatorio.',
        ]);

        DB::transaction(function () use ($request) {

            //1.crear el prrstamo cabecera
            $prestamo = Prestamos::create([
                'id_empleado'     => $request->id_empleado,
                'id_usuario'      => 1, // ← CAMBIO: temporal para pruebas (era auth()->id())
                'estatus_general' => 'Activo',
            ]);

            //2.registrar salida de cada material
            foreach ($request->materiales as $item) {
                $cantSalida = (int) $item['cantidad_salida'];

                if ($cantSalida <= 0) continue; // ignorar los que no salieron

                //detalle del prestamo
                DetallePrestamos::create([
                    'id_prestamo'              => $prestamo->id_prestamo,
                    'id_material'              => $item['id_material'],
                    'cantidad'                 => $cantSalida,
                    'cantidad_devuelta'        => 0,        // aún no regresa
                    'estatus_articulo'         => 'Prestado',
                    'fecha_devolucion_esperada'=> now()->toDateString(),
                ]);

                //descontar existencia
                Materiales::where('id_material', $item['id_material'])
                          ->decrement('existencia', $cantSalida);
            }

            //3.registrar devolución (sobrantes)
            if ($request->has('devolucion')) {
                foreach ($request->devolucion as $dev) {
                    $cantDevuelta = (int) $dev['cantidad_devuelta'];

                    //buscar el detalle correspondiente para actualizarlo
                    $detalle = DetallePrestamos::where('id_prestamo', $prestamo->id_prestamo)
                                               ->where('id_material', $dev['id_material'])
                                               ->first();

                    if ($detalle) {
                        $detalle->update([
                            'cantidad_devuelta'    => $cantDevuelta,
                            'fecha_devolucion_real'=> now(),
                            'estatus_articulo'     => $cantDevuelta >= $detalle->cantidad
                                                        ? 'Devuelto'
                                                        : 'Consumido',
                        ]);
                    }

                    //suumar los sobrantes a la existencia
                    if ($cantDevuelta > 0) {
                        Materiales::where('id_material', $dev['id_material'])
                                  ->increment('existencia', $cantDevuelta);
                    }
                }
            }

            //4.actualizar estatus general del préstamo
            $pendientes = DetallePrestamos::where('id_prestamo', $prestamo->id_prestamo)
                                          ->where('estatus_articulo', 'Prestado')
                                          ->count();

            $prestamo->update([
                'estatus_general' => $pendientes === 0 ? 'Cerrado' : 'Devuelto Parcial',
            ]);

            //5.actualizar estatus de materiales agotados
            Materiales::where('existencia', 0)
                      ->update(['estatus' => 'Agotado']);

            Materiales::where('existencia', '>', 0)
                      ->update(['estatus' => 'Disponible']);
        });

        return redirect()->route('materiales.index')
                         ->with('success', 'Registro de salida y devolución guardado correctamente.');
    }
}