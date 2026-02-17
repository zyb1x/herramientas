<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Empleados extends Authenticatable
{
    use HasFactory;

    protected $table = 'Empleados';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'correo',
        'contrasena',
        'puesto',
        'linea_produccion',
        'turno',
    ];

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    
    public function getEmailForPasswordReset()
    {
        return $this->correo;
    }

}


?>