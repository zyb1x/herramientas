@extends('plantilla.app')

@section('titulo', 'Detalle del Préstamo #' . str_pad($prestamo->id_prestamo, 5, '0', STR_PAD_LEFT))

@section('contenido')

    <div class="max-w-5xl mx-auto px-4 py-6">

        {{-- Encabezado --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    Préstamo #{{ str_pad($prestamo->id_prestamo, 5, '0', STR_PAD_LEFT) }}
                </h1>
                <p class="text-gray-400 text-sm mt-1">
                    Registrado el {{ \Carbon\Carbon::parse($prestamo->fecha_prestamo)->format('d/m/Y \a \l\a\s H:i') }}
                    · por <span class="text-orange-500 font-bold">{{ $prestamo->usuario->nombre ?? '—' }}</span>
                </p>
            </div>

            {{-- Badge estatus general --}}
            @php
                $badgeClasses = match ($prestamo->estatus_general) {
                    'Activo' => 'bg-green-500/20 text-green-400 border border-green-500/40',
                    'Devuelto Parcial' => 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/40',
                    'Cerrado' => 'bg-gray-500/20 text-gray-400 border border-gray-500/40',
                    default => 'bg-blue-500/20 text-blue-400 border border-blue-500/40',
                };
            @endphp
            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $badgeClasses }}">
                {{ $prestamo->estatus_general }}
            </span>
        </div>

        {{-- Datos del empleado --}}
        <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg p-5 mb-6">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-4">Empleado</h2>
            <div class="flex items-center gap-4">
                @if (!empty($prestamo->empleado->imagen))
                    <img src="{{ $prestamo->empleado->imagen }}"
                        class="w-14 h-14 rounded-full object-cover border border-gray-600">
                @else
                    <div class="w-14 h-14 rounded-full bg-gray-700 border border-gray-600 flex items-center justify-center">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                @endif
                <div>
                    <p class="text-white font-semibold">
                        {{ $prestamo->empleado->nombre ?? '—' }}
                        {{ $prestamo->empleado->apellido_p ?? '' }}
                        {{ $prestamo->empleado->apellido_m ?? '' }}
                    </p>
                    <p class="text-gray-400 text-sm">
                        {{ $prestamo->empleado->puesto ?? '' }}
                        @if (!empty($prestamo->empleado->area))
                            · {{ $prestamo->empleado->area }}
                        @endif
                    </p>
                    <p class="text-gray-500 text-xs mt-0.5">ID #{{ $prestamo->empleado->id_empleado }}</p>
                </div>
            </div>
        </div>

        {{-- Herramientas --}}
        @php $herramientas = $prestamo->detalles->whereNotNull('id_herramienta'); @endphp
        @if ($herramientas->isNotEmpty())
            <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg overflow-hidden mb-6">
                <div class="px-5 py-4 bg-[#01263a] border-b border-gray-700">
                    <h2 class="text-lg font-bold text-white">Herramientas</h2>
                    <p class="text-gray-400 text-xs mt-0.5">{{ $herramientas->count() }} artículo(s)</p>
                </div>

                {{-- Cabecera tabla --}}
                <div
                    class="hidden md:grid grid-cols-12 gap-2 px-5 py-3 bg-[#01263a] border-b border-gray-700
                            text-gray-400 text-xs font-semibold uppercase tracking-wider">
                    <div class="col-span-1"># ID</div>
                    <div class="col-span-5">Herramienta</div>
                    <div class="col-span-2 text-center">Cant.</div>
                    <div class="col-span-4 text-center">Estatus</div>
                </div>

                <div class="divide-y divide-gray-700">
                    @foreach ($herramientas as $detalle)
                        <div class="grid grid-cols-12 gap-2 px-5 py-4 items-center">

                            {{-- ID --}}
                            <div class="col-span-2 md:col-span-1 text-gray-500 text-xs font-mono">
                                #{{ $detalle->id_herramienta }}
                            </div>

                            {{-- Nombre + estado --}}
                            <div class="col-span-10 md:col-span-5">
                                <p class="text-white text-sm font-medium">
                                    {{ $detalle->herramienta->nombre_herramienta ?? '—' }}
                                </p>
                                @if ($detalle->herramienta)
                                    @php
                                        $estadoColor = match ($detalle->herramienta->estado) {
                                            'Nuevo' => 'text-green-400',
                                            'Buen Estado' => 'text-blue-400',
                                            'Dañado' => 'text-red-400',
                                            'Reparación' => 'text-yellow-400',
                                            default => 'text-gray-400',
                                        };
                                    @endphp
                                    <span class="text-xs {{ $estadoColor }}">{{ $detalle->herramienta->estado }}</span>
                                @endif
                            </div>

                            {{-- Cantidad --}}
                            <div class="col-span-4 md:col-span-2 text-center text-gray-300 text-sm">
                                <span class="md:hidden text-gray-500 text-xs">Cant: </span>{{ $detalle->cantidad }}
                            </div>

                            {{-- Estatus artículo --}}
                            <div class="col-span-8 md:col-span-4 text-center">
                                @php
                                    $estClasses = match ($detalle->estatus_articulo) {
                                        'Prestado' => 'bg-orange-500/20 text-orange-400',
                                        'Devuelto' => 'bg-green-500/20 text-green-400',
                                        'Perdido' => 'bg-red-500/20 text-red-400',
                                        'Dañado' => 'bg-yellow-500/20 text-yellow-400',
                                        'Consumido' => 'bg-gray-500/20 text-gray-400',
                                        default => 'bg-gray-500/20 text-gray-400',
                                    };
                                @endphp
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $estClasses }}">
                                    {{ $detalle->estatus_articulo }}
                                </span>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Materiales --}}
        @php $materiales = $prestamo->detalles->whereNotNull('id_material'); @endphp
        @if ($materiales->isNotEmpty())
            <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg overflow-hidden mb-6">
                <div class="px-5 py-4 bg-[#01263a] border-b border-gray-700">
                    <h2 class="text-lg font-bold text-white">Materiales</h2>
                    <p class="text-gray-400 text-xs mt-0.5">{{ $materiales->count() }} artículo(s)</p>
                </div>

                <div
                    class="hidden md:grid grid-cols-12 gap-2 px-5 py-3 bg-[#01263a] border-b border-gray-700
                            text-gray-400 text-xs font-semibold uppercase tracking-wider">
                    <div class="col-span-1"># ID</div>
                    <div class="col-span-5">Material</div>
                    <div class="col-span-2 text-center">Cant.</div>
                    <div class="col-span-4 text-center">Estatus</div>
                </div>

                <div class="divide-y divide-gray-700">
                    @foreach ($materiales as $detalle)
                        <div class="grid grid-cols-12 gap-2 px-5 py-4 items-center">

                            {{-- ID --}}
                            <div class="col-span-2 md:col-span-1 text-gray-500 text-xs font-mono">
                                #{{ $detalle->id_material }}
                            </div>

                            {{-- Nombre + estatus --}}
                            <div class="col-span-10 md:col-span-5">
                                <p class="text-white text-sm font-medium">
                                    {{ $detalle->material->nombre_material ?? '—' }}
                                </p>
                                @if ($detalle->material)
                                    <span
                                        class="text-xs {{ $detalle->material->estatus === 'Disponible' ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $detalle->material->estatus }}
                                    </span>
                                @endif
                            </div>

                            {{-- Cantidad --}}
                            <div class="col-span-4 md:col-span-2 text-center text-gray-300 text-sm">
                                {{ $detalle->cantidad }}
                            </div>

                            {{-- Estatus artículo --}}
                            <div class="col-span-8 md:col-span-4 text-center">
                                @php
                                    $estClasses = match ($detalle->estatus_articulo) {
                                        'Prestado' => 'bg-orange-500/20 text-orange-400',
                                        'Devuelto' => 'bg-green-500/20 text-green-400',
                                        'Perdido' => 'bg-red-500/20 text-red-400',
                                        'Dañado' => 'bg-yellow-500/20 text-yellow-400',
                                        'Consumido' => 'bg-gray-500/20 text-gray-400',
                                        default => 'bg-gray-500/20 text-gray-400',
                                    };
                                @endphp
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $estClasses }}">
                                    {{ $detalle->estatus_articulo }}
                                </span>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Botón regresar --}}
        <div class="mt-2">
            <a href="{{ route('prestamos.index') }}"
                class="inline-flex items-center gap-2 font-bold text-sm text-gray-400 hover:text-orange-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Regresar
            </a>
        </div>

    </div>

@endsection
