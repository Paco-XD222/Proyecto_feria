@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Crear Nuevo Período de Gestión') }}
    </h2>
@endsection

@section('content')

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- INICIO: ERRORES DE VALIDACIÓN --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul class="list-disc ml-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    {{-- FIN: ERRORES DE VALIDACIÓN --}}

                    <form method="POST" action="{{ route('gestion.store') }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="nombre_gestion" :value="__('Nombre del Período de Gestión')" />
                            <x-text-input 
                                id="nombre_gestion" 
                                class="block mt-1 w-full" 
                                type="text" 
                                name="nombre_gestion" 
                                :value="old('nombre_gestion') ?? 'Directiva '" 
                                required autofocus 
                            />
                            <x-input-error :messages="$errors->get('nombre_gestion')" class="mt-2" />
                            <small class="text-gray-500">Ejemplo: Directiva 2025-2026, Gestión I-2025</small>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="fecha_inicio" :value="__('Fecha de Inicio')" />
                            <x-text-input 
                                id="fecha_inicio" 
                                class="block mt-1 w-full" 
                                type="date" 
                                name="fecha_inicio" 
                                :value="old('fecha_inicio')" 
                                required 
                            />
                            <x-input-error :messages="$errors->get('fecha_inicio')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="fecha_fin" :value="__('Fecha de Finalización')" />
                            <x-text-input 
                                id="fecha_fin" 
                                class="block mt-1 w-full" 
                                type="date" 
                                name="fecha_fin" 
                                :value="old('fecha_fin')" 
                                required 
                            />
                            <x-input-error :messages="$errors->get('fecha_fin')" class="mt-2" />
                        </div>
                        
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('gestion.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>
                                {{ __('Guardar Período') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection