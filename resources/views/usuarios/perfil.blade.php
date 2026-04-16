@extends('plantilla.app')

@section('titulo', 'Mi Perfil')

@section('contenido')

    <section class="bg-white dark:bg-white min-h-screen py-10 px-4">
        <div class="mx-auto max-w-4xl">

            {{-- Encabezado del perfil --}}
            <div class="relative bg-[#023047] rounded-2xl shadow-lg p-6 mb-6 flex flex-col sm:flex-row items-center gap-6">

                {{-- Avatar --}}
                <div class="relative group">
                   <img id="avatar-preview" 
    src="{{ session('usuario')['imagen'] ?? asset('imagenes/default.jpg') }}"
    alt="Avatar" 
    class="rounded-full object-cover border-4 border-orange-500 shadow-md"
    style="width: 112px; height: 112px;">
                    
                </div>

                {{-- Info básica --}}
                <div class="text-center sm:text-left">
                    <h1 class="text-2xl text-white">{{ session('usuario')['nombre'] }}</h1>
                    <p class="text-orange-400 font-medium text-center">{{ session('usuario')['correo'] }}</p>
                    <div class="flex flex-wrap gap-2 mt-2 justify-center sm:justify-start">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-500 text-white">
                            {{ session('usuario')['rol'] }}
                        </span>
                        @if (session('usuario')['turno'])
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-600 text-white">
                                Turno {{ session('usuario')['turno'] }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- ID badge --}}
                <div class="sm:ml-auto text-center">
                    <span class="text-xs text-gray-400">ID de usuario</span>
                    <p class="text-orange-400 font-bold text-lg">#{{ session('usuario')['id'] }}</p>
                </div>
            </div>

            {{-- Alertas --}}
            @if (session('success'))
                <div
                    class="flex items-center gap-3 p-4 mb-6 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                    <svg class="w-5 h-5 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="flex items-start gap-3 p-4 mb-6 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                    <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulario --}}
            <form action="/perfil/actualizar" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="bg-[#023047] rounded-2xl shadow-lg p-6 mb-6">
                    <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Información personal
                    </h2>

                    <div class="grid gap-4 sm:grid-cols-2">

                        {{-- Nombre --}}
                        <div class="sm:col-span-2">
                            <label for="nombre" class="block mb-2 text-sm font-medium text-gray-300">Nombre
                                completo</label>
                            <input type="text" name="nombre" id="nombre"
                                value="{{ old('nombre', session('usuario')['nombre']) }}" placeholder="Tu nombre completo"
                                class="bg-gray-700 border text-white text-sm rounded-lg block w-full p-2.5 placeholder-gray-400 focus:ring-orange-500 focus:border-orange-500
                            {{ $errors->has('nombre') ? 'border-red-500' : 'border-gray-600' }}">
                            @error('nombre')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Correo --}}
                        <div>
                            <label for="correo" class="block mb-2 text-sm font-medium text-gray-300">Correo
                                electrónico</label>
                            <input type="email" name="correo" id="correo"
                                value="{{ old('correo', session('usuario')['correo']) }}" placeholder="correo@ejemplo.com"
                                class="bg-gray-700 border text-white text-sm rounded-lg block w-full p-2.5 placeholder-gray-400 focus:ring-orange-500 focus:border-orange-500
                            {{ $errors->has('correo') ? 'border-red-500' : 'border-gray-600' }}">
                            @error('correo')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Usuario --}}
                        <div>
                            <label for="usuario" class="block mb-2 text-sm font-medium text-gray-300">Nombre de
                                usuario</label>
                            <input type="text" name="usuario" id="usuario"
                                value="{{ old('usuario', session('usuario')['usuario'] ?? '') }}" placeholder="usuario123"
                                class="bg-gray-700 border text-white text-sm rounded-lg block w-full p-2.5 placeholder-gray-400 focus:ring-orange-500 focus:border-orange-500
                            {{ $errors->has('usuario') ? 'border-red-500' : 'border-gray-600' }}">
                            @error('usuario')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Turno --}}
                        <div>
                            <label for="turno" class="block mb-2 text-sm font-medium text-gray-300">Turno</label>
                            <select name="turno" id="turno"
                                class="bg-gray-700 border text-white text-sm rounded-lg block w-full p-2.5 focus:ring-orange-500 focus:border-orange-500
                            {{ $errors->has('turno') ? 'border-red-500' : 'border-gray-600' }}">
                                <option value="">Selecciona tu turno</option>
                                @foreach (['Matutino', 'Vespertino', 'Nocturno'] as $turno)
                                    <option value="{{ $turno }}"
                                        {{ old('turno', session('usuario')['turno']) == $turno ? 'selected' : '' }}>
                                        {{ $turno }}
                                    </option>
                                @endforeach
                            </select>
                            @error('turno')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Rol (solo lectura) --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-300">Rol</label>
                            <input type="text" value="{{ session('usuario')['rol'] }}" disabled
                                class="bg-gray-800 border border-gray-700 text-gray-400 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed">
                            <p class="mt-1 text-xs text-gray-500">El rol no puede ser modificado desde el perfil.</p>
                        </div>

                    </div>
                </div>

                {{-- Cambiar contraseña --}}
                <div class="bg-[#023047] rounded-2xl shadow-lg p-6 mb-6">
                    <h2 class="text-lg font-semibold text-white mb-1 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Cambiar contraseña
                    </h2>
                    <p class="text-sm text-gray-400 mb-4">Déjalo en blanco si no deseas cambiarla.</p>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="contrasena" class="block mb-2 text-sm font-medium text-gray-300">Nueva
                                contraseña</label>
                            <input type="password" name="contrasena" id="contrasena" placeholder="Mínimo 6 caracteres"
                                class="bg-gray-700 border text-white text-sm rounded-lg block w-full p-2.5 placeholder-gray-400 focus:ring-orange-500 focus:border-orange-500
                            {{ $errors->has('contrasena') ? 'border-red-500' : 'border-gray-600' }}">
                            @error('contrasena')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="conf_contrasena" class="block mb-2 text-sm font-medium text-gray-300">Confirmar
                                contraseña</label>
                            <input type="password" name="conf_contrasena" id="conf_contrasena"
                                placeholder="Repite la contraseña"
                                class="bg-gray-700 border text-white text-sm rounded-lg block w-full p-2.5 placeholder-gray-400 focus:ring-orange-500 focus:border-orange-500
                            {{ $errors->has('conf_contrasena') ? 'border-red-500' : 'border-gray-600' }}">
                            @error('conf_contrasena')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Foto de perfil --}}
                <div class="bg-[#023047] rounded-2xl shadow-lg p-6 mb-6">
                    <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Foto de perfil
                    </h2>

                    {{-- Preview imagen actual --}}
                    <div id="current-image" class="{{ session('usuario')['imagen'] ? '' : 'hidden' }} mb-4">
                        <p class="text-sm text-gray-400 mb-2">Imagen actual:</p>
                        <div class="relative inline-block">
                            <img id="preview-img" src="{{ session('usuario')['imagen'] ?? '' }}" alt="Avatar"
                                class="h-32 w-32 rounded-full object-cover border-4 border-orange-500">
                            <button type="button" id="remove-image"
                                class="absolute -top-1 -right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold shadow transition-colors">
                                ✕
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="eliminar_imagen" id="eliminar_imagen" value="0">

                    {{-- Dropzone --}}
                    <label for="dropzone-file" id="dropzone"
                        class="{{ session('usuario')['imagen'] ? 'hidden' : '' }} flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-600 rounded-lg cursor-pointer bg-gray-700 hover:bg-gray-600 transition-colors duration-200">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="mb-1 text-sm text-gray-400"><span class="font-semibold text-orange-400">Click para
                                    subir</span> o arrastra aquí</p>
                            <p class="text-xs text-gray-500">PNG, JPG (Max. 2MB)</p>
                        </div>
                        <input id="dropzone-file" name="imagen" type="file" accept="image/*" class="hidden" />
                    </label>

                    <div id="file-list" class="mt-3 hidden"></div>

                    @error('imagen')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botón guardar --}}
                <button type="submit"
                    class="w-full text-white bg-orange-500 hover:bg-orange-600 focus:ring-4 focus:outline-none focus:ring-orange-300 font-semibold rounded-xl text-sm px-5 py-3 text-center transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Guardar cambios
                </button>
            </form>
        </div>
    </section>

    <script>
        const dropzone = document.getElementById('dropzone');
        const currentImage = document.getElementById('current-image');
        const removeBtn = document.getElementById('remove-image');
        const previewImg = document.getElementById('preview-img');
        const avatarPreview = document.getElementById('avatar-preview');
        const fileInput = document.getElementById('dropzone-file');
        const eliminarInput = document.getElementById('eliminar_imagen');
        const fileList = document.getElementById('file-list');

        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                currentImage.classList.add('hidden');
                dropzone.classList.remove('hidden');
                eliminarInput.value = '1';
                fileInput.value = '';
                fileList.classList.add('hidden');
                fileList.innerHTML = '';
            });
        }

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(event) {
                previewImg.src = event.target.result;
                avatarPreview.src = event.target.result;
                currentImage.classList.remove('hidden');
                dropzone.classList.add('hidden');
                eliminarInput.value = '0';
            };
            reader.readAsDataURL(file);

            fileList.innerHTML = '';
            fileList.classList.remove('hidden');
            const fileElement = document.createElement('div');
            fileElement.className = 'flex items-center justify-between p-3 bg-gray-800 rounded-lg';
            fileElement.innerHTML = `
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm text-gray-300 truncate">${file.name}</span>
            </div>
            <span class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</span>`;
            fileList.appendChild(fileElement);
        });
    </script>

@endsection
