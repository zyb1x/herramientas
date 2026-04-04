@extends('plantilla.app')

@section('titulo', 'Registro de Ensamblado')

@section('contenido')

    <section class="bg-white dark:bg-white min-h-screen">
        <div class="py-10 px-4 mx-auto max-w-2xl lg:py-16 mt-10 mb-20">

            <div class="bg-[#023047] rounded-xl shadow-lg p-8">
                <h2 class="mb-2 text-2xl font-bold text-white text-center">Registro de Ensamblado</h2>
                <p class="text-center text-blue-200 text-sm mb-8">Completa los datos del ensamblado</p>


                <form action="{{ route('ensamblados.store') }}" method="POST">
                    @csrf

                    {{-- ID Empleado --}}
                    <div class="mb-5">
                        <label for="id_empleado" class="block mb-2 text-sm font-medium text-white">
                            Supervisor
                        </label>
                        <select name="id_empleado" id="id_empleado"
                            class="bg-gray-700 border border-gray-600 text-white placeholder-gray-400 text-sm rounded-lg
               focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5
               @error('id_empleado') border-red-500 ring-1 ring-red-500 @enderror">
                            <option value="" disabled selected>Selecciona un supervisor</option>
                            @foreach ($supervisores as $supervisor)
                                {{-- <option value="{{ $supervisor->id }}"
                                    {{ old('id_empleado') == $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->nombre }}
                                </option> --}}
                                <option value="{{ $supervisor['id'] }}"
                                    {{ old('id_empleado') == $supervisor['id'] ? 'selected' : '' }}>
                                    {{ $supervisor['nombre'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_empleado')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- campo id_material --}}
                    <div class="mb-5">
                        <label for="id_material" class="block mb-2 text-sm font-medium text-white">
                            Material
                        </label>
                        <select name="id_material" id="id_material"
                            class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg
               focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5
               @error('id_material') border-red-500 ring-1 ring-red-500 @enderror">
                            <option value="" disabled selected>Selecciona un material</option>
                            @foreach ($materiales as $material)
                                <option value="{{ $material['id_material'] }}"
                                    {{ old('id_material') == $material['id_material'] ? 'selected' : '' }}>
                                    {{ $material['nombre_material'] }} ({{ $material['existencia'] }} disponibles)
                                </option>
                            @endforeach
                        </select>
                        @error('id_material')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{--  cantidad_sobrante --}}
                    <div class="mb-5">
                        <label for="cantidad_sobrante" class="block mb-2 text-sm font-medium text-white">
                            Cantidad sobrante
                        </label>
                        <input type="number" name="cantidad_sobrante" id="cantidad_sobrante"
                            value="{{ old('cantidad_sobrante') }}" placeholder="Ej: 10" min="0"
                            class="bg-gray-700 border border-gray-600 text-white placeholder-gray-400 text-sm rounded-lg
               focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5
               @error('cantidad_sobrante') border-red-500 ring-1 ring-red-500 @enderror">
                        @error('cantidad_sobrante')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nombre --}}
                    {{-- <div class="mb-5">
                        <label for="nombre" class="block mb-2 text-sm font-medium text-white">
                            Nombre del Ensamblado
                        </label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                            placeholder="Ej: Tarjeta Electrónica"
                            class="bg-gray-700 border border-gray-600 text-white placeholder-gray-400 text-sm rounded-lg
                               focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5
                               @error('nombre') border-red-500 ring-1 ring-red-500 @enderror">
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div> --}}

                    {{-- Cantidad --}}
                    {{-- <div class="mb-5">
                        <label for="cantidad" class="block mb-2 text-sm font-medium text-white">
                            Cantidad
                        </label>
                        <input type="number" name="cantidad" id="cantidad" value="{{ old('cantidad') }}"
                            placeholder="Ej: 10" 
                            class="bg-gray-700 border border-gray-600 text-white placeholder-gray-400 text-sm rounded-lg
                               focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5
                               @error('cantidad') border-red-500 ring-1 ring-red-500 @enderror">
                        @error('cantidad')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div> --}}

                    {{-- Fecha de Registro --}}
                    {{-- <div class="mb-6">
                        <label for="fecha_registro" class="block mb-2 text-sm font-medium text-white">
                            Fecha de Registro
                        </label>
                        <input type="datetime-local" name="fecha_registro" id="fecha_registro"
                            value="{{ old('fecha_registro', now()->format('Y-m-d\TH:i')) }}"
                            class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg
                               focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5
                               @error('fecha_registro') border-red-500 ring-1 ring-red-500 @enderror">
                        @error('fecha_registro')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div> --}}

                    <button type="submit"
                        class="w-full text-white bg-[#fb5607] hover:bg-orange-600 focus:ring-4
                           focus:outline-none focus:ring-orange-300 font-medium rounded-lg
                           text-sm px-5 py-2.5 text-center transition-colors">
                        Registrar Ensamblado
                    </button>
                </form>
            </div>

        </div>
    </section>

@endsection
