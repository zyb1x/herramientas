<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->increments('id_devolucion');
            $table->integer('id_prestamo');
            $table->integer('id_herramienta');
            $table->integer('id_empleado');
            $table->integer('cantidad_devuelta');
            $table->integer('existencia_antes');
            $table->integer('existencia_despues');
            $table->enum('estatus_herramienta', ['Nuevo', 'Buen Estado', 'Dañado', 'Reparación'])->default('Buen Estado');
            $table->timestamp('fecha_devolucion')->useCurrent();
        });

        Schema::create('ensamblados', function (Blueprint $table) {
            $table->increments('id_ensamblado');
            $table->integer('id_herramienta');
            $table->integer('id_empleado');
            $table->integer('cantidad_sobrante');
            $table->integer('existencia_antes');
            $table->integer('existencia_despues');
            $table->timestamp('fecha_registro')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ensamblados');
        Schema::dropIfExists('devoluciones');
    }
};
