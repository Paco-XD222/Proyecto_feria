@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        📚 {{ __('Gestión de Clases de Mercadería') }}
    </h2>
@endsection

@section('content')

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                {{-- INICIO: MENSAJES DE SESIÓN --}}
                @if (session('success'))
                    <div class="mb-4 p-4 border border-green-400 bg-green-100 text-green-700 rounded">
                        <strong>🎉 ¡Éxito!</strong> {{ session('success') }}
                    </div>
                @endif
               @if (session('error'))
<div class="mb-4 p-4 border border-red-400 bg-red-100 text-red-700 rounded" role="alert">
    <span class="block sm:inline">{!! session('error') !!}</span></div>
@endif
                {{-- FIN: MENSAJES DE SESIÓN --}}

                {{-- BOTÓN PARA CREAR NUEVA MERCADERÍA --}}
                <div class="mb-6 flex justify-end">
                    <a href="{{ route('mercaderia.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 transition ease-in-out duration-150">
                        ➕ {{ __('Nueva Clase de Mercadería') }}
                    </a>
                </div>

                @if ($mercaderias->isEmpty())
                    <div class="p-6 text-center text-gray-500 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                        <p class="text-lg">No hay clases de mercadería registradas. 😔</p>
                    </div>
                @else
                    <div class="overflow-x-auto shadow-md sm:rounded-lg">
                        {{-- INICIO: TABLA DE MERCADERÍA --}}
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-indigo-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre de la Clase
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($mercaderias as $item)
                                    <tr class="hover:bg-gray-50">
                                        {{-- ID --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                            {{ $item->id_mercaderia }}
                                        </td>

                                        {{-- NOMBRE --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                            {{ $item->clase_mercaderia }}
                                        </td>

                                        {{-- ACCIONES --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            
                                            {{-- EDITAR --}}
                                            <a href="{{ route('mercaderia.edit', $item->id_mercaderia) }}" class="text-yellow-600 hover:text-yellow-900 mr-2 inline-flex items-center" title="Editar">
                                                ✏️ Editar
                                            </a>
                                            
                                            {{-- ELIMINAR --}}
                                            <form method="POST" action="{{ route('mercaderia.destroy', $item->id_mercaderia) }}" class="inline-block" onsubmit="return confirm('⚠️ ¿Está seguro de ELIMINAR la clase {{ $item->clase_mercaderia }}? Esto puede afectar a los afiliados.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 inline-flex items-center" title="Eliminar">
                                                    🗑️ Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr> 
                                @endforeach
                            </tbody>
                        </table>
                        {{-- FIN: TABLA DE MERCADERÍA --}}
                        
                        {{-- PAGINACIÓN --}}
                        <div class="mt-6 p-4">
                            {{ $mercaderias->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection