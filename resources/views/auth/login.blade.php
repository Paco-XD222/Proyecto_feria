<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

   
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* 1. Estilos para el video de fondo (CORREGIDO para RESPONSIVIDAD) */
        #fondo-video {
            position: fixed;
            top: 0;
            left: 0;
            min-width: 100vw; 
            min-height: 100vh;
            width: auto;
            height: auto;
            z-index: -100;
            background-size: cover;
            filter: brightness(75%);
            /* Propiedad clave para celulares */
            object-fit: cover; 
        }

        /* 2. Estilo de animación para que el formulario aparezca suavemente */
        .fade-in-form { animation: fadeIn 1.2s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Asegura que la fuente base exista y elimina márgenes del body */
        body {
            font-family: 'Figtree', sans-serif; 
            margin: 0;       
            padding: 0;      
            overflow-x: hidden;
        }

        /* 🟢 Estilos para los campos de entrada estilo glassmorphism (solo borde inferior) */
        .input-glass {
            background-color: transparent !important; 
            border: none !important;
            border-bottom: 2px solid rgba(255, 255, 255, 0.7) !important;
            color: white !important;
            padding-left: 30px !important; 
            padding-right: 40px !important; /* Espacio para el botón de mostrar/ocultar */
            transition: border-color 0.3s ease;
        }
        .input-glass:focus {
            border-color: #667EEA !important; 
            outline: none !important;
            box-shadow: none !important;
        }
        .input-glass::placeholder {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        /* 🌟 Efecto de serpientes persiguiéndose alrededor del borde */
        .rotating-border { position: relative; }
        .rotating-border::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 24px;
            padding: 4px;
            background: conic-gradient(
                from 0deg,
                transparent 0deg, transparent 60deg, #ff0000 60deg, 
                #ff3333 90deg, #ff6666 120deg, #ff0000 150deg, 
                transparent 150deg, transparent 240deg, #0066ff 240deg, 
                #3385ff 270deg, #66a3ff 300deg, #0066ff 330deg, 
                transparent 330deg, transparent 360deg
            );
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            animation: rotateBorder 3s linear infinite;
            z-index: -1;
        }

        @keyframes rotateBorder {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .institution-name {
            font-size: 2.5rem; 
            font-weight: 100;
            color: white;
            border-color: yellow;
            text-align: center;
            margin-bottom: 2rem;
            text-shadow: 0 0 10px rgba(0, 0, 0, 0.8), 0 0 20px rgba(255, 208, 0, 0.63); 
            animation: slideInDown 1s ease-out;
        }
        
        /* 🚨 SOLUCIÓN PARA OCULTAR BARRA DE DESCARGA EXTERNA */
        *[id*="download-video-bar"], *[class*="video-download-btn"], *[class*="video-downloader-icon"] {
            display: none !important;
            visibility: hidden !important;
        }
    </style>
</head>

<body class="font-sans antialiased">

    {{-- 1. ETIQUETA DE VIDEO --}}
    <video autoplay muted loop id="fondo-video">
        <source src="{{ asset('videos/fondo.mp4') }}" type="video/mp4">
        Tu navegador no soporta el fondo de video.
    </video>
    {{-- FIN ETIQUETA DE VIDEO --}}

    {{-- 2. CONTENEDOR PRINCIPAL: CENTRADO y RESPONSIVO --}}
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-6">
        
        <h1 class="institution-name">
            SISTEMA "FERIA POPULAR"
        </h1>

        {{-- 3. CONTENEDOR DEL FORMULARIO CON GLASSMORHPISM --}}
        {{-- w-full max-w-sm para móvil, sm:max-w-md para desktop --}}
        {{-- Eliminé 'border border-white/20' ya que no te gustaba el borde blanco --}}
        <div class="w-full max-w-sm sm:max-w-md mt-6 p-8 bg-black/30 backdrop-blur-md shadow-2xl overflow-hidden rounded-3xl fade-in-form rotating-border">
            
            {{-- TÍTULO DE LOGIN --}}
            <h1 class="text-4xl font-bold text-white text-center mb-8">Inicio de Sesión</h1>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Campo Email --}}
                <div class="mb-6 relative">
                    {{-- 🟢 ICONO EMAIL 🟢 --}}
                    <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-white/70"></i>
                    <x-text-input id="email" class="block w-full input-glass" 
                                    type="email" 
                                    name="email" 
                                    :value="old('email')" 
                                    required autofocus 
                                    autocomplete="username" 
                                    placeholder="Email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- Campo Password (CON BOTÓN DE MOSTRAR/OCULTAR) --}}
                <div class="mb-6 relative">
                    {{-- 🟢 ICONO PASSWORD 🟢 --}}
                    <i class="fa-solid fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-white/70"></i>
                    
                    <x-text-input id="password" class="block w-full input-glass"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password"
                                    placeholder="Password" />
                    
                    {{-- 🟢 BOTÓN DE MOSTRAR/OCULTAR CONTRASEÑA 🟢 --}}
                    <button type="button" id="togglePassword" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-white/70 cursor-pointer p-1">
                        <i class="fa-solid fa-eye-slash"></i>
                    </button>
                    
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- Botón de Login --}}
                <x-primary-button class="w-full justify-center py-3 bg-red-600 hover:bg-indigo-700 focus:bg-red-700 active:bg-red-900 border border-transparent rounded-full font-semibold text-white uppercase tracking-widest transition ease-in-out duration-150">
                    {{ __('Iniciar Sesión') }}
                </x-primary-button>
            </form>
        </div>
    </div>

{{-- 4. CÓDIGO JAVASCRIPT: Debe ir al final del body para funcionar correctamente --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password');
        const toggleButton = document.getElementById('togglePassword');
        
        if (passwordInput && toggleButton) {
            toggleButton.addEventListener('click', function() {
                const toggleIcon = this.querySelector('i');
                
                // Determina el nuevo tipo de input
                const isPassword = passwordInput.getAttribute('type') === 'password';
                const newType = isPassword ? 'text' : 'password';
                
                // Cambia el tipo
                passwordInput.setAttribute('type', newType);
                
                // Cambia el icono (ojo tachado <-> ojo abierto)
                if (isPassword) {
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                } else {
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                }
            });
        }
    });
</script>

</body>
</html>