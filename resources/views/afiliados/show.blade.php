@extends('layouts.app') 

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        📁 {{ __('Kárdex Detallado del Afiliado') }}
    </h2>
@endsection

@section('content')
<div class="py-4">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

            {{-- ENCABEZADO Y VOLVER --}}
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 border-b pb-4">
                <h2 class="text-2xl font-bold text-gray-800 mb-2 md:mb-0">
                    Kárdex de: {{ $afiliado->nombre_afiliado }} {{ $afiliado->apellido_paterno }}
                </h2>
                <a href="{{ route('afiliados.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Volver al Listado
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

                {{-- COLUMNA DE ARCHIVOS (4/12) --}}
                <div class="md:col-span-4">
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-5 shadow-lg">
                        <h4 class="text-lg font-semibold text-indigo-700 mb-4 border-b border-indigo-300 pb-2">🖼️ Archivos Adjuntos</h4>
                        
                        {{-- FOTO --}}
                        <div class="mb-5 text-center">
                            <p class="font-medium text-gray-700 mb-2">Foto Afiliado:</p>
                            @if ($afiliado->foto)
                                <img src="{{ asset('storage/' . $afiliado->foto) }}" alt="Foto Afiliado" class="max-w-xs mx-auto w-40 h-40 object-cover rounded-full border-4 border-white shadow-md">
                            @else
                                <div class="text-sm text-red-500 p-3 bg-white rounded-lg border">N/A - Sin Foto</div>
                            @endif
                        </div>
                        
                        {{-- FIRMA --}}
                        <div class="text-center">
                            <p class="font-medium text-gray-700 mb-2">Firma:</p>
                            @if ($afiliado->firma)
                                <img src="{{ asset('storage/' . $afiliado->firma) }}" alt="Firma Afiliado" class="max-w-xs mx-auto w-40 h-20 object-contain border border-gray-300 bg-white p-1">
                            @else
                                <div class="text-sm text-red-500 p-3 bg-white rounded-lg border">N/A - Sin Firma</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DE DATOS (8/12) --}}
                <div class="md:col-span-8 space-y-6">

                    {{-- 1. INFORMACIÓN PERSONAL Y ACCESO --}}
                    <div class="card bg-gray-50 p-5 rounded-lg border border-gray-200 shadow-md">
                        <h4 class="text-xl font-bold text-gray-700 mb-4 border-b pb-2">1. Información Personal y Contacto</h4>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div class="flex flex-col">
                                <dt class="font-medium text-gray-500">C.I.</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->ci }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="font-medium text-gray-500">Nombre Completo</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->nombre_afiliado }} {{ $afiliado->apellido_paterno }} {{ $afiliado->apellido_materno }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="font-medium text-gray-500">Fecha Nacimiento</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->fecha_nacimiento }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="font-medium text-gray-500">Teléfono</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->telefono }}</dd>
                            </div>
                            <div class="flex flex-col sm:col-span-2">
                                <dt class="font-medium text-gray-500">Dirección</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->direccion }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="font-medium text-gray-500">Estado Civil</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->estado_civil }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="font-medium text-gray-500">Cónyuge</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->nombre_conyuge ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="font-medium text-gray-500">Nro. Familiares</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->numero_familia }}</dd>
                            </div>
                             <div class="flex flex-col col-span-2">
                            <dt class="font-medium text-gray-500">Dirección</dt>
                            <dd class=" font-semibold text-gray-900">{{ $afiliado->direccion }}</dd>
                        </div>
                            <div class="flex flex-col">
                                <dt class="font-medium text-gray-500">Email de Usuario</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->usuario->email ?? 'Usuario no encontrado' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- 2. DATOS DE PUESTO Y ACTIVIDAD --}}
                    <div class="card bg-gray-50 p-5 rounded-lg border border-gray-200 shadow-md">
                        <h4 class="text-xl font-bold text-gray-700 mb-4 border-b pb-2">2. Datos de Puesto y Actividad</h4>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div class="flex flex-col">
                                <dt class="font-medium text-gray-500">Nro. Kárdex</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->puesto->nro_kardex ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="font-medium text-gray-500">Nro. Libro</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->puesto->nro_libro ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex flex-col sm:col-span-2">
                                <dt class="font-medium text-gray-500">Ubicación de Venta</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->puesto->ubicacion_venta ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="font-medium text-gray-500">Fila</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->puesto->fila ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="font-medium text-gray-500">Medida (m)</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->puesto->medida_puesto ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex flex-col sm:col-span-2">
                                <dt class="font-medium text-gray-500">Mercadería/Clase</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->mercaderia?->clase_mercaderia ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- 3. DATOS HISTÓRICOS Y DE GESTIÓN --}}
                    <div class="card bg-gray-50 p-5 rounded-lg border border-gray-200 shadow-md">
                        <h4 class="text-xl font-bold text-gray-700 mb-4 border-b pb-2">3. Datos Históricos y de Gestión</h4>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div class="flex flex-col">
                                <dt class="font-medium text-gray-500">Gestión Afiliación</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->gestion->nombre_gestion ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="font-medium text-gray-500">Fecha Afiliación</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->fecha_afiliacion }}</dd>
                            </div>
                            <div class="flex flex-col sm:col-span-2">
                                <dt class="font-medium text-gray-500">Cargo Histórico</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->cargo_alguna_vez ?? 'Ninguno' }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="font-medium text-gray-500">Recarnetización</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->recarnetizacion ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex flex-col sm:col-span-2">
                                <dt class="font-medium text-gray-500">Observaciones</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->observaciones ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex flex-col sm:col-span-2">
                                <dt class="font-medium text-gray-500">Otros Datos</dt>
                                <dd class="font-semibold text-gray-900">{{ $afiliado->otros ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- BOTONES DE ACCIÓN --}}
                    <div class="mt-6 flex justify-end space-x-3">
    
                    {{-- BOTÓN EDITAR: Solo visible para Administrador (1) y Directivo (2) --}}
                    @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                     <a href="{{ route('afiliados.edit', $afiliado) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition ease-in-out duration-150">
                     <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-9-4l4-4m-7 7l4-4m-4 4l7-7"></path></svg>
                     Editar Kárdex
                    </a>
                 @endif
    
                {{-- BOTÓN IMPRIMIR: Visible para Administrador (1), Directivo (2) y Afiliado (3) --}}
                @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 3)
                <a href="{{ route('afiliados.print', $afiliado->id_afiliado) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                 🖨️ Imprimir Kárdex
                 </a>
              @endif

            </div>
                </div> {{-- FIN md:col-span-8 --}}
            </div> {{-- FIN grid --}}
        </div>
        
    </div>
</div>
@endsection
