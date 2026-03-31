@extends('plantilla.app')

@section('titulo', 'herramientas')

@section('contenido')
    <div class="max-w-6xl mx-auto px-6 mt-10 mb-10">
        <div class="bg-[#023047] rounded-2xl p-5 border border-gray-700 shadow-lg">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-white">Herramientas</h1>

                <div class="hidden lg:block lg:pl-2">
                    <div class="relative mt-1 lg:w-96">
                        <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="text" id="buscador"
                            class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-9 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Buscar herramientas" autocomplete="off">
                    </div>
                </div>
            </div>

            <div id="lista-herramientas" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($herramientas as $herramienta)
                    <div
                        class="bg-[#023047] rounded-xl border border-gray-500 overflow-hidden hover:border-orange-600 transition-colors flex flex-col shadow-lg duration-400">
                        <div class="bg-white flex items-center justify-center h-56 p-4">
                            <img class="h-44 object-contain" src="{{ $herramienta->imagen }}"
                                alt="{{ $herramienta->nombre_herramienta }}">
                        </div>
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
                            <button data-modal-target="solicitarModal" data-modal-toggle="solicitarModal"
                                data-id="{{ $herramienta->id_herramienta }}"
                                data-nombre="{{ $herramienta->nombre_herramienta }}"
                                data-categoria="{{ $herramienta->categoria->nombre_categoria }}"
                                data-existencia="{{ $herramienta->existencia }}" data-imagen="{{ $herramienta->imagen }}"
                                class="btn-solicitar w-full bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold py-2 rounded-lg transition-colors flex items-center justify-center gap-2">
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

    <!-- Modal Solicitar Herramienta -->
    <div id="solicitarModal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
            <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5 shadow-orange-500">
                <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Solicitar Herramienta
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="solicitarModal">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Cerrar modal</span>
                    </button>
                </div>

                <form action="{{ route('carrito.agregar.herramienta') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="modal-herramienta-id">
                    <div class="grid gap-4 mb-4 sm:grid-cols-2">
                        <div class="sm:col-span-2 flex justify-center">
                            <img id="modal-imagen" src="" alt="Herramienta"
                                class="h-36 object-contain rounded-lg border border-gray-200 dark:border-gray-600 p-2 bg-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre:</label>
                            <input type="text" id="modal-nombre" readonly
                                class="bg-transparent border-transparent focus:ring-transparent text-lg font-bold text-gray-900 rounded-lg block w-full p-2.5 dark:text-orange-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categoría:</label>
                            <input type="text" id="modal-categoria" readonly
                                class="bg-transparent border-transparent focus:ring-transparent text-lg font-bold text-gray-900 rounded-lg block w-full p-2.5 dark:text-orange-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Existencia
                                disponible:</label>
                            <input type="number" id="modal-existencia" readonly
                                class="bg-transparent border-transparent focus:ring-transparent text-lg font-bold text-gray-900 rounded-lg block w-full p-2.5 dark:text-orange-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cantidad a
                                solicitar</label>
                            <input type="number" name="cantidad" id="modal-cantidad" min="1"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Ej. 2">
                            <p id="error-cantidad" class="hidden mt-1 text-sm text-red-500"></p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button type="submit"
                            class="text-white bg-orange-500 hover:bg-orange-600 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            Solicitar
                        </button>
                        <button type="button" data-modal-toggle="solicitarModal"
                            class="text-gray-500 inline-flex items-center hover:text-white border border-gray-500 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-gray-500 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-900">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function asignarBotonesSolicitar() {
            document.querySelectorAll('.btn-solicitar').forEach(function(btn) {
                btn.removeEventListener('click', abrirModal);
                btn.addEventListener('click', abrirModal);
            });
        }

        function abrirModal() {
            document.getElementById('modal-herramienta-id').value = this.dataset.id;
            document.getElementById('modal-nombre').value = this.dataset.nombre;
            document.getElementById('modal-categoria').value = this.dataset.categoria;
            document.getElementById('modal-existencia').value = this.dataset.existencia;
            document.getElementById('modal-imagen').src = this.dataset.imagen;
            document.getElementById('modal-imagen').alt = this.dataset.nombre;
            document.getElementById('modal-cantidad').value = '';
            document.getElementById('error-cantidad').classList.add('hidden');
        }

        asignarBotonesSolicitar();

        const listaOriginal = document.getElementById('lista-herramientas').innerHTML;

        document.getElementById('buscador').addEventListener('input', function() {
            const q = this.value.trim();

            if (q === '') {
                document.getElementById('lista-herramientas').innerHTML = listaOriginal;
                asignarBotonesSolicitar();
                return;
            }

            clearTimeout(this._timer);
            this._timer = setTimeout(() => {
                fetch(`/herramientas/buscar?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(herramientas => {
                        const lista = document.getElementById('lista-herramientas');

                        if (herramientas.length === 0) {
                            lista.innerHTML = `
                                <p class="text-gray-400 col-span-4 text-center py-8">
                                    No se encontraron herramientas.
                                </p>`;
                            return;
                        }

                        lista.innerHTML = herramientas.map(h => `
                            <div class="bg-[#023047] rounded-xl border border-gray-500 overflow-hidden hover:border-orange-600 transition-colors flex flex-col shadow-lg duration-400">
                                <div class="bg-white flex items-center justify-center h-56 p-4">
                                    <img class="h-44 object-contain" src="${h.imagen}" alt="${h.nombre_herramienta}">
                                </div>
                                <div class="p-4 flex flex-col flex-1">
                                    <p class="text-orange-400 text-xs font-bold tracking-widest mb-1">
                                        ${h.categoria ? h.categoria.nombre_categoria : ''}
                                    </p>
                                    <h3 class="text-white text-sm font-semibold leading-snug mb-2">
                                        ${h.nombre_herramienta}
                                    </h3>
                                    <div class="flex items-center gap-1 mb-3">
                                        <span class="text-gray-400 text-xs">Existencia:</span>
                                        <span class="text-white text-xs font-semibold">${h.existencia}</span>
                                    </div>
                                    <button
                                        data-modal-target="solicitarModal"
                                        data-modal-toggle="solicitarModal"
                                        data-id="${h.id_herramienta}"
                                        data-nombre="${h.nombre_herramienta}"
                                        data-categoria="${h.categoria ? h.categoria.nombre_categoria : ''}"
                                        data-existencia="${h.existencia}"
                                        data-imagen="${h.imagen}"
                                        class="btn-solicitar w-full bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold py-2 rounded-lg transition-colors flex items-center justify-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Solicitar
                                    </button>
                                </div>
                            </div>
                        `).join('');

                        asignarBotonesSolicitar();
                    });
            }, 300);
        });

        document.getElementById('modal-cantidad').addEventListener('input', function() {
            document.getElementById('error-cantidad').classList.add('hidden');
        });

        document.querySelector('#solicitarModal form').addEventListener('submit', function(e) {
            const existencia = parseInt(document.getElementById('modal-existencia').value);
            const cantidad = parseInt(document.getElementById('modal-cantidad').value);
            const errorEl = document.getElementById('error-cantidad');

            if (!cantidad || cantidad < 1) {
                e.preventDefault();
                errorEl.textContent = 'La cantidad debe ser mayor a 0.';
                errorEl.classList.remove('hidden');
                return;
            }

            if (cantidad > existencia) {
                e.preventDefault();
                errorEl.textContent = `La cantidad no puede exceder la existencia disponible (${existencia}).`;
                errorEl.classList.remove('hidden');
                return;
            }

            errorEl.classList.add('hidden');
        });
    </script>
@endsection
