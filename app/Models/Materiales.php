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

    protected $casts = [
        'existencia' => 'integer',
    ];

    public function detalles()
    {
        return $this->hasMany(DetallePrestamos::class, 'id_material', 'id_material');
    }
}


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamos extends Model
{
    use HasFactory;

    protected $table = 'prestamos';
    protected $primaryKey = 'id_prestamo';

    protected $fillable = [
        'id_empleado',
        'id_usuario',
        'estatus_general',
    ];

    protected $casts = [
        'fecha_prestamo' => 'datetime',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleados::class, 'id_empleado', 'id_empleado');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePrestamos::class, 'id_prestamo', 'id_prestamo');
    }
}


//  app/Models/DetallePrestamos.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePrestamos extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'detalle_prestamos';
    protected $primaryKey = 'id_detalle';

    protected $fillable = [
        'id_prestamo',
        'id_herramienta',
        'id_material',
        'cantidad',
        'cantidad_devuelta',
        'fecha_devolucion_esperada',
        'fecha_devolucion_real',
        'estatus_articulo',
    ];

    protected $casts = [
        'fecha_devolucion_esperada' => 'date',
        'fecha_devolucion_real'     => 'datetime',
        'cantidad'                  => 'integer',
        'cantidad_devuelta'         => 'integer',
    ];

    public function prestamo()
    {
        return $this->belongsTo(Prestamos::class, 'id_prestamo', 'id_prestamo');
    }

    public function material()
    {
        return $this->belongsTo(Materiales::class, 'id_material', 'id_material');
    }

    public function herramienta()
    {
        return $this->belongsTo(Herramientas::class, 'id_herramienta', 'id_herramienta');
    }
}

//  app/Models/Empleados.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleados extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'empleados';
    protected $primaryKey = 'id_empleado';

    protected $fillable = [
        'nombre',
        'apellido_p',
        'apellido_m',
        'correo',
        'puesto',
        'turno',
        'linea_produccion',
        'imagen',
    ];

    public function prestamos()
    {
        return $this->hasMany(Prestamos::class, 'id_empleado', 'id_empleado');
    }
}


//
//  use App\Http\Controllers\ControlMaterialesController;
//
//  Route::get('/materiales/control',  [ControlMaterialesController::class, 'create'])->name('materiales.control');
//  Route::post('/materiales/control', [ControlMaterialesController::class, 'store'])->name('prestamos.salida.store');
//