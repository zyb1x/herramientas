<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    protected $table      = 'prestamos';
    protected $primaryKey = 'id_prestamo';
    public    $timestamps = false;

    protected $fillable = [
        'id_empleado',
        'id_usuario',
        'estatus_general',
    ];

    protected $attributes = [
        'estatus_general' => 'Activo',
    ];

    public function detalles()
    {
        return $this->hasMany(DetallePrestamo::class, 'id_prestamo', 'id_prestamo');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleados::class, 'id_empleado', 'id_empleado');
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Usuarios::class, 'id_usuario', 'id');
    }
}
