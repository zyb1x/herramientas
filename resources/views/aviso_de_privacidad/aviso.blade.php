@extends('plantilla.app')

@section('titulo', 'aviso de privacidad')

@section('contenido')

    <section class="bg-white dark:bg-white">
        <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
            <div class="mr-auto place-self-center lg:col-span-7">
                <h1
                    class="max-w-2xl mb-4 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl dark:text-dark-blue">
                    Aviso de Privacidad</h1>
                <br>

                <p class="mb-6 text-lg font-normal text-gray-500 lg:text-xl dark:text-gray-400">Electric’s Component, con
                    domicilio para
                    efectos académicos, es responsable del uso y
                    protección de sus datos personales, y al respecto le informamos lo siguiente:
                </p>

                <h2 class="text-2xl font-bold mb-4">Finalidad del tratamiento de los datos personales
                </h2>
                <p class="mb-6 text-lg font-normal text-gray-500 lg:text-xl dark:text-gray-400">- Administración de usuarios
                    dentro de la plataforma web desarrollada en PHP con
                    el framework Laravel.
                    <br>
                    - Gestión de servicios, procesos internos y control administrativo.
                    <br>
                    - Autenticación, autorización y control de accesos al sistema.
                    <br>
                    - Registro de actividades dentro de la plataforma.
                </p>

                <h2 class="text-2xl font-bold mb-4">Datos personales recabados
                </h2>
                <p class="mb-6 text-lg font-normal text-gray-500 lg:text-xl dark:text-gray-400">- Nombre del usuario
                    <br>
                    - Correo electrónico
                    <br>
                    - Rol o perfil dentro del sistema
                    <br>
                    - Credenciales de acceso
                </p>

                <h2 class="text-2xl font-bold mb-4">Protección de datos personales</h2>
                <p class="mb-6 text-lg font-normal text-gray-500 lg:text-xl dark:text-gray-400">Electric’s Component
                    implementa medidas
                    de seguridad administrativas, técnicas y físicas para
                    proteger los datos personales, haciendo uso de mecanismos propios del framework
                    Laravel para prevenir accesos no autorizados, pérdida, alteración o uso indebido de
                    la información.
                </p>

                <h2 class="text-2xl font-bold mb-4">Transferencia de datos personales
                </h2>
                <p class="mb-6 text-lg font-normal text-gray-500 lg:text-xl dark:text-gray-400">Los datos personales no
                    serán compartidos con terceros. La información es utilizada
                    exclusivamente con fines académicos y de desarrollo del proyecto.

                </p>

                <h2 class="text-2xl font-bold mb-4">Derechos ARCO
                </h2>
                <p class="mb-6 text-lg font-normal text-gray-500 lg:text-xl dark:text-gray-400">
                    El titular de los datos personales podrá ejercer sus derechos de Acceso,
                    Rectificación, Cancelación u Oposición (ARCO) mediante solicitud directa al
                    administrador del sistema Cloud IT.
                </p>

                <h2 class="text-2xl font-bold mb-4">Derechos ARCO
                </h2>
                <p class="mb-6 text-lg font-normal text-gray-500 lg:text-xl dark:text-gray-400">
                    El titular de los datos personales podrá ejercer sus derechos de Acceso,
                    Rectificación, Cancelación u Oposición (ARCO) mediante solicitud directa al
                    administrador del sistema Cloud IT.
                </p>

                <h2 class="text-2xl font-bold mb-4">Uso académico
                </h2>
                <p class="mb-6 text-lg font-normal text-gray-500 lg:text-xl dark:text-gray-400">
                    El presente Aviso de Privacidad forma parte de un proyecto académico y no
                    representa una solución comercial real.
                </p>

                <h2 class="text-2xl font-bold mb-4">Aceptación del aviso de privacidad
                </h2>
                <p class="mb-6 text-lg font-normal text-gray-500 lg:text-xl dark:text-gray-400">
                    El uso de la plataforma Electric’s Component implica la aceptación del presente Aviso de
                    Privacidad.
                </p>
    </section>
@endsection
