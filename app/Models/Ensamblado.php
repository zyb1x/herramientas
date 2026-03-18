<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ensamblado extends Model
{
    protected $table      = 'ensamblados';
    protected $primaryKey = 'id_ensamblado';
    public    $timestamps = false;

    protected $fillable = [
        'id_herramienta',
        'id_empleado',
        'cantidad_sobrante',
        'existencia_antes',
        'existencia_despues',
        'fecha_registro',
    ];

    public function herramienta()
    {
        return $this->belongsTo(Herramientas::class, 'id_herramienta', 'id_herramienta');
    }
}