@extends('plantilla.app')

@section('titulo', 'Devolución de Herramientas')

@section('contenido')

    <div class="max-w-4xl mx-auto px-4 py-6">

        <h1 class="text-2xl font-bold text-white mb-6">Devolución de Herramientas</h1>

        {{-- Alertas --}}
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-600 text-white text-sm rounded-lg">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-600 text-white text-sm rounded-lg">{{ session('error') }}</div>
        @endif

        {{-- Paso 1: Buscar préstamo --}}
        <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg p-5 mb-6">
            <h2 class="text-lg font-bold text-white mb-4">Paso 1 — Buscar préstamo</h2>

            <form action="{{ route('devoluciones.buscar') }}" method="POST" class="flex gap-3 items-end">
                @csrf
                <div class="flex-1">
                    <label for="id_prestamo" class="block mb-2 text-sm font-medium text-gray-300">
                        ID del Préstamo
                    </label>
                    <input type="number" name="id_prestamo" id="id_prestamo"
                        value="{{ isset($prestamo) ? $prestamo->id_prestamo : old('id_prestamo') }}"
                        class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 placeholder-gray-400"
                        placeholder="Ej. 12" min="1" required>
                    @error('id_prestamo')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors">
                    Buscar
                </button>
            </form>
        </div>

        {{-- Paso 2: Tabla de devolución --}}
        @isset($prestamo)
            <form action="{{ route('devoluciones.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_prestamo" value="{{ $prestamo->id_prestamo }}">

                <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg overflow-hidden mb-6">

                    <div class="px-5 py-4 bg-[#01263a] border-b border-gray-700">
                        <h2 class="text-lg font-bold text-white">
                            Paso 2 — Herramientas del préstamo #{{ $prestamo->id_prestamo }}
                        </h2>
                        <p class="text-gray-400 text-xs mt-1">Solo se muestran las herramientas con estatus <span
                                class="text-orange-400 font-semibold">Prestado</span>.</p>
                    </div>

                    {{-- Encabezado --}}
                    <div
                        class="grid grid-cols-12 gap-2 px-5 py-3 bg-[#01263a] text-gray-400 text-xs font-semibold uppercase tracking-wider border-b border-gray-700">
                        <div class="col-span-1">Código</div>
                        <div class="col-span-4">Herramienta</div>
                        <div class="col-span-2 text-center">Cant. prestada</div>
                        <div class="col-span-2 text-center">Exist. actual</div>
                        <div class="col-span-2 text-center">Cant. a devolver</div>
                        <div class="col-span-1 text-center">Estatus</div>
                    </div>

                    {{-- Filas --}}
                    <div class="divide-y divide-gray-700">
                        @foreach ($prestamo->detalles as $detalle)
                            <div class="grid grid-cols-12 gap-2 px-5 py-4 items-center">

                                <input type="hidden" name="devoluciones[{{ $loop->index }}][id_herramienta]"
                                    value="{{ $detalle->id_herramienta }}">

                                {{-- Código --}}
                                <div class="col-span-1 text-gray-300 text-xs font-mono">
                                    #{{ $detalle->id_herramienta }}
                                </div>

                                {{-- Nombre --}}
                                <div class="col-span-4">
                                    <p class="text-white text-sm font-medium">{{ $detalle->herramienta->nombre_herramienta }}
                                    </p>
                                    <span
                                        class="text-xs
                            @if ($detalle->herramienta->estado === 'Nuevo') text-green-400
                            @elseif($detalle->herramienta->estado === 'Buen Estado') text-blue-400
                            @elseif($detalle->herramienta->estado === 'Dañado') text-red-400
                            @else text-yellow-400 @endif">
                                        {{ $detalle->herramienta->estado }}
                                    </span>
                                </div>

                                {{-- Cantidad prestada --}}
                                <div class="col-span-2 text-center text-gray-300 text-sm">
                                    {{ $detalle->cantidad }}
                                </div>

                                {{-- Existencia actual --}}
                                <div class="col-span-2 text-center text-gray-300 text-sm">
                                    {{ $detalle->herramienta->existencia }}
                                </div>

                                {{-- Cantidad a devolver --}}
                                <div class="col-span-2 text-center">
                                    <input type="number" name="devoluciones[{{ $loop->index }}][cantidad]" min="0"
                                        max="{{ $detalle->cantidad }}" value="{{ $detalle->cantidad }}"
                                        class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 w-full p-2 text-center">
                                </div>

                                {{-- Estatus hardcodeado --}}
                                <div class="col-span-1 text-center">
                                    <span class="text-xs text-blue-400 font-semibold">Buen Estado</span>
                                </div>

                            </div>
                        @endforeach
                    </div>

                </div>

                {{-- ID Empleado y confirmar --}}
                <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg p-5">
                    <div class="mb-4">
                        <label for="id_empleado" class="block mb-2 text-sm font-medium text-gray-300">
                            ID del Empleado <span class="text-gray-500 font-normal">(quien devuelve)</span>
                        </label>
                        <input type="number" name="id_empleado" id="id_empleado"
                            class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 placeholder-gray-400"
                            placeholder="Ej. 42" min="1" required>
                        @error('id_empleado')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium py-3 rounded-lg transition-colors">
                        Confirmar devolución
                    </button>
                </div>

            </form>
        @endisset

    </div>

@endsection
