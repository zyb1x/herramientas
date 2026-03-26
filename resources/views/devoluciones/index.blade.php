@extends('plantilla.app')

@section('titulo', 'Devolución de Herramientas y Materiales')

@section('contenido')

    <div class="max-w-5xl mx-auto px-4 py-6">

        <h1 class="text-2xl font-bold text-white mb-6">Devolución y Registro de Ensamble</h1>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-600 text-white text-sm rounded-lg">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-600 text-white text-sm rounded-lg">{{ session('error') }}</div>
        @endif

        @if (!isset($prestamo))
            <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg p-5 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-white">Paso 1 — Selecciona un Préstamo a Devolver</h2>
                    
                    <form action="{{ route('devoluciones.buscar') }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="number" name="id_prestamo" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg w-32 p-2 focus:ring-orange-500 focus:border-orange-500" placeholder="ID Manual" required>
                        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">Buscar</button>
                    </form>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 border-t border-gray-700 pt-5">
                    @forelse($prestamosActivos as $pa)
                        <a href="{{ route('devoluciones.index', ['id_prestamo' => $pa->id_prestamo]) }}" class="flex flex-col p-4 border border-gray-600 rounded-lg bg-[#01263a] hover:bg-gray-700 hover:border-orange-400 transition duration-200">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-orange-400 font-bold text-lg">Préstamo #{{ $pa->id_prestamo }}</span>
                                <span class="bg-blue-100/10 text-blue-300 border border-blue-400 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded">{{ $pa->estatus_general }}</span>
                            </div>
                            <p class="text-white text-sm font-medium mb-1 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ $pa->empleado->nombre ?? 'Sin nombre' }} {{ $pa->empleado->apellido_p ?? '' }}
                            </p>
                            <div class="text-gray-400 text-xs mt-2 mb-4 flex-1">
                                <p class="font-semibold text-gray-500 mb-1 border-b border-gray-600 pb-1">Artículos pendientes:</p>
                                <ul class="list-disc list-inside space-y-0.5 pl-1">
                                    @foreach($pa->detalles->take(4) as $det)
                                        <li class="truncate" title="{{ $det->herramienta ? $det->herramienta->nombre_herramienta : ($det->material ? $det->material->nombre_material : '') }}">
                                            <span class="font-bold text-gray-300">{{ $det->cantidad }}x</span> 
                                            @if($det->herramienta)
                                                {{ $det->herramienta->nombre_herramienta }} <span class="text-[10px] text-orange-400 opacity-80">(H)</span>
                                            @elseif($det->material)
                                                {{ $det->material->nombre_material }} <span class="text-[10px] text-blue-400 opacity-80">(M)</span>
                                            @endif
                                        </li>
                                    @endforeach
                                    @if($pa->detalles->count() > 4)
                                        <li class="text-orange-400 text-[10px] font-bold italic list-none mt-1">+ {{ $pa->detalles->count() - 4 }} artículo(s) más...</li>
                                    @endif
                                </ul>
                            </div>
                            <span class="inline-block mt-auto w-full text-center bg-orange-600 hover:bg-orange-500 text-white font-medium text-xs px-3 py-2.5 rounded transition-colors">
                                Procesar Devolución →
                            </span>
                        </a>
                    @empty
                        <div class="col-span-1 sm:col-span-2 md:col-span-3 text-center py-8">
                            <svg class="w-12 h-12 text-gray-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-gray-400 text-sm">No hay préstamos activos pendientes por devolver este turno.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @else
            <div class="mb-5 bg-[#023047] p-3 rounded-lg border border-gray-700 shadow flex items-center justify-between">
                <a href="{{ route('devoluciones.index') }}" class="inline-flex items-center text-sm font-medium text-gray-300 hover:text-white transition-colors bg-gray-700 px-3 py-1.5 rounded">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Volver a la lista de préstamos
                </a>
                <span class="text-orange-400 font-bold text-sm">Préstamo Activo #{{ $prestamo->id_prestamo }}</span>
            </div>
        @endif

        @isset($prestamo)
            <form action="{{ route('devoluciones.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_prestamo" value="{{ $prestamo->id_prestamo }}">

                {{-- Información del empleado --}}
                <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg p-5 mb-6 flex items-center gap-4">
                    @if ($prestamo->empleado->imagen)
                        <img src="{{ $prestamo->empleado->imagen }}" class="w-12 h-12 rounded-full object-cover border border-gray-600">
                    @else
                        <div class="w-12 h-12 rounded-full bg-gray-700 border border-gray-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    @endif
                    <div>
                        <p class="text-white font-semibold text-sm">
                            {{ $prestamo->empleado->nombre }} {{ $prestamo->empleado->apellido_p }}
                        </p>
                        <p class="text-gray-400 text-xs mt-0.5">{{ $prestamo->empleado->puesto }}</p>
                    </div>
                </div>

                {{-- Ensamble --}}
                <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg p-5 mb-6">
                    <div class="flex items-baseline justify-between mb-4">
                        <h2 class="text-lg font-bold text-white">Paso 2 — Registro de Ensamble <span class="text-xs font-normal text-orange-400 bg-orange-400/10 px-2 py-1 rounded ml-2">OPCIONAL</span></h2>
                        <span class="text-xs text-gray-400 italic">Llena esto solo si las herramientas se usaron para manufacturar algo nuevo.</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-300">Nombre de lo que se ensambló</label>
                            <input type="text" name="ensamblado_nombre" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5" placeholder="Ej. Tablilla Electrónica X1 (Déjalo vacío si no aplica)">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-300">Cantidad ensamblada</label>
                            <input type="number" name="ensamblado_cantidad" min="1" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5" placeholder="Solo números">
                        </div>
                    </div>
                </div>

                @php
                    $materiales = $prestamo->detalles->whereNotNull('id_material');
                    $herramientas = $prestamo->detalles->whereNotNull('id_herramienta');
                @endphp



                {{-- Herramientas Devolver --}}
                @if($herramientas->count() > 0)
                <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg overflow-hidden mb-6">
                    <div class="px-5 py-4 bg-[#01263a] border-b border-gray-700">
                        <h2 class="text-lg font-bold text-white">Paso 4 — Devolución de Herramientas</h2>
                    </div>
                    <div class="grid grid-cols-12 gap-2 px-5 py-3 bg-[#01263a] text-gray-400 text-xs font-semibold uppercase tracking-wider border-b border-gray-700">
                        <div class="col-span-4">Herramienta</div>
                        <div class="col-span-2 text-center">Prestada</div>
                        <div class="col-span-3 text-center">A devolver</div>
                        <div class="col-span-3 text-center">Estatus Entrega</div>
                    </div>
                    <div class="divide-y divide-gray-700">
                        @foreach ($herramientas as $detalle)
                            <div class="grid grid-cols-12 gap-2 px-5 py-4 items-center">
                                <input type="hidden" name="devoluciones[{{ $loop->index }}][id_herramienta]" value="{{ $detalle->id_herramienta }}">
                                <div class="col-span-4">
                                    <p class="text-white text-sm font-medium">{{ $detalle->herramienta->nombre_herramienta }}</p>
                                </div>
                                <div class="col-span-2 text-center text-gray-300 text-sm">{{ $detalle->cantidad }}</div>
                                <div class="col-span-3 pl-2">
                                    <input type="number" name="devoluciones[{{ $loop->index }}][cantidad]" min="1" max="{{ $detalle->cantidad }}" value="{{ $detalle->cantidad }}" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg text-center w-full p-2" required>
                                </div>
                                <div class="col-span-3">
                                    <select name="devoluciones[{{ $loop->index }}][estatus_herramienta]" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg w-full p-2">
                                        <option value="Nuevo">Nuevo</option>
                                        <option value="Buen Estado" selected>Buen Estado</option>
                                        <option value="Dañado">Dañado</option>
                                        <option value="Reparacion">Reparación</option>
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg p-5">
                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium py-3 rounded-lg transition-colors">
                        Confirmar Devolución / Registro
                    </button>
                </div>

            </form>
        @endisset

    </div>

@endsection
