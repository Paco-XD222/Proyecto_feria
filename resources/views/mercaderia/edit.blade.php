@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        ✏️ {{ __('Editar Clase de Mercadería') }}: {{ $mercaderium->clase_mercaderia }}
    </h2>
@endsection

@section('content')

    <div class="py-4">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- ENLACE PARA VOLVER --}}
                    <div class="mb-4">
                        <a href="{{ route('mercaderia.index') }}" class="text-indigo-600 hover:text-indigo-900 font-semibold flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Volver al Listado
                        </a>
                    </div>
                    
                    {{-- INICIO DEL FORMULARIO --}}
                    {{-- Se apunta a la ruta 'update' y se pasa el modelo $mercaderium --}}
                    <form action="{{ route('mercaderia.update', $mercaderium->id_mercaderia) }}" method="POST" class="space-y-6">
                        @csrf
                        {{-- MÉTODO PUT ES NECESARIO PARA LA RUTA DE ACTUALIZACIÓN --}}
                        @method('PUT') 
                        
                        {{-- SECCIÓN PRINCIPAL DE DATOS --}}
                        <div>
                            <label for="clase_mercaderia" class="block text-sm font-medium text-gray-700 required">
                                Nombre de la Clase de Mercadería
                            </label>
                            <input type="text" 
                                   name="clase_mercaderia" 
                                   id="clase_mercaderia" 
                                   {{-- El valor actual del campo o el valor anterior si falló la validación --}}
                                   value="{{ old('clase_mercaderia', $mercaderium->clase_mercaderia) }}"
                                   required
                                   maxlength="255"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('clase_mercaderia') border-red-500 @enderror">
                            
                            {{-- MANEJO DE ERRORES DE VALIDACIÓN --}}
                            @error('clase_mercaderia')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- BOTONES DE ACCIÓN --}}
                        <div class="flex justify-end space-x-3 mt-6">
                            
                            <a href="{{ route('mercaderia.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancelar
                            </a>

                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                Actualizar Clase
                            </button>
                        </div>
                    </form>
                    {{-- FIN DEL FORMULARIO --}}

                </div>
            </div>
        </div>
    </div>
@endsection