@extends('plantilla.app')

@section('titulo', 'registro material')

@section('contenido')


    <section class="bg-white dark:bg-White">
        <div class="py-8 px-4 mx-auto max-w-2xl lg:py-16 bg-[#023047] rounded-lg shadow dark:bg-[#023047] mt-20 mb-20">
            <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Registrar Material</h2>
            <form action="/materiales/store" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">

                    <div class="sm:col-span-2">
                        <label for="nombre_material"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre
                            del material</label>
                        <input type="text" name="nombre_material" id="nombre_material"
                            value="{{ old('nombre_material') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            @error('nombre_material')
                                style="border: 1px solid #ef4444; box-shadow: 0 0 0 1px #ef4444;"
                            @else
                                border-gray-300 dark:border-gray-600
                            @enderror
                            placeholder="Nombre de la herramienta">
                        @error('nombre_material')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="existencia"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Existencia</label>
                        <input type="number" name="existencia" id="existencia" value="{{ old('existencia') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            @error('existencia')
                                style="border: 1px solid #ef4444; box-shadow: 0 0 0 1px #ef4444;"
                            @else
                                border-gray-300 dark:border-gray-600
                            @enderror
                            placeholder="Ingrese la cantidad en existencia">
                        @error('existencia')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="estatus" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado de
                            la herramienta</label>
                        <select name="estatus" id="estatus"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            @error('estatus')
                                style="border: 1px solid #ef4444; box-shadow: 0 0 0 1px #ef4444;"
                            @else
                                border-gray-300 dark:border-gray-600
                            @enderror>
                            <option value="" selected disabled>Selecciona el estatus del material</option>
                            <option value="Disponible">Disponible</option>
                            <option value="Agotado">Agotado</option>
                        </select>
                        @error('estatus')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <button type="submit"
                    class="w-full text-white bg-[#fb5607] hover:bg-orange-600 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mt-6">
                    Registrar
                </button>
            </form>
        </div>
    </section>

@endsection
