@extends('plantilla.app')

@section('titulo', 'Devolución de Herramientas')

@section('contenido')

    <div class="max-w-5xl mx-auto px-4 py-6">

        <h1 class="text-2xl font-bold text-white mb-6">Devolución de Herramientas</h1>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-600 text-white text-sm rounded-lg">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-600 text-white text-sm rounded-lg">{{ session('error') }}</div>
        @endif

        <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg p-5 mb-6">
            <h2 class="text-lg font-bold text-white mb-4">Paso 1 — Seleccionar préstamo</h2>

            <form action="{{ route('devoluciones.buscar') }}" method="POST" class="flex gap-3 items-end">
                @csrf
                <div class="flex-1">
                    <label for="id_prestamo" class="block mb-2 text-sm font-medium text-gray-300">
                        Préstamo
                    </label>
                    <select name="id_prestamo" id="id_prestamo"
                        class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg
                       focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                        <option value="" disabled selected>Selecciona un préstamo</option>
                        @foreach ($prestamos as $p)
                            <option value="{{ $p['id_prestamo'] }}"
                                {{ isset($prestamo) && $prestamo['id_prestamo'] == $p['id_prestamo'] ? 'selected' : '' }}>
                                #{{ str_pad($p['id_prestamo'], 5, '0', STR_PAD_LEFT) }}
                                — {{ $p['empleado']['nombre'] ?? 'Sin nombre' }}
                                {{ $p['empleado']['apellido_p'] ?? '' }}
                                ({{ $p['estatus_general'] }})
                            </option>
                        @endforeach
                    </select>
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

        @isset($prestamo)
            <form action="{{ route('devoluciones.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_prestamo" value="{{ $prestamo['id_prestamo'] }}">

                {{-- Información del empleado --}}
                <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg p-5 mb-6">
                    <h2 class="text-lg font-bold text-white mb-4">Empleado del préstamo</h2>
                    <div class="flex items-center gap-4">
                        @if (!empty($prestamo['empleado']['imagen']))
                            <img src="{{ $prestamo['empleado']['imagen'] }}"
                                class="w-12 h-12 rounded-full object-cover border border-gray-600">
                        @else
                            <div
                                class="w-12 h-12 rounded-full bg-gray-700 border border-gray-600 flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                        <div>
                            <p class="text-white font-semibold text-sm">
                                {{ $prestamo['empleado']['nombre'] ?? '—' }}
                                {{ $prestamo['empleado']['apellido_p'] ?? '' }}
                                {{ $prestamo['empleado']['apellido_m'] ?? '' }}
                            </p>
                            <p class="text-gray-400 text-xs mt-0.5">
                                {{ $prestamo['empleado']['puesto'] ?? '' }}
                            </p>
                            <p class="text-gray-500 text-xs">ID #{{ $prestamo['empleado']['id_empleado'] ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Tabla de herramientas --}}
                <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg overflow-hidden mb-6">

                    <div class="px-5 py-4 bg-[#01263a] border-b border-gray-700">
                        <h2 class="text-lg font-bold text-white">
                            Paso 2 — Herramientas del préstamo #{{ $prestamo['id_prestamo'] }}
                        </h2>
                        <p class="text-gray-400 text-xs mt-1">
                            Solo se muestran las herramientas con estatus
                            <span class="text-orange-400 font-semibold">Prestado</span>.
                            La devolución debe ser por la cantidad completa prestada.
                        </p>
                    </div>

                    <div
                        class="grid grid-cols-12 gap-2 px-5 py-3 bg-[#01263a] text-gray-400 text-xs font-semibold uppercase tracking-wider border-b border-gray-700">
                        <div class="col-span-1">Código</div>
                        <div class="col-span-3">Herramienta</div>
                        <div class="col-span-1 text-center">Prestada</div>
                        <div class="col-span-1 text-center">Exist. actual</div>
                        <div class="col-span-1 text-center">A devolver</div>
                        <div class="col-span-2 text-center">Exist. después</div>
                        <div class="col-span-3 text-center">Estatus devolución</div>
                    </div>

                    <div class="divide-y divide-gray-700">
                        @foreach ($prestamo['detalles'] as $index => $detalle)
                            <div class="grid grid-cols-12 gap-2 px-5 py-4 items-center">

                                <input type="hidden" name="devoluciones[{{ $index }}][id_herramienta]"
                                    value="{{ $detalle['id_herramienta'] }}">
                                <input type="hidden" name="devoluciones[{{ $index }}][cantidad]"
                                    value="{{ $detalle['cantidad'] }}">

                                <div class="col-span-1 text-gray-300 text-xs font-mono">
                                    #{{ $detalle['id_herramienta'] }}
                                </div>

                                <div class="col-span-3">
                                    <p class="text-white text-sm font-medium">
                                        {{ $detalle['herramienta']['nombre_herramienta'] ?? '—' }}
                                    </p>
                                    @if (!empty($detalle['herramienta']['estado']))
                                        <span
                                            class="text-xs
                                            @if ($detalle['herramienta']['estado'] === 'Nuevo') text-green-400
                                            @elseif($detalle['herramienta']['estado'] === 'Buen Estado') text-blue-400
                                            @elseif($detalle['herramienta']['estado'] === 'Dañado') text-red-400
                                            @else text-yellow-400 @endif">
                                            {{ $detalle['herramienta']['estado'] }}
                                        </span>
                                    @endif
                                </div>

                                <div class="col-span-1 text-center text-gray-300 text-sm">
                                    {{ $detalle['cantidad'] }}
                                </div>

                                <div class="col-span-1 text-center text-gray-300 text-sm">
                                    {{ $detalle['herramienta']['existencia'] ?? '—' }}
                                </div>

                                <div class="col-span-1 text-center text-white font-semibold text-sm">
                                    {{ $detalle['cantidad'] }}
                                </div>

                                @php $despues = ($detalle['herramienta']['existencia'] ?? 0) + $detalle['cantidad']; @endphp
                                <div
                                    class="col-span-2 text-center text-sm font-semibold
                                    {{ $despues <= 0 ? 'text-red-400' : ($despues <= 3 ? 'text-yellow-400' : 'text-green-400') }}">
                                    {{ $despues }}
                                </div>

                                <div class="col-span-3">
                                    <select name="devoluciones[{{ $index }}][estatus_herramienta]"
                                        class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 w-full p-2">
                                        <option value="Nuevo">Nuevo</option>
                                        <option value="Buen Estado" selected>Buen Estado</option>
                                        <option value="Dañado">Dañado</option>
                                        <option value="Reparación">Reparación</option>
                                    </select>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg p-5">
                    <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium py-3 rounded-lg transition-colors">
                        Confirmar devolución
                    </button>
                </div>

            </form>
        @endisset

    </div>

@endsection
