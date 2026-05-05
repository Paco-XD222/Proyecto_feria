@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        🏛️ {{ __('Lista de Directivos') }} 👥
    </h2>
@endsection

@section('content')

<div class="py-4">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

            {{-- INICIO: MENSAJE DE SESIÓN (ÉXITO/ERROR) --}}
            @if (session('success'))
                <div class="mb-4 p-4 border border-green-400 bg-green-100 text-green-700 rounded">
                    <strong>🎉 ¡Éxito!</strong> {{ session('success') }}
                </div>
            @endif
            {{-- FIN: MENSAJE DE SESIÓN --}}

            {{-- BOTÓN PARA CREAR NUEVO DIRECTIVO (CON CONTROL DE ROLES) --}}
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-xl font-bold text-gray-800">
                    Directiva Registrada
                </h1>
                {{-- Asumiendo que Auth::user() existe y tiene la propiedad role_id --}}
                @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                    <a href="{{ route('directivos.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        ➕ {{ __('Registrar Nuevo Directivo') }}
                    </a>
                @endif
            </div>

            @if ($directivos->isEmpty())
                <div class="p-6 text-center text-gray-500 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                    <p class="text-lg">No hay directivos registrados actualmente. 😔</p>
                    <p class="text-sm mt-2">Utiliza el botón superior para crear un nuevo registro.</p>
                </div>
            @else
                <div class="overflow-x-auto shadow-md sm:rounded-lg">
                    {{-- INICIO: TABLA DE DIRECTIVOS --}}
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cargo
                                </th>
                                {{-- COLUMNA ACTUALIZADA PARA MOSTRAR NOMBRE COMPLETO DESDE AFILIADO --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
                                    Directivo (Nombre)
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                                    Afiliado / CI
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                    Gestión
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Posesión
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($directivos as $directivo)
                                <tr class="hover:bg-gray-50">
                                    {{-- ID --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                        {{ $directivo->id_directivo }}
                                    </td>

                                    {{-- CARGO --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        {{ $directivo->cargo_directivo }}
                                    </td>

                                    {{-- NOMBRE DIRECTIVO (Extraído del Afiliado) --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 hidden lg:table-cell">
                                        @if ($directivo->afiliado)
                                            {{ $directivo->afiliado->nombre_afiliado }} 
                                            {{ $directivo->afiliado->apellido_paterno }} 
                                            {{ $directivo->afiliado->apellido_materno }}
                                        @else
                                            <span class="text-red-500">Afiliado Desvinculado</span>
                                        @endif
                                    </td>

                                    {{-- AFILIADO / CI --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                        @if ($directivo->afiliado)
                                            <div class="font-medium text-gray-900">{{ $directivo->afiliado->ci }}</div>
                                            <div class="text-xs text-gray-500">{{ $directivo->afiliado->nombre_afiliado }}</div>
                                        @else
                                            N/A
                                        @endif
                                    </td>

                                    {{-- GESTIÓN --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-purple-600 font-medium hidden sm:table-cell">
                                        {{ $directivo->gestion->nombre_gestion ?? 'No Definida' }}
                                    </td>
                                    
                                    {{-- POSESIÓN --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $directivo->fecha_posesion }}
                                    </td>
                                    
                                    {{-- ACCIONES --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                            {{-- EDITAR --}}
                                            <a href="{{ route('directivos.edit', $directivo) }}" class="text-yellow-600 hover:text-yellow-900 mr-2 inline-flex items-center" title="Editar">
                                                <span class="mr-1">✏️</span> Editar
                                            </a>
                                            
                                            {{-- ELIMINAR --}}
                                            <form method="POST" action="{{ route('directivos.destroy', $directivo) }}" class="inline-block" onsubmit="return confirm('⚠️ ¿Está seguro de que desea ELIMINAR este registro de directivo?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 inline-flex items-center" title="Eliminar">
                                                    <span class="mr-1">🗑️</span> Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{-- FIN: TABLA DE DIRECTIVOS --}}
                </div>
                
                {{-- INICIO: PAGINACIÓN --}}
                <div class="mt-4">
                    {{ $directivos->links() }}
                </div>
                {{-- FIN: PAGINACIÓN --}}
            @endif
        </div>
    </div>
</div>
@endsection