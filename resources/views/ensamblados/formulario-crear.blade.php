@extends('plantilla.app')

@section('titulo', 'Registro de Ensamblado')

@section('contenido')

    <section class="bg-white dark:bg-white min-h-screen">
        <div class="py-10 px-4 mx-auto max-w-2xl lg:py-16 mt-10 mb-20">

            
            <div id="paso-tipo" class="bg-[#023047] rounded-xl shadow-lg p-8">
                <h2 class="mb-2 text-2xl font-bold text-white text-center">Registro de Ensamblado</h2>
                <p class="text-center text-blue-200 text-sm mb-8">¿Qué tipo de operación deseas realizar?</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                 
                    <button onclick="mostrarBuscador()"
                        class="group flex flex-col items-center justify-center gap-3 bg-[#023047] border-2 border-blue-400 hover:bg-blue-900 hover:border-white text-white rounded-xl px-6 py-8 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-10 w-10 text-blue-300 group-hover:text-white transition-colors" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                        </svg>
                        <span class="text-lg font-semibold">Ensamble Existente</span>
                        <span class="text-xs text-blue-300 group-hover:text-blue-100 text-center">Busca un ensamble por ID y
                            agrega cantidad sobrante</span>
                    </button>

                  
                    <button onclick="mostrarFormularioNuevo()"
                        class="group flex flex-col items-center justify-center gap-3 bg-[#023047] border-2 border-orange-400 hover:bg-orange-900 hover:border-white text-white rounded-xl px-6 py-8 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-10 w-10 text-orange-300 group-hover:text-white transition-colors" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="text-lg font-semibold">Ensamble Nuevo</span>
                        <span class="text-xs text-orange-300 group-hover:text-orange-100 text-center">Registra un nuevo
                            ensamblado con material sobrante</span>
                    </button>
                </div>
            </div>

            
            <div id="paso-buscar" class="hidden bg-[#023047] rounded-xl shadow-lg p-8">
                <div class="flex items-center gap-3 mb-6">
                    <button onclick="volverATipo()" class="text-blue-300 hover:text-white transition-colors" title="Volver">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <h2 class="text-xl font-bold text-white">Buscar Ensamble Existente</h2>
                </div>

                <div class="flex gap-3">
                    <div class="flex-1">
                        <label for="buscar_id" class="block mb-2 text-sm font-medium text-white">Selecciona un Ensamblado Existente</label>
                        <select id="buscar_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">-- Selecciona uno --</option>
                            @foreach($ensamblados as $ens)
                                <option value="{{ $ens->id_ensamblado }}">
                                    #{{ $ens->id_ensamblado }} - {{ $ens->nombre_ensamblado ?? ($ens->material->nombre_material ?? 'Material') }} (ID Empleado: {{ $ens->id_empleado }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button onclick="buscarEnsamblado()"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm px-5 py-2.5 transition-colors">
                            Cargar Datos
                        </button>
                    </div>
                </div>

                
                <div id="resultado-busqueda" class="hidden mt-6">
                    <div class="bg-blue-900/40 border border-blue-400 rounded-lg p-4 mb-5">
                        <p class="text-blue-200 text-xs font-semibold uppercase tracking-wider mb-3">Ensamblado encontrado
                        </p>
                        <div class="grid grid-cols-2 gap-y-2 gap-x-4 text-sm text-white">
                            <div><span class="text-blue-300">ID Ensamblado:</span> <span id="res-id"
                                    class="font-bold"></span></div>
                            <div><span class="text-blue-300">ID Empleado:</span> <span id="res-empleado"></span></div>
                            <div class="col-span-2"><span class="text-blue-300">Material:</span> <span id="res-material"
                                    class="font-semibold"></span></div>
                            <div><span class="text-blue-300">Stock actual:</span> <span id="res-stock"
                                    class="font-bold text-green-400"></span></div>
                            <div><span class="text-blue-300">Últ. cantidad:</span> <span id="res-cantidad"></span></div>
                            <div><span class="text-blue-300">Exist. antes:</span> <span id="res-antes"></span></div>
                            <div><span class="text-blue-300">Exist. después:</span> <span id="res-despues"></span></div>
                            <div class="col-span-2"><span class="text-blue-300">Fecha registro:</span> <span
                                    id="res-fecha"></span></div>
                        </div>
                    </div>

                    
                    <form action="{{ route('ensamblados.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="modo" value="existente">
                        <input type="hidden" id="hidden_id_material" name="articulos[0][id_material]">
                        <input type="hidden" id="hidden_id_empleado" name="id_empleado">

                        <label for="cantidad_existente" class="block mb-2 text-sm font-medium text-white">
                            Cantidad sobrante a sumar al stock
                        </label>
                        <div class="flex gap-3 items-start">
                            <div class="flex-1">
                                <input type="number" name="articulos[0][cantidad]" id="cantidad_existente"
                                    min="1" placeholder="Ingresa la cantidad sobrante"
                                    oninput="previewExistente(this)"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                                <p id="preview-existente" class="mt-1 text-xs text-blue-200 hidden">
                                    Stock resultante: <span id="preview-existente-valor"
                                        class="text-orange-300 font-bold"></span>
                                </p>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full text-white bg-[#fb5607] hover:bg-orange-600 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mt-5 transition-colors">
                            Registrar Ensamblado
                        </button>
                    </form>
                </div>

             
                <div id="error-busqueda"
                    class="hidden mt-4 bg-red-900/40 border border-red-400 rounded-lg p-3 text-red-300 text-sm">
                    No se encontró ningún ensamblado con ese ID. Verifica e intenta de nuevo.
                </div>
            </div>

           
            <div id="paso-nuevo" class="hidden bg-[#023047] rounded-xl shadow-lg p-8">
                <div class="flex items-center gap-3 mb-6">
                    <button onclick="volverATipo()" class="text-orange-300 hover:text-white transition-colors"
                        title="Volver">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <h2 class="text-xl font-bold text-white">Nuevo Ensamblado</h2>
                </div>

                @if ($errors->any())
                    <div class="bg-red-900/40 border border-red-400 rounded-lg p-3 mb-5">
                        <ul class="text-red-300 text-sm list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('ensamblados.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="modo" value="nuevo">

                    
                    <div class="mb-5">
                        <label for="id_empleado" class="block mb-2 text-sm font-medium text-white">ID del Empleado</label>
                        <input type="number" name="id_empleado" id="id_empleado" value="{{ old('id_empleado') }}"
                            placeholder="Ej: 3" min="1"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5
                            @error('id_empleado') border-red-500 ring-1 ring-red-500 @enderror">
                        @error('id_empleado')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                   
                    <div id="articulos-container">
                        <div class="articulo-row border border-blue-700 rounded-lg p-4 mb-4" data-index="0">
                            <p class="text-blue-300 text-xs font-semibold uppercase tracking-wider mb-3">Material sobrante
                                #1</p>

                            
                            <div class="mb-3">
                                <label class="block mb-2 text-sm font-medium text-white">Material sobrante</label>
                                <select name="articulos[0][id_material]" onchange="actualizarStock(this, 0)"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                                    <option value="" disabled selected>Selecciona un material</option>
                                    @foreach ($materiales as $m)
                                        <option value="{{ $m->id_material }}" data-stock="{{ $m->existencia }}">
                                            {{ $m->nombre_material }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            
                            <div class="mb-3 flex items-center gap-2 bg-blue-950/40 rounded-lg px-3 py-2">
                                <span class="text-blue-300 text-sm">Existencia actual:</span>
                                <span id="stock-label-0" class="text-green-400 font-bold text-sm">—</span>
                            </div>

                            
                            <div>
                                <label class="block mb-2 text-sm font-medium text-white">
                                    Cantidad sobrante
                                </label>
                                <input type="number" name="articulos[0][cantidad]" min="1" placeholder="Ej: 10"
                                    oninput="actualizarPreview(this, 0)"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                                <div id="preview-0"
                                    class="hidden mt-2 flex items-center gap-2 bg-blue-950/40 rounded-lg px-3 py-2">
                                    <span class="text-blue-300 text-sm">Existencia después:</span>
                                    <span id="preview-valor-0" class="text-orange-300 font-bold text-sm"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Botón agregar artículo --}}
                    {{-- <button type="button" onclick="agregarArticulo()"
                    class="w-full border-2 border-dashed border-blue-500 hover:border-orange-400 text-blue-300 hover:text-orange-300 rounded-lg py-3 text-sm font-medium transition-colors mb-5">
                    + Agregar otro material sobrante
                </button> --}}

                    <button type="submit"
                        class="w-full text-white bg-[#fb5607] hover:bg-orange-600 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors">
                        Registrar Ensamblado
                    </button>
                </form>
            </div>

        </div>
    </section>

    <script>
        
        const materiales = @json($materiales->keyBy('id_material'));
        let articuloIndex = 1;

       
        function mostrarBuscador() {
            document.getElementById('paso-tipo').classList.add('hidden');
            document.getElementById('paso-buscar').classList.remove('hidden');
        }

        function mostrarFormularioNuevo() {
            document.getElementById('paso-tipo').classList.add('hidden');
            document.getElementById('paso-nuevo').classList.remove('hidden');
        }

        function volverATipo() {
            ['paso-buscar', 'paso-nuevo'].forEach(id => document.getElementById(id).classList.add('hidden'));
            document.getElementById('paso-tipo').classList.remove('hidden');
            document.getElementById('resultado-busqueda').classList.add('hidden');
            document.getElementById('error-busqueda').classList.add('hidden');
            document.getElementById('buscar_id').value = '';
        }

       
        async function buscarEnsamblado() {
            const id = document.getElementById('buscar_id').value.trim();
            if (!id) {
                alert('Ingresa un ID válido.');
                return;
            }

            try {
                const resp = await fetch(`/ensamblados/${id}/json`);
                if (!resp.ok) throw new Error('No encontrado');
                const d = await resp.json();

                document.getElementById('res-id').textContent = d.id_ensamblado;
                document.getElementById('res-empleado').textContent = d.id_empleado;
                document.getElementById('res-material').textContent = d.nombre_material ?? `ID ${d.id_material}`;
                document.getElementById('res-stock').textContent = d.existencia_actual ?? '—';
                document.getElementById('res-cantidad').textContent = d.cantidad_sobrante;
                document.getElementById('res-antes').textContent = d.existencia_antes;
                document.getElementById('res-despues').textContent = d.existencia_despues;
                document.getElementById('res-fecha').textContent = d.fecha_registro;

                document.getElementById('hidden_id_material').value = d.id_material;
                document.getElementById('hidden_id_empleado').value = d.id_empleado;

                
                document.getElementById('cantidad_existente').dataset.stock = d.existencia_actual ?? 0;
                document.getElementById('preview-existente').classList.add('hidden');

                document.getElementById('resultado-busqueda').classList.remove('hidden');
                document.getElementById('error-busqueda').classList.add('hidden');
            } catch {
                document.getElementById('resultado-busqueda').classList.add('hidden');
                document.getElementById('error-busqueda').classList.remove('hidden');
            }
        }

        document.getElementById('buscar_id').addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarEnsamblado();
            }
        });

        
        function previewExistente(input) {
            const stock = parseInt(input.dataset.stock) || 0;
            const cantidad = parseInt(input.value) || 0;
            const preview = document.getElementById('preview-existente');
            const valor = document.getElementById('preview-existente-valor');
            if (cantidad > 0) {
                valor.textContent = stock + cantidad;
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
        }

       
        function actualizarStock(select, index) {
            const opt = select.options[select.selectedIndex];
            const stock = opt ? opt.getAttribute('data-stock') : null;
            const label = document.getElementById(`stock-label-${index}`);
            if (label) label.textContent = stock !== null ? stock : '—';

            
            const cantInput = document.querySelector(`[name="articulos[${index}][cantidad]"]`);
            if (cantInput && cantInput.value) actualizarPreview(cantInput, index);
        }

        
        function actualizarPreview(input, index) {
            const label = document.getElementById(`stock-label-${index}`);
            const preview = document.getElementById(`preview-${index}`);
            const valor = document.getElementById(`preview-valor-${index}`);

            const stockActual = parseInt(label?.textContent) || 0;
            const cantidad = parseInt(input.value) || 0;

            if (preview && valor) {
                if (cantidad > 0 && label?.textContent !== '—') {
                    preview.classList.remove('hidden');
                    valor.textContent = stockActual + cantidad;
                } else {
                    preview.classList.add('hidden');
                }
            }
        }

        
        function agregarArticulo() {
            const idx = articuloIndex;
            const container = document.getElementById('articulos-container');

            
            const opciones = Array.from(
                document.querySelectorAll('[name="articulos[0][id_material]"] option')
            ).map(o =>
                `<option value="${o.value}" data-stock="${o.getAttribute('data-stock')}">${o.textContent}</option>`
            ).join('');

            container.insertAdjacentHTML('beforeend', `
        <div class="articulo-row border border-blue-700 rounded-lg p-4 mb-4" data-index="${idx}">
            <div class="flex justify-between items-center mb-3">
                <p class="text-blue-300 text-xs font-semibold uppercase tracking-wider">Material sobrante #${idx + 1}</p>
                <button type="button" onclick="eliminarArticulo(this)"
                    class="text-red-400 hover:text-red-300 text-xs underline">Eliminar</button>
            </div>

            <div class="mb-3">
                <label class="block mb-2 text-sm font-medium text-white">Material sobrante</label>
                <select name="articulos[${idx}][id_material]" onchange="actualizarStock(this, ${idx})"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                    <option value="" disabled selected>Selecciona un material</option>
                    ${opciones}
                </select>
            </div>

            <div class="mb-3 flex items-center gap-2 bg-blue-950/40 rounded-lg px-3 py-2">
                <span class="text-blue-300 text-sm">Existencia actual:</span>
                <span id="stock-label-${idx}" class="text-green-400 font-bold text-sm">—</span>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-white">
                    Cantidad sobrante
                </label>
                <input type="number" name="articulos[${idx}][cantidad]" min="1" placeholder="Ej: 10"
                    oninput="actualizarPreview(this, ${idx})"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5">
                <div id="preview-${idx}" class="hidden mt-2 flex items-center gap-2 bg-blue-950/40 rounded-lg px-3 py-2">
                    <span class="text-blue-300 text-sm">Existencia después:</span>
                    <span id="preview-valor-${idx}" class="text-orange-300 font-bold text-sm"></span>
                </div>
            </div>
        </div>`);

            articuloIndex++;
        }

        
        function eliminarArticulo(btn) {
            btn.closest('.articulo-row').remove();
        }

       
        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', () => {
                const modo = '{{ old('modo', 'nuevo') }}';
                modo === 'existente' ? mostrarBuscador() : mostrarFormularioNuevo();
            });
        @endif

        document.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            const modo = params.get('modo');
            if (modo === 'existente') mostrarBuscador();
            else if (modo === 'nuevo') mostrarFormularioNuevo();
        });
    </script>

@endsection
