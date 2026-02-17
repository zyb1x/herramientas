@extends('plantilla.app')

@section('titulo', 'login')


@section('contenido')

    <section class="bg-gray-50 dark:white">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">

            <div
                class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-[#023047] dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        Inicia sesión
                    </h1>
                    @if ($errors->any())
                        <div class="bg-red-500 text-white p-4 rounded-lg mb-4">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form class="space-y-4 md:space-y-6" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div>
                            <label for="email"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Correo
                                electrónico</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="name@company.com" required>
                        </div>
                        <div>
                            <label for="password"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contraseña</label>
                            <input type="password" name="password" id="password" placeholder="••••••••"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">

                                </div>
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full text-white bg-[#fb5607] hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300
                            font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-[#fb5607] dark:hover:bg-primary-700 dark:focus:ring-primary-800">Iniciar
                            sesión</button>

                        <div class="flex items-center my-2">
                            <hr class="flex-grow border-gray-300 dark:border-gray-600">
                            <span class="mx-3 text-sm text-gray-500 dark:text-gray-400">o</span>
                            <hr class="flex-grow border-gray-300 dark:border-gray-600">
                        </div>
                    </form>
                    <form action="{{ route('registro') }}" method="GET">
                        <button type="submit"
                            class="w-full text-[#fb5607] bg-white hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300
                            font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:hover:bg-primary-700 dark:focus:ring-primary-800">Registrate</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection
