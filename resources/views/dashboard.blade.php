@extends('layouts.app')

@section('header')
    {{-- Título principal (Responsivo y sin logo) --}}
    <h1 class="text-2xl sm:text-4xl font-extrabold text-black leading-tight" 
        style="text-shadow: 2px 2px 5px rgba(34, 33, 33, 0.8);">
        🎉 ¡BIENVENIDO AL SISTEMA DE FERIA POPULAR!
    </h1>
@endsection

@section('content')
    {{-- Aquí se inyecta el contenido principal fuera de los slots --}}
    <div class="pt-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 🟢 PRIMER BLOQUE: MENSAJE PRINCIPAL 🟢 --}}
            {{-- Clases: bg-white/70 (Más transparente) + backdrop-blur-md (Desenfoque claro) --}}
            <div class="bg-white/70 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg mb-6 p-6 border-l-4 border-indigo-500 overflow-hidden">
                
                <div class="flex items-start space-x-4"> 
                    <img src="{{ asset('img/escudof.png') }}" alt="Logo del Sistema" class="h-10 w-auto flex-shrink-0 mt-1"> 
                    
                    <div>
                        <h3 class="font-semibold text-xl text-gray-800 leading-tight mb-2">Página Principal</h3>
                        <p class="text-gray-600">
                            Utiliza el menú superior para acceder a la gestión de datos o a tu información personal, según tu perfil de usuario.
                        </p>
                    </div>
                </div>

            </div>
            
            {{-- 🟢 SEGUNDO BLOQUE: MENSAJE DE LOGUEO 🟢 --}}
            {{-- Clases: bg-white/70 (Más transparente) + backdrop-blur-md (Desenfoque claro) --}}
            <div class="bg-white/70 backdrop-blur-md overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Estás logueado!") }}
                </div>
            </div>
        </div>
    </div>
@endsection