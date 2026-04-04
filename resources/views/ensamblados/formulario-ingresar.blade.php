@extends('plantilla.app')

@section('titulo', 'Ingresar Cantidad a Ensamblado')

@section('contenido')

    <section class="bg-white dark:bg-white min-h-screen">
        <div class="py-10 px-4 mx-auto max-w-2xl lg:py-16 mt-10 mb-20">

            <div class="bg-[#023047] rounded-xl shadow-lg p-8">
                <h2 class="mb-2 text-2xl font-bold text-white text-center">Registrar ensamble</h2>
                <p class="text-center text-blue-200 text-sm mb-8">Selecciona el ensamblado y la cantidad a ingresar</p>

                <form action="{{ route('ensamblados.ingresar.store') }}" method="POST">
                    @csrf

                    <div class="mb-5">
                        <label for="id_empleado" class="block mb-2 text-sm font-medium text-white">
                            Supervisor
                        </label>
                        <select name="id_empleado" id="id_empleado"
                            class="bg-gray-700 border border-gray-600 text-white placeholder-gray-400 text-sm rounded-lg
                                   focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5
                                   @error('id_empleado') border-red-500 ring-1 ring-red-500 @enderror">
                            <option value="" disabled selected>Selecciona un supervisor</option>
                            @foreach ($supervisores as $supervisor)
                                {{-- <option value="{{ $supervisor->id }}"
                                    {{ old('id_empleado') == $supervisor->id ? 'selected' : '' }}>
                                    {{ $supervisor->nombre }}
                                </option> --}}
                                <option value="{{ $supervisor['id'] }}"
                                    {{ old('id_empleado') == $supervisor['id'] ? 'selected' : '' }}>
                                    {{ $supervisor['nombre'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_empleado')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ensamblado --}}
                    <div class="mb-5">
                        <label for="id_ensamblado" class="block mb-2 text-sm font-medium text-white">
                            Ensamblado
                        </label>
                        <select name="id_ensamblado" id="id_ensamblado" onchange="actualizarCantidadActual(this)"
                            class="bg-gray-700 border border-gray-600 text-white placeholder-gray-400 text-sm rounded-lg
                                   focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5
                                   @error('id_ensamblado') border-red-500 ring-1 ring-red-500 @enderror">
                            <option value="" disabled selected>Selecciona un ensamblado</option>
                            @foreach ($ensamblados as $ens)
                                {{-- <option value="{{ $ens->id_ensamblado }}" data-cantidad="{{ $ens->cantidad }}"
                                    {{ old('id_ensamblado') == $ens->id_ensamblado ? 'selected' : '' }}>
                                    {{ $ens->nombre }}
                                </option> --}}
                                <option value="{{ $ens['id_ensamblado'] }}" data-cantidad="{{ $ens['cantidad_sobrante'] }}"
                                    {{ old('id_ensamblado') == $ens['id_ensamblado'] ? 'selected' : '' }}>
                                    {{ $ens['material']['nombre_material'] ?? 'Ensamblado #' . $ens['id_ensamblado'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_ensamblado')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Cantidad actual (informativo) --}}
                    <div id="info-cantidad" class="hidden mb-5 flex items-center gap-2 bg-blue-950/40 rounded-lg px-4 py-3">
                        <span class="text-blue-300 text-sm">Cantidad actual:</span>
                        <span id="cantidad-actual" class="text-green-400 font-bold text-sm">—</span>
                    </div>

                    {{-- Supervisor --}}


                    {{-- Cantidad a sumar --}}
                    <div class="mb-6">
                        <label for="cantidad" class="block mb-2 text-sm font-medium text-white">
                            Cantidad a ingresar
                        </label>
                        <input type="number" name="cantidad_sobrante" id="cantidad_sobrante"
                            value="{{ old('cantidad_sobrante') }}" placeholder="Ej: 10" min="1"
                            oninput="actualizarPreview(this)"
                            class="bg-gray-700 border border-gray-600 text-white placeholder-gray-400 text-sm rounded-lg
                                   focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5
                                   @error('cantidad_sobrante') border-red-500 ring-1 ring-red-500 @enderror">
                        @error('cantidad_sobrante')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror

                        {{-- Preview resultado --}}
                        <div id="preview-resultado"
                            class="hidden mt-2 flex items-center gap-2 bg-blue-950/40 rounded-lg px-4 py-3">
                            <span class="text-blue-300 text-sm">Cantidad después:</span>
                            <span id="preview-valor" class="text-orange-400 font-bold text-sm">—</span>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full text-white bg-[#fb5607] hover:bg-orange-600 focus:ring-4
                               focus:outline-none focus:ring-orange-300 font-medium rounded-lg
                               text-sm px-5 py-2.5 text-center transition-colors">
                        Ingresar cantidad
                    </button>
                </form>
            </div>

        </div>
    </section>

    <script>
        function actualizarCantidadActual(select) {
            const opt = select.options[select.selectedIndex];
            const cantidad = opt ? opt.getAttribute('data-cantidad') : null;

            const info = document.getElementById('info-cantidad');
            const span = document.getElementById('cantidad-actual');

            if (cantidad !== null) {
                span.textContent = cantidad;
                info.classList.remove('hidden');
            } else {
                info.classList.add('hidden');
            }

            // Recalcular preview si ya hay cantidad ingresada
            const input = document.getElementById('cantidad_sobrante');
            if (input.value) actualizarPreview(input);
        }

        function actualizarPreview(input) {
            const select = document.getElementById('id_ensamblado');
            const opt = select.options[select.selectedIndex];
            const cantidadActual = opt ? parseInt(opt.getAttribute('data-cantidad')) : null;
            const cantidadNueva = parseInt(input.value) || 0;

            const preview = document.getElementById('preview-resultado');
            const valor = document.getElementById('preview-valor');

            if (cantidadActual !== null && cantidadNueva > 0) {
                valor.textContent = cantidadActual + cantidadNueva;
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>

@endsection
