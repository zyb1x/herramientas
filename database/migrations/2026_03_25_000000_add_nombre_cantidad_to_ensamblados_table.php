<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ensamblados', function (Blueprint $table) {
            $table->string('nombre_ensamblado')->nullable()->after('id_empleado');
            $table->integer('cantidad_ensamblada')->nullable()->after('nombre_ensamblado');
        });
    }

    public function down(): void
    {
        Schema::table('ensamblados', function (Blueprint $table) {
            $table->dropColumn(['nombre_ensamblado', 'cantidad_ensamblada']);
        });
    }
};
