@extends('plantilla.app')

@section('titulo', 'Carrito')

@section('contenido')

    <div class="max-w-4xl mx-auto px-4 py-6">

        <h1 class="text-2xl font-bold text-white mb-6">Carrito de solicitud</h1>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-600 text-white text-sm rounded-lg">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-600 text-white text-sm rounded-lg">{{ session('error') }}</div>
        @endif

        @if ($items->isEmpty())
            <div class="text-center py-16 text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <p class="text-lg font-medium">El carrito está vacío</p>
                <p class="text-sm mt-1">Agrega herramientas o materiales para continuar.</p>
                <div class="flex justify-center gap-4 mt-6">
                    <a href="{{ route('herramientas.index') }}"
                        class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm rounded-lg transition-colors">
                        Ver herramientas
                    </a>
                    <a href="{{ route('materiales.index') }}"
                        class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm rounded-lg transition-colors">
                        Ver materiales
                    </a>
                </div>
            </div>
        @else
            <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg overflow-hidden">

                <div
                    class="grid grid-cols-12 gap-2 px-5 py-3 bg-[#01263a] text-gray-400 text-xs font-semibold uppercase tracking-wider">
                    <div class="col-span-1">Tipo</div>
                    <div class="col-span-1">Código</div>
                    <div class="col-span-3">Artículo</div>
                    <div class="col-span-2 text-center">Cantidad salida</div>
                    <div class="col-span-2 text-center">Exist. antes</div>
                    <div class="col-span-2 text-center">Exist. después</div>
                    <div class="col-span-1 text-center">Quitar</div>
                </div>

                <div class="divide-y divide-gray-700">
                    @foreach ($items as $item)
                        <div class="grid grid-cols-12 gap-2 px-5 py-4 items-center" data-row-id="{{ $item->rowId }}">

                            <div class="col-span-1">
                                @if ($item->options->tipo === 'herramienta')
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-900 text-blue-300">
                                        Herr.
                                    </span>
                                @else
                                    <span
                                        class="text-xs font-semibold px-2 py-0.5 rounded-full bg-purple-900 text-purple-300">
                                        Mat.
                                    </span>
                                @endif
                            </div>

                            <div class="col-span-1 text-gray-300 text-xs font-mono">
                                #{{ $item->id }}
                            </div>

                            <div class="col-span-3">
                                <p class="text-white text-sm font-medium leading-snug">{{ $item->name }}</p>
                                @if ($item->options->tipo === 'herramienta')
                                    <span
                                        class="text-xs mt-0.5 inline-block
                                        @if ($item->options->estado === 'Nuevo') text-green-400
                                        @elseif($item->options->estado === 'Buen Estado') text-blue-400
                                        @elseif($item->options->estado === 'Dañado') text-red-400
                                        @else text-yellow-400 @endif">
                                        {{ $item->options->estado }}
                                    </span>
                                @else
                                    <span
                                        class="text-xs mt-0.5 inline-block
                                        @if ($item->options->estatus === 'Disponible') text-green-400
                                        @else text-red-400 @endif">
                                        {{ $item->options->estatus }}
                                    </span>
                                @endif
                            </div>

                            <div class="col-span-2 flex justify-center">
                                <input type="number" value="{{ $item->qty }}" min="1"
                                    max="{{ $item->options->existencia }}" data-row-id="{{ $item->rowId }}"
                                    data-existencia="{{ $item->options->existencia }}"
                                    class="qty-input w-20 text-center bg-gray-700 border border-gray-600 text-white text-sm font-semibold rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors p-1.5">
                            </div>

                            <div class="col-span-2 text-center text-gray-300 text-sm">
                                {{ $item->options->existencia }}
                            </div>

                            <div id="despues-{{ $item->rowId }}"
                                class="col-span-2 text-center text-sm font-semibold
                                @if ($item->options->existencia - $item->qty <= 0) text-red-400
                                @elseif($item->options->existencia - $item->qty <= 3) text-yellow-400
                                @else text-green-400 @endif">
                                {{ $item->options->existencia - $item->qty }}
                            </div>

                            <div class="col-span-1 flex justify-center">
                                <form action="{{ route('carrito.eliminar', $item->rowId) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-500 hover:text-red-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>

                        </div>
                    @endforeach
                </div>

                <div class="px-5 py-3 bg-[#01263a] border-t border-gray-700 flex justify-between text-sm text-gray-400">
                    <span>Total de artículos: <span class="text-white font-semibold">{{ $items->count() }}</span></span>
                    <form action="{{ route('carrito.vaciar') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300 text-xs transition-colors">
                            Vaciar carrito
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-6 bg-[#023047] rounded-2xl border border-gray-700 shadow-lg p-5">

                <h2 class="text-lg font-bold text-white mb-4">Datos del préstamo</h2>

                <form action="{{ route('carrito.confirmar') }}" method="POST">
                    @csrf

                    @foreach ($items as $item)
                        <input type="hidden" id="hidden-qty-{{ $item->rowId }}" name="cantidades[{{ $item->rowId }}]"
                            value="{{ $item->qty }}">
                    @endforeach

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">

                        <div>
                            <label for="id_empleado" class="block mb-2 text-sm font-medium text-gray-300">
                                ID del Empleado
                                <span class="text-gray-500 font-normal">(quien recibe los artículos)</span>
                            </label>
                            <input type="number" name="id_empleado" id="id_empleado" value="{{ old('id_empleado') }}"
                                class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 placeholder-gray-400"
                                placeholder="Ej. 42" min="1" required>
                            @error('id_empleado')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-300">
                                Registrado por
                            </label>
                            <input type="text" value="{{ session('usuario')['nombre'] }}" readonly
                                class="bg-gray-800 border border-gray-600 text-gray-400 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed">
                        </div>

                    </div>

                    <div class="flex gap-4">
                        <a href="{{ route('herramientas.index') }}"
                            class="flex-1 text-center border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 text-sm font-medium py-3 rounded-lg transition-colors">
                            Seguir agregando
                        </a>
                        <button type="submit"
                            class="flex-1 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium py-3 rounded-lg transition-colors">
                            Confirmar préstamo
                        </button>
                    </div>

                </form>
            </div>

        @endif

    </div>

    @if (!$items->isEmpty())
        <script>
            function colorExistDespues(el, valor) {
                el.classList.remove('text-red-400', 'text-yellow-400', 'text-green-400');
                if (valor <= 0) el.classList.add('text-red-400');
                else if (valor <= 3) el.classList.add('text-yellow-400');
                else el.classList.add('text-green-400');
            }

            document.querySelectorAll('.qty-input').forEach(input => {
                input.addEventListener('input', () => {
                    const existencia = parseInt(input.dataset.existencia);
                    const cantidad = parseInt(input.value);
                    const rowId = input.dataset.rowId;
                    const despuesEl = document.getElementById(`despues-${rowId}`);
                    const hiddenInput = document.getElementById(`hidden-qty-${rowId}`);

                    if (isNaN(cantidad) || cantidad < 1 || cantidad > existencia) {
                        input.classList.add('border-red-500');
                        input.classList.remove('border-gray-600');
                        return;
                    }

                    input.classList.remove('border-red-500');
                    input.classList.add('border-gray-600');

                    despuesEl.textContent = existencia - cantidad;
                    colorExistDespues(despuesEl, existencia - cantidad);

                    hiddenInput.value = cantidad;
                });
            });
        </script>
    @endif

@endsection
