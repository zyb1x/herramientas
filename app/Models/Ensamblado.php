<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ensamblado extends Model
{
    protected $table      = 'ensamblados';
    protected $primaryKey = 'id_ensamblado';
    public    $timestamps = false;

    protected $fillable = [
        'id_material',
        'id_empleado',
        'nombre',
        'cantidad',
        'cantidad_sobrante',
        'existencia_antes',
        'existencia_despues',
        'fecha_registro',
    ];

    public function material()
    {
        return $this->belongsTo(Materiales::class, 'id_material', 'id_material');
    }

    public function empleado()
    {
        return $this->belongsTo(Usuarios::class, 'id_empleado', 'id');
    }
}
