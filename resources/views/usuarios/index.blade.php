@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        👤 {{ __('Gestión de Usuarios del Sistema') }} 
    </h2>
@endsection

@section('content')

<div class="py-4">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

        {{-- INICIO: MENSAJE DE SESIÓN (ÉXITO) --}}
            @if (session('success'))
                <div class="mb-4 p-4 border border-green-400 bg-green-100 text-green-700 rounded">
                    <strong>🎉 ¡Éxito!</strong> {{ session('success') }}
                </div>
            @endif
            {{-- FIN: MENSAJE DE SESIÓN (ÉXITO) --}}
            
            {{-- INICIO: MENSAJE DE SESIÓN (ERROR) <-- AGREGAR ESTO --}}
            @if (session('error'))
                <div class="mb-4 p-4 border border-red-400 bg-red-100 text-red-700 rounded" role="alert">
                    <span class="block sm:inline">{!! session('error') !!}</span>
                </div>
            @endif
            {{-- FIN: MENSAJE DE SESIÓN (ERROR) --}}

            {{-- ENCABEZADO Y BOTÓN DE CREAR --}}
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-xl font-bold text-gray-800">
                    Usuarios Registrados
                </h1>
                <a href="{{ route('usuarios.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    ➕ {{ __('Registrar Nuevo Usuario') }}
                </a>
            </div>

            @if ($users->isEmpty())
                <div class="p-6 text-center text-gray-500 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                    <p class="text-lg">No hay usuarios registrados en el sistema. 😔</p>
                    <p class="text-sm mt-2">Utiliza el botón superior para crear el primer usuario.</p>
                </div>
            @else
                <div class="overflow-x-auto shadow-md sm:rounded-lg">
                    {{-- INICIO: TABLA DE USUARIOS --}}
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nombre
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rol Asignado
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50">
                                    {{-- ID --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                        {{ $user->id }}
                                    </td>

                                    {{-- NOMBRE --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        {{ $user->name }}
                                    </td>

                                    {{-- EMAIL --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                        {{ $user->email }}
                                    </td>

                                    {{-- ROL --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($user->role)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if ($user->role->id == 1) bg-green-100 text-green-800
                                                @elseif ($user->role->id == 2) bg-blue-100 text-blue-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $user->role->nombre }}
                                            </span>
                                        @else
                                            <span class="text-xs text-red-500">Sin Rol Asignado</span>
                                        @endif
                                    </td>
                                    
                                    {{-- ACCIONES --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        
                                        @if ($user->role_id != 3)
                                            {{-- EDITAR --}}
                                            <a href="{{ route('usuarios.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-900 inline-flex items-center" title="Editar Usuario">
                                                <span class="mr-1">✏️</span> Editar
                                            </a>
                                            
                                            {{-- ELIMINAR --}}
                                            <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('⚠️ ¿Está seguro de que desea ELIMINAR al usuario {{ $user->name }}? Esta acción es irreversible.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 inline-flex items-center" title="Eliminar Usuario">
                                                    <span class="mr-1">🗑️</span> Eliminar
                                                </button>
                                            </form>
                                        @else
                                            {{-- Mensaje para usuarios de Rol 3 (Afiliado) --}}
                                            <span class="text-xs text-gray-400 italic">
                                                Gestionado en Kárdex
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{-- FIN: TABLA DE USUARIOS --}}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection