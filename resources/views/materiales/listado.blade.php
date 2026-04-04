@extends('plantilla.app')

@section('titulo', 'listado materiales')

@section('contenido')
    <section class="bg-gray-50 p-3 sm:p-5 antialiased mt-5">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
            <div class="bg-[#023047] relative shadow-md sm:rounded-lg overflow-hidden">

                <form method="GET" action="{{ route('materiales.listado') }}">
                    <div
                        class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">

                        {{-- Buscador --}}
                        <div class="w-full md:w-1/2">
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" name="q" value="{{ request('q') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-400 focus:border-orange-400 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                    placeholder="Buscar material...">
                            </div>
                        </div>

                        <div
                            class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center md:space-x-3 flex-shrink-0">

                            {{-- Filtro estatus --}}
                            <button type="button" id="filtroEstatusBtn" data-dropdown-toggle="filtroEstatusDropdown"
                                class="flex items-center justify-center text-white border border-gray-500 hover:border-orange-400 hover:text-orange-400 duration-300 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                                </svg>
                                Estatus
                                @if (request('estatus'))
                                    <span
                                        class="ml-1 bg-orange-500 text-white text-xs rounded-full px-1.5">{{ count(request('estatus')) }}</span>
                                @endif
                            </button>

                            <div id="filtroEstatusDropdown"
                                class="hidden absolute z-20 mt-1 w-48 bg-white rounded-lg shadow-lg dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                <div class="p-3">
                                    <h6 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Filtrar por estatus
                                    </h6>
                                    <ul class="space-y-2 text-sm">
                                        <li class="flex items-center">
                                            <input type="checkbox" name="estatus[]" value="Disponible" id="est-disponible"
                                                {{ in_array('Disponible', request('estatus', [])) ? 'checked' : '' }}
                                                class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                                            <label for="est-disponible"
                                                class="ml-2 text-sm font-medium text-green-500">Disponible</label>
                                        </li>
                                        <li class="flex items-center">
                                            <input type="checkbox" name="estatus[]" value="Agotado" id="est-agotado"
                                                {{ in_array('Agotado', request('estatus', [])) ? 'checked' : '' }}
                                                class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                                            <label for="est-agotado"
                                                class="ml-2 text-sm font-medium text-red-500">Agotado</label>
                                        </li>
                                    </ul>
                                    <div class="mt-3 flex gap-2">
                                        <button type="submit"
                                            class="flex-1 text-white bg-orange-500 hover:bg-orange-600 font-medium rounded-lg text-xs px-3 py-1.5 text-center">
                                            Aplicar
                                        </button>
                                        <a href="{{ route('materiales.listado') }}"
                                            class="flex-1 text-center text-gray-500 border border-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600 font-medium rounded-lg text-xs px-3 py-1.5">
                                            Limpiar
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Botón buscar --}}
                            <button type="submit"
                                class="flex items-center justify-center text-white bg-orange-500 hover:bg-orange-600 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                                Buscar
                            </button>

                            {{-- Botón registrar --}}
                            <button type="button" data-modal-target="createProductModal"
                                data-modal-toggle="createProductModal"
                                class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-2 hover:text-orange-400 focus:ring-orange-400 duration-300 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                                <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path clip-rule="evenodd" fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                </svg>
                                Registrar Material
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Indicador de filtros activos --}}
                @if (request('q') || request('estatus'))
                    <div class="px-4 pb-3 flex items-center gap-2 flex-wrap">
                        <span class="text-gray-400 text-xs">Filtros activos:</span>
                        @if (request('q'))
                            <span class="bg-orange-500 text-white text-xs rounded-full px-2 py-0.5">
                                "{{ request('q') }}"
                            </span>
                        @endif
                        @foreach (request('estatus', []) as $e)
                            <span
                                class="bg-orange-500 text-white text-xs rounded-full px-2 py-0.5">{{ $e }}</span>
                        @endforeach
                        <a href="{{ route('materiales.listado') }}"
                            class="text-gray-400 hover:text-white text-xs underline">
                            Limpiar todo
                        </a>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-4">ID</th>
                                <th scope="col" class="px-4 py-3">Nombre</th>
                                <th scope="col" class="px-4 py-3">Existencia</th>
                                <th scope="col" class="px-4 py-3">Estatus</th>
                                <th scope="col" class="px-4 py-3"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($materiales) && count($materiales) > 0)
                                @foreach ($materiales as $material)
                                    <tr class="border-b dark:border-gray-700">
                                        <th scope="row"
                                            class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $material['id_material'] }}
                                        </th>
                                        <td class="px-4 py-3">{{ $material['nombre_material'] }}</td>
                                        <td class="px-4 py-3">{{ $material['existencia'] }}</td>
                                        <td
                                            class="px-4 py-3 font-semibold {{ $material['estatus'] === 'Disponible' ? 'text-green-500' : 'text-red-500' }}">
                                            {{ $material['estatus'] }}
                                        </td>
                                        <td class="px-4 py-3 flex items-center justify-end">
                                            <button id="material-{{ $material['id_material'] }}-dropdown-button"
                                                data-dropdown-toggle="material-{{ $material['id_material'] }}-dropdown"
                                                class="inline-flex items-center text-sm font-medium hover:bg-gray-100 dark:hover:bg-orange-300 duration-200 p-1.5 text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100"
                                                type="button">
                                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                </svg>
                                            </button>
                                            <div id="material-{{ $material['id_material'] }}-dropdown"
                                                class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:divide-gray-600">
                                                <ul class="py-1 text-sm"
                                                    aria-labelledby="material-{{ $material['id_material'] }}-dropdown-button">
                                                    <li>
                                                        <a href="{{ route('materiales.edit', $material['id_material']) }}"
                                                            class="flex w-full items-center py-2 px-4 hover:bg-orange-300 text-gray-700 duration-300">
                                                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg"
                                                                viewBox="0 0 20 20" fill="currentColor">
                                                                <path
                                                                    d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                                                            </svg>
                                                            Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form
                                                            action="{{ route('materiales.destroy', $material['id_material']) }}"
                                                            method="POST" class="w-full">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                onclick="return confirm('¿Estás seguro de eliminar este material?')"
                                                                class="flex w-full items-center py-2 px-4 hover:bg-red-600 text-red-600 hover:text-white duration-300">
                                                                <svg class="w-4 h-4 mr-2" viewBox="0 0 14 15"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        fill="currentColor"
                                                                        d="M6.09922 0.300781C5.93212 0.30087 5.76835 0.347476 5.62625 0.435378C5.48414 0.523281 5.36931 0.649009 5.29462 0.798481L4.64302 2.10078H1.59922C1.36052 2.10078 1.13161 2.1956 0.962823 2.36439C0.79404 2.53317 0.699219 2.76209 0.699219 3.00078C0.699219 3.23948 0.79404 3.46839 0.962823 3.63718C1.13161 3.80596 1.36052 3.90078 1.59922 3.90078V12.9008C1.59922 13.3782 1.78886 13.836 2.12643 14.1736C2.46399 14.5111 2.92183 14.7008 3.39922 14.7008H10.5992C11.0766 14.7008 11.5344 14.5111 11.872 14.1736C12.2096 13.836 12.3992 13.3782 12.3992 12.9008V3.90078C12.6379 3.90078 12.8668 3.80596 13.0356 3.63718C13.2044 3.46839 13.2992 3.23948 13.2992 3.00078C13.2992 2.76209 13.2044 2.53317 13.0356 2.36439C12.8668 2.1956 12.6379 2.10078 12.3992 2.10078H9.35542L8.70382 0.798481C8.62913 0.649009 8.5143 0.523281 8.37219 0.435378C8.23009 0.347476 8.06631 0.30087 7.89922 0.300781H6.09922ZM4.29922 5.70078C4.29922 5.46209 4.39404 5.23317 4.56282 5.06439C4.73161 4.8956 4.96052 4.80078 5.19922 4.80078C5.43791 4.80078 5.66683 4.8956 5.83561 5.06439C6.0044 5.23317 6.09922 5.46209 6.09922 5.70078V11.1008C6.09922 11.3395 6.0044 11.5684 5.83561 11.7372C5.66683 11.906 5.43791 12.0008 5.19922 12.0008C4.96052 12.0008 4.73161 11.906 4.56282 11.7372C4.39404 11.5684 4.29922 11.3395 4.29922 11.1008V5.70078ZM8.79922 4.80078C8.56052 4.80078 8.33161 4.8956 8.16282 5.06439C7.99404 5.23317 7.89922 5.46209 7.89922 5.70078V11.1008C7.89922 11.3395 7.99404 11.5684 8.16282 11.7372C8.33161 11.906 8.56052 12.0008 8.79922 12.0008C9.03791 12.0008 9.26683 11.906 9.43561 11.7372C9.6044 11.5684 9.69922 11.3395 9.69922 11.1008V5.70078C9.69922 5.46209 9.6044 5.23317 9.43561 5.06439C9.26683 4.8956 9.03791 4.80078 8.79922 4.80078Z" />
                                                                </svg>
                                                                Eliminar
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No hay materiales registrados.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
