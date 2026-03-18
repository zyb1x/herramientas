<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePrestamo extends Model
{
    protected $table      = 'detalle_prestamos';
    protected $primaryKey = 'id_detalle';
    public    $timestamps = false;

    protected $fillable = [
        'id_prestamo',
        'id_herramienta',
        'id_material',
        'cantidad',
        'fecha_devolucion_esperada',
        'fecha_devolucion_real',
        'estatus_articulo',
    ];

    public function herramienta()
    {
        return $this->belongsTo(Herramientas::class, 'id_herramienta', 'id_herramienta');
    }

    public function material()
    {
        return $this->belongsTo(Materiales::class, 'id_material', 'id_material');
    }

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class, 'id_prestamo', 'id_prestamo');
    }
}
