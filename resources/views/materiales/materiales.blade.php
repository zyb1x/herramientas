@extends('plantilla.app')

@section('titulo', 'materiales')


@section('contenido')
    <!-- GRID PRODUCTOS -->
    <div class="max-w-6xl mx-auto px-6 mt-10 mb-10">

        <div class="bg-[#023047] rounded-2xl p-5 border border-gray-700 shadow-lg">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-white">Materiales</h1>

                <form action="{{ route('materiales.index') }}" method="GET" class="hidden lg:block lg:pl-2">
                    <label for="topbar-search" class="sr-only">Buscar material</label>
                    <div class="relative mt-1 lg:w-96">
                        <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="text" name="q" id="topbar-search" value="{{ request('q') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-9 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Buscar materiales" autocomplete="off">
                    </div>
                </form>
            </div>
            <div id="lista-materiales" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                @foreach ($materiales as $material)
                    <div
                        class="bg-[#023047] rounded-xl border border-gray-500 overflow-hidden hover:border-orange-600 transition-colors flex flex-col sjadow-lg duration-400">

                        <!-- Contenido -->
                        <div class="p-4 flex flex-col flex-1">

                            @if ($material->estatus === 'Disponible')
                                <p class="text-green-500 text-xs font-bold tracking-widest mb-1">
                                    {{ $material->estatus }}
                                </p>
                            @else
                                <p class="text-red-500 text-xs font-bold tracking-widest mb-1">
                                    {{ $material->estatus }}
                                </p>
                            @endif

                            <h3 class="text-white text-sm font-semibold leading-snug mb-2">
                                {{ $material->nombre_material }}
                            </h3>

                            <div class="flex items-center gap-1 mb-3">
                                <span class="text-gray-400 text-xs">Existencia:</span>
                                <span class="text-white text-xs font-semibold">{{ $material->existencia }}</span>
                            </div>

                            <button data-modal-target="solicitarMaterialModal" data-modal-toggle="solicitarMaterialModal"
                                data-id="{{ $material->id_material }}" data-nombre="{{ $material->nombre_material }}"
                                data-existencia="{{ $material->existencia }}" data-estatus="{{ $material->estatus }}"
                                class="btn-solicitar-material w-full bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold py-2 rounded-lg transition-colors flex items-center justify-center gap-2">
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

    <!-- Modal Solicitar Material -->
    <div id="solicitarMaterialModal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5 shadow-orange-500">
                <!-- Modal header -->
                <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Solicitar Material
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="solicitarMaterialModal">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Cerrar modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form action="{{ route('carrito.agregar.material') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="modal-material-id">
                    <div class="grid gap-4 mb-4 sm:grid-cols-2">
                        <div>
                            <label for="modal-material-nombre"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre:</label>
                            <input type="text" id="modal-material-nombre" readonly
                                class="bg-transparent border-transparent focus:ring-transparent text-gray-900 text-lg font-bold rounded-lg block w-full p-2.5 dark:text-orange-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label for="modal-material-estatus"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estatus</label>
                            <input type="text" id="modal-material-estatus" readonly
                                class="bg-transparent border-transparent focus:ring-transparent text-gray-900 text-lg font-bold rounded-lg block w-full p-2.5  dark:text-orange-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label for="modal-material-existencia"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Existencia
                                disponible</label>
                            <input type="number" id="modal-material-existencia" readonly
                                class="bg-transparent border-transparent focus:ring-transparent text-gray-900 text-lg font-bold rounded-lg block w-full p-2.5 dark:text-orange-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label for="modal-material-cantidad"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cantidad a
                                solicitar</label>
                            <input type="number" name="cantidad" id="modal-material-cantidad" min="1"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Ej. 2">
                            <p id="error-material-cantidad" class="hidden mt-1 text-sm text-red-500"></p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button type="submit"
                            class="text-white bg-orange-500 hover:bg-orange-600 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            Agregar al carrito
                        </button>
                        <button type="button" data-modal-toggle="solicitarMaterialModal"
                            class="text-gray-500 inline-flex items-center hover:text-white border border-gray-500 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-gray-500 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-900">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('modal-material-cantidad').addEventListener('input', function() {
            document.getElementById('error-material-cantidad').classList.add('hidden');
        });

        document.querySelectorAll('.btn-solicitar-material').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('modal-material-id').value = this.dataset.id;
                document.getElementById('modal-material-nombre').value = this.dataset.nombre;
                document.getElementById('modal-material-existencia').value = this.dataset.existencia;
                document.getElementById('modal-material-estatus').value = this.dataset.estatus;
                document.getElementById('modal-material-cantidad').value = '';
                document.getElementById('error-material-cantidad').classList.add('hidden');
            });
        });

        document.querySelector('#solicitarMaterialModal form').addEventListener('submit', function(e) {
            const existencia = parseInt(document.getElementById('modal-material-existencia').value);
            const cantidad = parseInt(document.getElementById('modal-material-cantidad').value);
            const errorEl = document.getElementById('error-material-cantidad');

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
