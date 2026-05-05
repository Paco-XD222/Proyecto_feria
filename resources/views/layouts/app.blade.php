<!DOCTYPE html>
{{-- Define el inicio del documento HTML y establece el idioma según la configuración de Laravel --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <style>
        #fondo-video {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -100; /* Detrás de todo el contenido */
            background-size: cover;
            filter: brightness(60%); /* Oscurecer el video para que el texto resalte */
        }
    </style>
        {{-- Configuración básica del documento --}}
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {{-- Token CSRF: esencial para la seguridad en formularios y peticiones AJAX de Laravel --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- Carga los archivos CSS y JS compilados por Vite (incluyendo Tailwind CSS) --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Se necesita para dar estilo y funcionalidad de buscador al campo <select> --}}
       <link href="{{ asset('assets/select2/css/select2.min.css') }}" rel="stylesheet" />
        
    </head>
    <body class="font-sans antialiased ">
        <video autoplay muted loop id="fondo-video">
        <source src="{{ asset('videos/fondo.mp4') }}" type="video/mp4">
        Tu navegador no soporta el fondo de video.
    </video>
        <div class="min-h-screen bg-transparent">
            {{-- Incluye la barra de navegación principal (típicamente layouts/navigation.blade.php) --}}
            @include('layouts.navigation')

            {{-- Muestra el encabezado de la página si la vista hija ha definido una sección 'header' --}}
            @hasSection('header')
            <header class="bg-white shadow  ">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
            @endif

            <main>
                {{-- Aquí se inyectará el contenido principal de la vista hija (ej. pagos/create.blade.php) --}}
                @yield('content')
            </main>
        </div>

        {{-- jQuery es necesario para que Select2 funcione. Debe cargarse antes de Select2 --}}
         <script src="{{ asset('assets/jquery/jquery-3.7.1.min.js') }}"></script>
        {{-- Librería principal de Select2. Transforma el <select> en un campo de búsqueda --}}
       <script src="{{ asset('assets/select2/js/select2.min.js') }}"></script>

        {{-- Permite a las vistas hijas (como pagos/create.blade.php) añadir código JS específico --}}
        {{-- En este caso, lo usamos para inicializar Select2: @push('scripts') ... @endpush --}}
        @stack('scripts') 

    </body>
</html>