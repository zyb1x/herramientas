@extends('plantilla.app')

@section('titulo', 'herramientas')


@section('contenido')
        <!-- GRID PRODUCTOS -->
        <div class="max-w-6xl mx-auto px-6 mt-5">
            <div class="bg-[#023047] rounded-2xl p-5 border border-gray-700 shadow-lg">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    @foreach ($herramientas as $herramienta)
                        <div
                            class="bg-[#023047] rounded-xl border border-gray-500 overflow-hidden hover:border-orange-500 transition-colors flex flex-col sjadow-lg">

                            <!-- Imagen -->
                            <div class="bg-white flex items-center justify-center h-56 p-4">
                                <img class="h-44 object-contain" src="{{ $herramienta->imagen }}"
                                    alt="{{ $herramienta->nombre_herramienta }}">
                            </div>

                            <!-- Contenido -->
                            <div class="p-4 flex flex-col flex-1">

                                <p class="text-orange-400 text-xs font-bold tracking-widest mb-1">
                                    {{ $herramienta->categoria->nombre_categoria }}
                                </p>

                                <h3 class="text-white text-sm font-semibold leading-snug mb-2">
                                    {{ $herramienta->nombre_herramienta }}
                                </h3>

                                <div class="flex items-center gap-1 mb-3">
                                    <span class="text-gray-400 text-xs">Existencia:</span>
                                    <span class="text-white text-xs font-semibold">{{ $herramienta->existencia }}</span>
                                </div>

                                {{-- <div class="flex gap-2 mb-2 mt-auto">
                                    <button
                                        class="flex-1 text-xs border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 py-1.5 rounded-lg transition-colors">
                                        Wishlist
                                    </button>
                                    <button
                                        class="flex-1 text-xs border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 py-1.5 rounded-lg transition-colors">
                                        Compare
                                    </button>
                                </div> --}}

                                <button
                                    class="w-full bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold py-2 rounded-lg transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Solicitar
                                </button>

                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

        <!-- PAGINACIÓN -->
        <div class="mt-10">

            {{-- {{ $herramientas->links() }} --}}

        </div>

    </div>
@endsection
