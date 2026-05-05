@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        ✨ {{ __('Registro de Nuevo Directivo') }} 
    </h2>
@endsection

@section('content')
<div class="py-4">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    Formulario de Registro de Directivo 🧑‍💼
                </h1>
                <a href="{{ route('directivos.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Volver
                </a>
            </div>
            
            {{-- Manejo de Errores de Validación --}}
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <p class="font-bold">¡Atención!</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('directivos.store') }}" method="POST">
                @csrf

                {{-- SECCIÓN 1: DATOS DEL CARGO --}}
                <div class="mb-8 border-b pb-4">
                    <h2 class="text-xl font-semibold text-indigo-700 mb-4">1. Datos del Cargo de la Directiva</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- GESTIÓN --}}
                        <div>
                            <label for="id_gestion" class="block font-medium text-sm text-gray-700">Gestión de la Directiva <span class="text-red-500">*</span></label>
                            <select name="id_gestion" id="id_gestion" required 
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Seleccione la Gestión</option>
                                @foreach ($gestiones as $gestion)
                                    <option value="{{ $gestion->id_gestion }}" {{ old('id_gestion') == $gestion->id_gestion ? 'selected' : '' }}>
                                        {{ $gestion->nombre_gestion }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_gestion') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- CARGO DESEMPEÑADO --}}
                        <div>
                            <label for="cargo_directivo" class="block font-medium text-sm text-gray-700">Cargo Desempeñado <span class="text-red-500">*</span></label>
                            <input type="text" name="cargo_directivo" id="cargo_directivo" value="{{ old('cargo_directivo') }}" required  onkeypress="return !(event.charCode >= 48 && event.charCode <= 57)"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @error('cargo_directivo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        {{-- FECHA DE POSESIÓN --}}
                        <div>
                            <label for="fecha_posesion" class="block font-medium text-sm text-gray-700">Fecha de Posesión <span class="text-red-500">*</span></label>
                            <input type="date" name="fecha_posesion" id="fecha_posesion" value="{{ old('fecha_posesion') }}" required
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @error('fecha_posesion') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        {{-- FECHA DE CONCLUSIÓN --}}
                        <div>
                            <label for="fecha_conclusion" class="block font-medium text-sm text-gray-700">Fecha de Conclusión (Opcional)</label>
                            <input type="date" name="fecha_conclusion" id="fecha_conclusion" value="{{ old('fecha_conclusion') }}"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @error('fecha_conclusion') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- 🔥 SECCIÓN 2: PERSONA QUE DESEMPEÑA EL CARGO (CON SELECT SEARCHABLE) 🔥 --}}
                <div class="mb-8 border-b pb-4">
                    <h2 class="text-xl font-semibold text-indigo-700 mb-4">
                        2. Persona que Desempeña el Cargo 🧑‍🤝‍🧑
                    </h2>

                    <div class="grid grid-cols-1 gap-6">
                        
                        {{-- CAMPO PRINCIPAL: AFILIADO ASOCIADO (Ahora usa Select2) --}}
                        <div>
                            <label for="id_afiliado" class="block font-medium text-sm text-gray-700">
                                Afiliado Asociado (Busque por CI/Nombre) <span class="text-red-500">*</span>
                            </label>
                            {{-- IMPORTANTE: El select original debe quedar así para que Select2 lo reemplace --}}
                            <select name="id_afiliado" id="id_afiliado" required style="width: 100%;"
                                class="mt-1 block border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Seleccione el Afiliado</option>
                                @foreach ($afiliados as $afiliado)
                                    <option value="{{ $afiliado->id_afiliado }}" {{ old('id_afiliado') == $afiliado->id_afiliado ? 'selected' : '' }}>
                                        {{ $afiliado->ci }} - {{ $afiliado->nombre_afiliado }} {{ $afiliado->apellido_paterno }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                *La información de nombre y CI se tomará directamente del registro del afiliado.
                            </p>
                            @error('id_afiliado') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- OBSERVACIONES (MANTENER) --}}
                    <div class="mt-6">
                        <label for="observaciones" class="block font-medium text-sm text-gray-700">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="3"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('observaciones') }}</textarea>
                        @error('observaciones') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                
                {{-- BOTONES DE FORMULARIO --}}
                <div class="flex items-center justify-end mt-4 space-x-3">
                    <a href="{{ route('directivos.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Cancelar
                    </a>
                    
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        💾 Registrar Directivo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- 
    USO DE RECURSOS LOCALES:
    Se ha modificado el código para utilizar la ruta específica proporcionada por el usuario: 'assets/select2/'.
    
    Asegúrate de que tus archivos Select2.min.css y select2.min.js
    estén disponibles en las rutas 'public/assets/select2/select2.min.css' y 'public/assets/select2/select2.min.js'
    (ajusta los nombres de archivo si son diferentes).
    
    También asumimos que jQuery ($) ya está cargado en 'app.js' o en tu layout principal.
--}}
@push('styles')
    {{-- Ruta corregida para el CSS de Select2 --}}
    <link href="{{ asset('assets/select2/select2.min.css') }}" rel="stylesheet" />
    
    {{-- Mantenemos el estilo personalizado para integración con Tailwind --}}
    <style>
        .select2-container--default .select2-selection--single {
            border: 1px solid rgb(209 213 219); /* border-gray-300 */
            height: 42px; /* Altura similar a los inputs de Laravel Breeze/Jetstream */
            border-radius: 0.375rem; /* rounded-md */
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 42px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
    </style>
@endpush

@push('scripts')
    {{-- Requerimiento: Select2 JS, usando el helper asset() con la ruta corregida --}}
    <script src="{{ asset('assets/select2/select2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Inicializa Select2 en el select de Afiliados
            $('#id_afiliado').select2({
                placeholder: "Busque por CI o Nombre del Afiliado...",
                allowClear: false, // Ya que es un campo obligatorio
                
                // === MODIFICACIÓN CLAVE AQUÍ: Personalizar mensajes ===
                language: {
                    // Esta función se llama cuando Select2 no encuentra coincidencias.
                    noResults: function () {
                        // Aquí está tu mensaje personalizado
                        return "No hay afiliados registrados aún";
                    }
                }
                // =======================================================
            });
        });
    </script>
@endpush