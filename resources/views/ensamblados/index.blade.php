@extends('plantilla.app')

@section('titulo', 'Ensamblados')

@section('contenido')

    <section class="min-h-screen bg-white dark:bg-white">
        <div class="py-10 px-4 mx-auto max-w-2xl lg:py-16 mt-10 mb-20">

            <div class="bg-[#023047] rounded-xl shadow-lg p-8">
                <h2 class="mb-2 text-2xl font-bold text-white text-center">Registro de Ensamblado</h2>
                <p class="text-center text-blue-200 text-sm mb-8">¿Qué tipo de operación deseas realizar?</p>

                @if (session('success'))
                    <div
                        class="mb-6 bg-green-800/40 border border-green-400 rounded-lg p-3 text-green-300 text-sm text-center">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Ensamble Existente --}}
                    <a href="{{ route('ensamblados.create', ['modo' => 'existente']) }}"
                        class="group flex flex-col items-center justify-center gap-3 bg-[#023047] border-2 border-blue-400 hover:bg-blue-900 hover:border-white text-white rounded-xl px-6 py-8 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-10 w-10 text-blue-300 group-hover:text-white transition-colors" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                        </svg>
                        <span class="text-lg font-semibold">Ensamble Existente</span>
                        <span class="text-xs text-blue-300 group-hover:text-blue-100">Busca un ensamble por ID y agrega
                            cantidad sobrante</span>
                    </a>

                    {{-- Ensamble Nuevo --}}
                    <a href="{{ route('ensamblados.create', ['modo' => 'nuevo']) }}"
                        class="group flex flex-col items-center justify-center gap-3 bg-[#023047] border-2 border-orange-400 hover:bg-orange-900 hover:border-white text-white rounded-xl px-6 py-8 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-400 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-10 w-10 text-orange-300 group-hover:text-white transition-colors" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="text-lg font-semibold">Ensamble Nuevo</span>
                        <span class="text-xs text-orange-300 group-hover:text-orange-100">Registra un nuevo ensamblado con
                            material sobrante</span>
                    </a>

                </div>

                {{-- Botón ver listado --}}
                {{-- <div class="mt-6 text-center">
                    <a href="{{ route('ensamblados.listado') }}"
                        class="inline-flex items-center gap-2 text-blue-300 hover:text-white text-sm underline underline-offset-2 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        Ver todos los ensamblados registrados
                    </a>
                </div> --}}
            </div>

        </div>
    </section>

@endsection
