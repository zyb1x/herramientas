@extends('plantilla.app')

@section('titulo', 'login registro')

@section('contenido')

    <section class="bg-white dark:bg-White">
        <div class="py-8 px-4 mx-auto max-w-2xl lg:py-16 bg-[#023047] rounded-lg shadow dark:bg-[#023047] mt-8 mb-10">
            <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Registrate</h2>
            <form action="/registro/store" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                    
                    <div class="sm:col-span-2">
                        <label for="name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombres</label>
                        <input type="text" name="nombre" id="nombre"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Nombre(s)" required="">
                    </div>
                    <div>
                        <label for="paterno" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Apellido
                            Paterno</label>
                        <input type="text" name="apellido_p" id="apellido_p"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Apellido Paterno" required="">
                    </div>

                    <div>
                        <label for="materno" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Apellido
                            Materno</label>
                        <input type="text" name="apellido_m" id="apellido_m"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Apellido Materno" required="">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="correo"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Correo Electrónico</label>
                        <input type="email" name="correo" id="correo"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="ejemplo@dominio.com" required="">
                    </div>
                    <div>
                        <label for="contraseña" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contraseña</label>
                        <input type="password" name="contrasena" id="contrasena"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Contraseña" required="">
                    </div>

                    <div>
                        <label for="confirmar_contraseña" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirmar Contraseña</label>
                        <input type="password" name="conf_contrasena" id="conf_contraseña"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Confirmar Contraseña" required="">
                    </div>
                    <div>
                        <label for="puesto" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Puesto</label>
                        <input type="text" name="puesto" id="puesto"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Puesto" required="">
                    </div>
                    <div>
                        <label for="area" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Área</label>
                        <input type="text" name="area" id="area"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Área" required="">
                    </div>

                    <!-- Categoría y peso - cada uno en su columna -->
                    <div>
                        <label for="turno"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Turno</label>
                        <select name="turno" id="turno"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option selected="">Selecciona el turno</option>
                            <option value="Matutino">Matutino</option>
                            <option value="Vespertino">Vespertino</option>
                            <option value="Nocturno">Nocturno</option>
                            
                        </select>
                    </div>
                    <!-- Descripción - ocupa ambas columnas -->
                    {{-- <div class="sm:col-span-2">
                        <label for="description"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
                        <textarea id="description" rows="8"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Your description here"></textarea>
                    </div> --}}
                </div>

                <button type="submit"
                    class="w-full text-white bg-[#fb5607] hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-[#fb5607] mt-10 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                    Registrarse
                </button>
            </form>
        </div>
    </section>

@endsection
