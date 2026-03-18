<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table      = 'devoluciones';
    protected $primaryKey = 'id_devolucion';
    public    $timestamps = false;

    protected $fillable = [
        'id_prestamo',
        'id_herramienta',
        'id_empleado',
        'cantidad_devuelta',
        'existencia_antes',
        'existencia_despues',
        'estatus_herramienta',
        'fecha_devolucion',
    ];

    protected $attributes = [
        'estatus_herramienta' => 'Buen Estado',
    ];

    public function herramienta()
    {
        return $this->belongsTo(Herramientas::class, 'id_herramienta', 'id_herramienta');
    }

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class, 'id_prestamo', 'id_prestamo');
    }
}
