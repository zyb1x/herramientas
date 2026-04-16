@extends('plantilla.app')

@section('titulo', 'listado herramientas')

@section('contenido')
    <section class="bg-gray-50 p-3 sm:p-5 antialiased mt-5">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
            <div class="bg-[#023047] relative shadow-md sm:rounded-lg overflow-visible">

                <form method="GET" action="{{ route('herramientas.listado') }}">
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
                                    placeholder="Buscar herramienta...">
                            </div>
                        </div>

                        <div
                            class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center md:space-x-3 flex-shrink-0">

                            {{-- Filtro categorías (dinámico desde BD) --}}
                            <div class="relative">
                                <button type="button" id="filtroCategoriasBtn"
                                    data-dropdown-toggle="filtroCategoriasDropdown"
                                    class="flex items-center justify-center text-white border border-gray-500 hover:border-orange-400 hover:text-orange-400 duration-300 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                                    </svg>
                                    Categoría
                                    @if (request('categorias'))
                                        <span
                                            class="ml-1 bg-orange-500 text-white text-xs rounded-full px-1.5">{{ count(request('categorias')) }}</span>
                                    @endif
                                </button>

                                <div id="filtroCategoriasDropdown"
                                    class="hidden absolute right-0 z-20 mt-1 w-56 bg-white rounded-lg shadow-lg dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                    <div class="p-3">
                                        <h6 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Filtrar por
                                            categoría</h6>
                                        <ul class="space-y-2 text-sm max-h-48 overflow-y-auto">
                                            @foreach ($categorias as $categoria)
                                                <li class="flex items-center">
                                                    <input type="checkbox" name="categorias[]"
                                                        value="{{ $categoria['id_categoria'] }}"
                                                        id="cat-{{ $categoria['id_categoria'] }}"
                                                        {{ in_array($categoria['id_categoria'], request('categorias', [])) ? 'checked' : '' }}
                                                        class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                                                    <label for="cat-{{ $categoria['id_categoria'] }}"
                                                        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $categoria['nombre_categoria'] }}
                                                    </label>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <div class="mt-3 flex gap-2">
                                            <button type="submit"
                                                class="flex-1 text-white bg-orange-500 hover:bg-orange-600 font-medium rounded-lg text-xs px-3 py-1.5 text-center">
                                                Aplicar
                                            </button>
                                            <a href="{{ route('herramientas.listado') }}"
                                                class="flex-1 text-center text-gray-500 border border-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600 font-medium rounded-lg text-xs px-3 py-1.5">
                                                Limpiar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Botón buscar --}}
                            <button type="submit"
                                class="flex items-center justify-center text-white bg-orange-500 hover:bg-orange-600 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                                Buscar
                            </button>

                            {{-- Botón registrar --}}
                            @if (in_array(session('usuario')['rol'], ['Administrador']))
                                <button type="button" data-modal-target="createProductModal"
                                    data-modal-toggle="createProductModal"
                                    class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-2 hover:text-orange-400 focus:ring-orange-400 duration-300 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none">
                                    <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                    </svg>
                                    <a href="/herramientas/registro"> Registrar Herramienta </a>
                                </button>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Indicador de filtros activos --}}
                @if (request('q') || request('categorias'))
                    <div class="px-4 pb-3 flex items-center gap-2 flex-wrap">
                        <span class="text-gray-400 text-xs">Filtros activos:</span>
                        @if (request('q'))
                            <span class="bg-orange-500 text-white text-xs rounded-full px-2 py-0.5">
                                "{{ request('q') }}"
                            </span>
                        @endif
                        @foreach (request('categorias', []) as $catId)
                            @php $cat = collect($categorias)->firstWhere('id_categoria', $catId) @endphp
                            @if ($cat)
                                <span class="bg-orange-500 text-white text-xs rounded-full px-2 py-0.5">
                                    {{ $cat['nombre_categoria'] }}
                                </span>
                            @endif
                        @endforeach
                        <a href="{{ route('herramientas.listado') }}"
                            class="text-gray-400 hover:text-white text-xs underline">
                            Limpiar todo
                        </a>
                    </div>
                @endif

                <div class="overflow-x-auto overflow-y-auto" style="max-height:750px;">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs uppercase bg-gray-700 text-gray-400 sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="px-4 py-4">ID</th>
                                <th scope="col" class="px-4 py-3">Nombre</th>
                                <th scope="col" class="px-4 py-3">Categoría</th>
                                <th scope="col" class="px-4 py-3">Existencia</th>
                                <th scope="col" class="px-4 py-3">Estado</th>
                                <th scope="col" class="px-4 py-3">Disponibilidad</th>
                                <th scope="col" class="px-4 py-3">Imagen</th>
                                <th scope="col" class="px-4 py-3"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($herramientas) && count($herramientas) > 0)
                                @foreach ($herramientas as $herramienta)
                                    <tr class="border-b dark:border-gray-700 font-bold">
                                        <th scope="row"
                                            class="px-4 py-3 font-medium whitespace-nowrap text-orange-500">
                                            {{ $herramienta['id_herramienta'] }}
                                        </th>
                                        <td class="px-4 py-3">{{ $herramienta['nombre_herramienta'] }}</td>
                                        <td class="px-4 py-3">{{ $herramienta['categoria']['nombre_categoria'] }}</td>
                                        <td class="px-4 py-3">{{ $herramienta['existencia'] }}</td>
                                        <td class="px-4 py-3">{{ $herramienta['estado'] }}</td>
                                        <td
                                            class="px-4 py-3 font-semibold {{ $herramienta['disponible'] ? 'text-green-500' : 'text-red-500' }}">
                                            {{ $herramienta['disponible'] ? 'Disponible' : 'No disponible' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @php $primeraImagen = $herramienta['imagen']; @endphp
                                            @if ($primeraImagen && !str_contains($primeraImagen, 'producto_default'))
                                                <img src="{{ $primeraImagen }}" class="w-16 h-16 object-cover rounded"
                                                    alt="Imagen herramienta">
                                            @else
                                                <span class="text-gray-400">Sin imagen</span>
                                            @endif
                                        </td>
                                        @if (in_array(session('usuario')['rol'], ['Administrador']))
                                            <td class="px-4 py-3 flex items-center justify-end">
                                                <button
                                                    id="herramienta-{{ $herramienta['id_herramienta'] }}-dropdown-button"
                                                    data-dropdown-toggle="herramienta-{{ $herramienta['id_herramienta'] }}-dropdown"
                                                    class="inline-flex items-center text-sm font-medium hover:bg-gray-100 dark:hover:bg-orange-300 duration-200 p-1.5 text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100"
                                                    type="button">
                                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    </svg>
                                                </button>
                                                <div id="herramienta-{{ $herramienta['id_herramienta'] }}-dropdown"
                                                    class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:divide-gray-600">
                                                    <ul class="py-1 text-sm"
                                                        aria-labelledby="herramienta-{{ $herramienta['id_herramienta'] }}-dropdown-button">
                                                        <li>
                                                            <a href="{{ route('herramientas.edit', $herramienta['id_herramienta']) }}"
                                                                class="flex w-full items-center py-2 px-4 hover:bg-orange-300 text-gray-700 duration-300">
                                                                <svg class="w-4 h-4 mr-2"
                                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                                    fill="currentColor">
                                                                    <path
                                                                        d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                                                                </svg>
                                                                Editar
                                                            </a>
                                                        </li>
                                                        {{-- <li>
                                                            <form
                                                                action="{{ route('herramientas.destroy', $herramienta['id_herramienta']) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('¿Estás seguro de desactivar esta herramienta?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="flex w-full items-center py-2 px-4 hover:bg-red-600 text-red-600 hover:text-white duration-300">
                                                                    Desactivar
                                                                </button>
                                                            </form>
                                                        </li> --}}
                                                    </ul>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No hay herramientas registradas.
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
