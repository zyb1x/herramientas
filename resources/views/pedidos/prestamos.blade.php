@extends('plantilla.app')

@section('titulo', 'Mis Préstamos')

@section('contenido')
    <section class="bg-white py-8 antialiased md:py-16">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <div class="mx-auto max-w-5xl">

                {{-- Encabezado --}}
                <div class="gap-4 sm:flex sm:items-center sm:justify-between">
                    <h2 class="text-xl font-bold text-orange-500 sm:text-2xl">Mis Préstamos</h2>

                    <div class="mt-6 sm:mt-0">
                        <label for="filtro-estatus" class="sr-only">Filtrar por estatus</label>
                        <select id="filtro-estatus"
                            class="block w-full min-w-[10rem] rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900
                                   focus:border-primary-500 focus:ring-primary-500
                                   dark:border-gray-600 dark:bg-[#023047] dark:text-white dark:placeholder:text-gray-400
                                   dark:focus:border-primary-500 dark:focus:ring-primary-500">
                            <option value="">Todos los estatus</option>
                            <option value="Activo">Activo</option>
                            <option value="Devuelto Parcial">Devuelto Parcial</option>
                            <option value="Cerrado">Cerrado</option>
                        </select>
                    </div>
                </div>
                {{-- Listado --}}
                <div class="mt-6 flow-root sm:mt-8">
                    @if ($prestamos->isEmpty())
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <svg class="mb-4 h-12 w-12 text-gray-300 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            <p class="text-base font-medium text-orange-500">No tienes préstamos
                                registrados.</p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-200 dark:divide-gray-700" id="lista-prestamos">
                            @foreach ($prestamos as $prestamo)
                                <div class="prestamo-item grid md:grid-cols-12 gap-4 md:gap-6 py-4 md:py-6"
                                    data-estatus="{{ $prestamo->estatus_general }}">

                                    {{-- Columna: ID y Fecha --}}
                                    <dl class="md:col-span-3 order-2 md:order-1">
                                        <dt class="text-medium font-medium uppercase text-orange-400">#ID
                                        </dt>
                                        <dd class="mt-1 text-base font-semibold text-[#023047]">
                                            #{{ str_pad($prestamo->id_prestamo, 5, '0', STR_PAD_LEFT) }}
                                        </dd>
                                        <dt class="mt-2 text-medium font-medium uppercase text-orange-400">
                                            Fecha</dt>
                                        <dd class="mt-1 text-sm text-[#023047] font-bold">
                                            {{ \Carbon\Carbon::parse($prestamo->fecha_prestamo)->format('d/m/Y H:i') }}
                                        </dd>
                                    </dl>

                                    {{-- Columna: Empleado --}}
                                    <dl class="md:col-span-6 order-3 md:order-2 content-center">
                                        <dt class="text-mendium font-medium uppercase text-orange-400">Empleado
                                        </dt>
                                        <dd class="mt-1 text-base font-semibold text-[#023047]">
                                            {{ $prestamo->empleado->nombre ?? 'Sin nombre' }}
                                            {{ $prestamo->empleado->apellido_paterno ?? '' }}
                                        </dd>
                                        <dt class="mt-2 text-medium font-medium uppercase text-orange-400">
                                            Registrado por</dt>
                                        <dd class="mt-1 text-sm text-[#023047] font-bold">
                                            {{ $prestamo->usuario->nombre ?? 'Usuario desconocido' }}
                                        </dd>
                                    </dl>

                                    {{-- Columna: Estatus + Acción --}}
                                    <div
                                        class="md:col-span-3 order-1 md:order-3 flex items-center justify-between md:flex-col md:items-end md:justify-center gap-2">

                                        {{-- Badge de estatus --}}
                                        @php
                                            $badgeClasses = match ($prestamo->estatus_general) {
                                                'Activo'
                                                    => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                'Devuelto Parcial'
                                                    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                'Cerrado'
                                                    => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                                default => 'bg-blue-100 text-blue-800',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeClasses }}">
                                            {{ $prestamo->estatus_general }}
                                        </span>

                                        {{-- Botón Ver detalle --}}
                                        <a href="{{ route('prestamos.show', $prestamo->id_prestamo) }}"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-900
                                                  bg-orange-500 hover:bg-[#023047] text-white hover:text-orange-500 duration-150">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            Ver detalle
                                        </a>
                                    </div>

                                </div>
                                <br>
                                <br>
                            @endforeach
                        </div>

                        {{-- Paginación --}}
                        @if ($prestamos->hasPages())
                            <div class="mt-6">
                                {{ $prestamos->links() }}
                            </div>
                        @endif
                    @endif
                </div>

            </div>
        </div>
    </section>

    {{-- Script de filtrado por estatus --}}
    <script>
        document.getElementById('filtro-estatus').addEventListener('change', function() {
            const valor = this.value.toLowerCase();
            document.querySelectorAll('.prestamo-item').forEach(function(item) {
                const estatus = item.dataset.estatus.toLowerCase();
                item.style.display = (valor === '' || estatus === valor) ? '' : 'none';
            });
        });
    </script>
@endsection
