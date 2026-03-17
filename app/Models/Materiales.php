<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materiales extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'materiales';
    protected $primaryKey = 'id_material';

    protected $fillable = [
        'nombre_material',
        'existencia',
        'estatus',
    ];
}
