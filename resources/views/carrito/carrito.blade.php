@extends('plantilla.app')

@section('titulo', 'carrito')


@section('contenido')

    <div class="max-w-2xl mx-auto px-4 py-6">

        <!-- Título -->
        <h1 class="text-2xl font-bold text-white mb-6">Shopping Cart</h1>

        <!-- Lista de productos -->
        <div class="divide-y divide-gray-700">

            @foreach ($carrito as $item)
                <div class="flex items-center gap-4 py-4">

                    <!-- Imagen -->
                    <div class="w-14 h-14 flex-shrink-0 flex items-center justify-center">
                        <img src="{{ $item->herramienta->imagen }}" alt="{{ $item->herramienta->nombre_herramienta }}"
                            class="w-12 h-12 object-contain">
                    </div>

                    <!-- Nombre -->
                    <p class="text-white text-sm font-medium flex-1">
                        {{ $item->herramienta->nombre_herramienta }}
                    </p>

                    <!-- Cantidad -->
                    <div class="flex items-center gap-2">
                        <button
                            class="w-7 h-7 rounded-md bg-gray-700 hover:bg-gray-600 text-white text-sm flex items-center justify-center transition-colors">
                            −
                        </button>
                        <span class="text-white text-sm w-4 text-center">{{ $item->cantidad }}</span>
                        <button
                            class="w-7 h-7 rounded-md bg-gray-700 hover:bg-gray-600 text-white text-sm flex items-center justify-center transition-colors">
                            +
                        </button>
                    </div>

                    <!-- Precio -->
                    <p class="text-white font-bold text-sm w-20 text-right">
                        ${{ number_format($item->precio_total, 2) }}
                    </p>

                    <!-- Eliminar -->
                    <button class="text-gray-500 hover:text-red-400 transition-colors ml-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>

                </div>
            @endforeach

        </div>

        <!-- Order Summary -->
        <div class="mt-6 pt-6 border-t border-gray-700">

            <h2 class="text-xl font-bold text-white mb-4">Order summary</h2>

            <div class="space-y-2">
                <div class="flex justify-between text-sm text-gray-400">
                    <span>Original price</span>
                    <span class="text-white">${{ number_format($resumen->precio_original, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-400">
                    <span>Savings</span>
                    <span class="text-green-400">-${{ number_format($resumen->ahorro, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-400">
                    <span>Store Pickup</span>
                    <span class="text-white">${{ number_format($resumen->envio, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-400">
                    <span>Tax</span>
                    <span class="text-white">${{ number_format($resumen->impuesto, 2) }}</span>
                </div>
            </div>

            <div class="flex justify-between text-white font-bold text-base mt-4 pt-4 border-t border-gray-700">
                <span>Total</span>
                <span>${{ number_format($resumen->total, 2) }}</span>
            </div>

        </div>

        <!-- Botones -->
        <div class="flex gap-4 mt-6">
            <button
                class="flex-1 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 text-sm font-medium py-3 rounded-lg transition-colors">
                Continue Shopping
            </button>
            <button
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-3 rounded-lg transition-colors">
                Proceed to Checkout
            </button>
        </div>

    </div>

@endsection
