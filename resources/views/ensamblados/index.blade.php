@extends('plantilla.app')

@section('titulo', 'Entrega de Artículos Ensamblados')

@section('contenido')

    <div class="max-w-4xl mx-auto px-4 py-6">

        <h1 class="text-2xl font-bold text-white mb-6">Entrega de Artículos Ensamblados</h1>

        {{-- Alertas --}}
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-600 text-white text-sm rounded-lg">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-600 text-white text-sm rounded-lg">{{ session('error') }}</div>
        @endif

        <form action="{{ route('ensamblados.store') }}" method="POST" id="form-ensamblados">
            @csrf

            <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg overflow-hidden mb-6">

                <div class="px-5 py-4 bg-[#01263a] border-b border-gray-700 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-white">Artículos que sobraron del ensamblado</h2>
                        <p class="text-gray-400 text-xs mt-1">Ingresa el código de herramienta y la cantidad sobrante.</p>
                    </div>
                    <button type="button" id="btn-agregar-fila"
                        class="bg-gray-700 hover:bg-gray-600 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Agregar fila
                    </button>
                </div>

                {{-- Encabezado tabla --}}
                <div
                    class="grid grid-cols-12 gap-2 px-5 py-3 bg-[#01263a] text-gray-400 text-xs font-semibold uppercase tracking-wider border-b border-gray-700">
                    <div class="col-span-2">Código artículo</div>
                    <div class="col-span-4">Herramienta</div>
                    <div class="col-span-2 text-center">Cantidad sobrante</div>
                    <div class="col-span-2 text-center">Exist. antes</div>
                    <div class="col-span-2 text-center">Exist. después</div>
                </div>

                {{-- Filas dinámicas --}}
                <div id="filas-ensamblado" class="divide-y divide-gray-700">

                    {{-- Fila template (se clona con JS) --}}
                    <div class="fila-ensamblado grid grid-cols-12 gap-2 px-5 py-4 items-center">

                        {{-- Código --}}
                        <div class="col-span-2">
                            <input type="number" name="articulos[0][id_herramienta]" placeholder="Ej. 5" min="1"
                                class="input-codigo bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 w-full p-2.5 placeholder-gray-400"
                                required>
                        </div>

                        {{-- Nombre (se llena automático) --}}
                        <div class="col-span-4">
                            <p class="nombre-herramienta text-gray-400 text-sm italic">—</p>
                        </div>

                        {{-- Cantidad sobrante --}}
                        <div class="col-span-2">
                            <input type="number" name="articulos[0][cantidad]" placeholder="Ej. 2" min="1"
                                class="input-cantidad bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 w-full p-2.5 placeholder-gray-400 text-center"
                                required>
                        </div>

                        {{-- Exist. antes --}}
                        <div class="col-span-2 text-center">
                            <span class="existencia-antes text-gray-400 text-sm">—</span>
                        </div>

                        {{-- Exist. después --}}
                        <div class="col-span-2 text-center flex items-center justify-center gap-2">
                            <span class="existencia-despues text-green-400 text-sm font-semibold">—</span>
                            <button type="button"
                                class="btn-eliminar-fila text-gray-600 hover:text-red-400 transition-colors hidden">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>

                    </div>

                </div>
            </div>

            {{-- ID Empleado y confirmar --}}
            <div class="bg-[#023047] rounded-2xl border border-gray-700 shadow-lg p-5">
                <div class="mb-4">
                    <label for="id_empleado" class="block mb-2 text-sm font-medium text-gray-300">
                        ID del Empleado <span class="text-gray-500 font-normal">(quien entrega)</span>
                    </label>
                    <input type="number" name="id_empleado" id="id_empleado"
                        class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 placeholder-gray-400"
                        placeholder="Ej. 201" min="1" required>
                    @error('id_empleado')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium py-3 rounded-lg transition-colors">
                    Registrar entrega
                </button>
            </div>

        </form>
    </div>

    <script>
        let filaIndex = 1;

        // Buscar nombre y existencia de herramienta por código (debounce)
        function attachCodigoListener(fila) {
            const inputCodigo = fila.querySelector('.input-codigo');
            const inputCantidad = fila.querySelector('.input-cantidad');
            const nombreEl = fila.querySelector('.nombre-herramienta');
            const existAntesEl = fila.querySelector('.existencia-antes');
            const existDespuesEl = fila.querySelector('.existencia-despues');

            let timer;

            function actualizarDespues() {
                const existencia = parseInt(existAntesEl.dataset.valor || 0);
                const cantidad = parseInt(inputCantidad.value) || 0;
                if (existencia > 0 && cantidad > 0) {
                    existDespuesEl.textContent = existencia + cantidad;
                } else {
                    existDespuesEl.textContent = '—';
                }
            }

            inputCantidad.addEventListener('input', actualizarDespues);

            inputCodigo.addEventListener('input', function() {
                clearTimeout(timer);
                const id = this.value.trim();
                if (!id) {
                    nombreEl.textContent = '—';
                    existAntesEl.textContent = '—';
                    existDespuesEl.textContent = '—';
                    existAntesEl.dataset.valor = 0;
                    return;
                }
                timer = setTimeout(async () => {
                    try {
                        const res = await fetch(`/herramientas/buscar?id=${id}`);
                        const data = await res.json();
                        if (data.error) {
                            nombreEl.textContent = 'No encontrada';
                            existAntesEl.textContent = '—';
                            existDespuesEl.textContent = '—';
                            existAntesEl.dataset.valor = 0;
                        } else {
                            nombreEl.textContent = data.nombre_herramienta;
                            existAntesEl.textContent = data.existencia;
                            existAntesEl.dataset.valor = data.existencia;
                            actualizarDespues();
                        }
                    } catch {
                        nombreEl.textContent = 'Error al buscar';
                    }
                }, 400);
            });
        }

        // Agregar fila
        document.getElementById('btn-agregar-fila').addEventListener('click', function() {
            const contenedor = document.getElementById('filas-ensamblado');
            const template = contenedor.querySelector('.fila-ensamblado');
            const nueva = template.cloneNode(true);

            // Actualizar names con nuevo índice
            nueva.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/\[\d+\]/, `[${filaIndex}]`);
                el.value = '';
            });

            // Limpiar textos
            nueva.querySelector('.nombre-herramienta').textContent = '—';
            nueva.querySelector('.existencia-antes').textContent = '—';
            nueva.querySelector('.existencia-antes').dataset.valor = 0;
            nueva.querySelector('.existencia-despues').textContent = '—';

            // Mostrar botón eliminar
            nueva.querySelector('.btn-eliminar-fila').classList.remove('hidden');

            contenedor.appendChild(nueva);
            attachCodigoListener(nueva);
            attachEliminarListener(nueva);
            filaIndex++;
        });

        // Eliminar fila
        function attachEliminarListener(fila) {
            fila.querySelector('.btn-eliminar-fila').addEventListener('click', function() {
                fila.remove();
            });
        }

        // Init primera fila
        attachCodigoListener(document.querySelector('.fila-ensamblado'));
    </script>

@endsection
