@extends('plantilla.app')

@section('titulo', 'Historial de Ensambles')

@section('contenido')

    <section class="min-h-screen bg-gray-50 dark:bg-white pt-10 pb-20">
        <div class="px-4 mx-auto max-w-screen-xl">

            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-gray-900 border-l-4 border-orange-500 pl-4">Historial de Ensambles</h1>
                <!-- Optional Date Filter placeholder -->
                <p class="text-sm text-gray-500 font-medium">Lista de todos los productos y devoluciones procesadas.</p>
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-300 rounded-lg text-green-800 font-medium shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold">Ensamblado (Producto)</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Técnico (Empleado)</th>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">Fecha de Registro</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($ensamblados as $ens)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shadow-inner">
                                                #{{ $ens->id_ensamblado }}
                                            </div>
                                            <div>
                                                @if($ens->nombre_ensamblado)
                                                    <p class="font-bold text-gray-900 text-base">{{ $ens->nombre_ensamblado }}</p>
                                                    <p class="text-sm text-orange-600 font-semibold mt-0.5">Producción: {{ $ens->cantidad_ensamblada }} u.</p>
                                                @else
                                                    <p class="font-bold text-gray-900 text-base italic">Sin ensamble registrado</p>
                                                    <p class="text-xs text-gray-400 mt-0.5">Solo devolución de material</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($ens->empleado)
                                            <p class="font-semibold text-gray-900">{{ $ens->empleado->nombre }} {{ $ens->empleado->apellido_p }}</p>
                                            <p class="text-xs text-gray-500">{{ $ens->empleado->puesto }}</p>
                                        @else
                                            <span class="text-gray-400 block italic">ID: {{ $ens->id_empleado }}</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <p class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($ens->fecha_registro)->format('d/m/Y') }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($ens->fecha_registro)->format('h:i A') }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-semibold text-gray-900">No hay ensambles registrados</h3>
                                        <p class="mt-1 text-sm text-gray-500">Los ensambles aparecerán aquí automáticamente después de que un técnico devuelva materiales/herramientas al final de su turno.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($ensamblados->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $ensamblados->links() }}
                </div>
            @endif

        </div>
    </section>

@endsection
