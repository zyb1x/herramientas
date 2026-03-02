<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Administradores;
use App\Models\Empleados;
use Illuminate\Support\Facades\Hash;


class AdministradoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Empleados::create([
            'nombre' => 'Luis Daniel',
            'apellido_p' => 'Camacho',
            'apellido_m' => 'Plascencia',
            'correo' => 'dluis98@hotmail.com',
            'contrasena' => Hash::make('385621'),
            'puesto' => 'Administrador',
            'area' => 1,
            'turno' => 'Matutino',
            'imagen' => '',
        ]);
    }
}
