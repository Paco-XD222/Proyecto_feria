@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight" >
        📋 {{ __('Listado de Afiliados y Kárdex') }} 🤝
    </h2>
@endsection

@section('content')

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- INICIO: MENSAJES DE SESIÓN (ÉXITO/ERROR) --}}
@if (session('success'))
    <div class="mb-4 p-4 border border-green-400 bg-green-100 text-green-700 rounded-lg shadow">
        <strong>🎉 ¡Éxito!</strong> {!! session('success') !!}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 p-4 border border-red-400 bg-red-100 text-red-700 rounded-lg shadow">
        <strong>⚠️ Error:</strong> {!! session('error') !!}
    </div>
@endif
{{-- FIN: MENSAJES DE SESIÓN --}}

                {{-- BOTÓN PARA CREAR NUEVO AFILIADO (CON CONTROL DE ROLES) --}}
              <div class="mb-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0 md:space-x-4">
        
        <form action="{{ route('afiliados.index') }}" method="GET" class="flex-1 w-full flex items-center space-x-2">
            <input type="text" 
                   name="search" 
                   placeholder="Buscar por CI, Nombre o Apellido..." 
                   value="{{ request('search') }}"
                   class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                   
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 transition duration-150">
                🔍 Buscar
            </button>

            @if (request('search'))
                <a href="{{ route('afiliados.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-md hover:bg-gray-100 transition duration-150">
                    Limpiar
                </a>
            @endif
        </form>

        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
            <a href="{{ route('afiliados.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 w-full md:w-auto justify-center">
                ➕ {{ __('Registrar Nuevo Afiliado') }}
            </a>
        @endif
        
    </div>
</div>

                @if ($afiliados->isEmpty())
                    <div class="p-6 text-center text-gray-500 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                        <p class="text-lg">No hay afiliados registrados. 😔</p>
                        <p class="text-sm mt-2">Utiliza el botón superior para crear un nuevo registro de Kárdex.</p>
                    </div>
                @else
                    <div class="overflow-x-auto shadow-md sm:rounded-lg">
                        {{-- INICIO: TABLA DE AFILIADOS --}}
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-indigo-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Afiliado / C.I.
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                                        Puesto / Mercadería
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
                                        Gestión
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Foto
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Firma
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($afiliados as $afiliado)
                                    <tr class="hover:bg-gray-50">
                                        {{-- ID --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                            {{ $afiliado->id_afiliado }}
                                        </td>

                                        {{-- NOMBRE Y C.I. --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="font-semibold">{{ $afiliado->nombre_afiliado }} {{ $afiliado->apellido_paterno }} {{ $afiliado->apellido_materno }}</div>
                                            <div class="text-xs text-gray-500">CI: **{{ $afiliado->ci }}**</div>
                                        </td>

                                        {{-- PUESTO / MERCADERÍA --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                            <span class="font-semibold">Kdx: {{ $afiliado->nro_kardex ?? $afiliado->puesto->nro_kardex ?? 'N/A' }}</span> - Fila: {{ $afiliado->fila ?? $afiliado->puesto->fila ?? 'N/A' }}
                                            <div class="text-xs text-indigo-400">{{ $afiliado->mercaderia->clase_mercaderia ?? 'N/A' }}</div>
                                        </td>

                                        {{-- GESTIÓN --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                            {{ $afiliado->gestion->nombre_gestion ?? 'N/A' }}
                                            <div class="text-xs text-gray-400">Afiliado: {{ $afiliado->fecha_afiliacion }}</div>
                                        </td>
                                        
                                        {{-- CELDA FOTO --}}
                                        <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
                                            @if ($afiliado->foto) 
                                                <img src="{{ asset('storage/' . $afiliado->foto) }}" alt="Foto" class="w-12 h-12 object-cover mx-auto rounded-full border border-gray-300">
                                            @else
                                                <span class="text-xs text-red-500">Sin Foto</span>
                                            @endif
                                        </td>
                                        
                                        {{-- CELDA FIRMA --}}
                                        <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-500">
                                            @if ($afiliado->firma) 
                                                <img src="{{ asset('storage/' . $afiliado->firma) }}" alt="Firma" class="w-12 h-12 object-contain mx-auto">
                                            @else
                                                <span class="text-xs text-red-500">Sin Firma</span>
                                            @endif
                                        </td>

                                        {{-- ACCIONES (MODIFICADA) --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            
                                            {{-- VER KÁRDEX --}}
                                            <a href="{{ route('afiliados.show', $afiliado->id_afiliado) }}" class="text-blue-600 hover:text-blue-900 mr-2 inline-flex items-center" title="Ver Kárdex">
                                                <span class="mr-1">👁️</span> Ver
                                            </a>

                                            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                                {{-- EDITAR --}}
                                                <a href="{{ route('afiliados.edit', $afiliado) }}" class="text-yellow-600 hover:text-yellow-900 mr-2 inline-flex items-center" title="Editar">
                                                    <span class="mr-1">✏️</span> Editar
                                                </a>
                                                
                                                {{-- ELIMINAR (CAMBIO AQUÍ) --}}
                                                <form method="POST" action="{{ route('afiliados.destroy', $afiliado) }}" class="inline-block" onsubmit="return confirm('⚠️ ¿Está seguro de que desea ELIMINAR a este afiliado? Esta acción es irreversible.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 inline-flex items-center" title="Eliminar">
                                                        <span class="mr-1">🗑️</span> **Eliminar**
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>  
                                @endforeach
                            </tbody>
                        </table>
                        {{-- FIN: TABLA DE AFILIADOS --}}
                        <div class="mt-6">
    {{ $afiliados->appends(['search' => $search])->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection