@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
        {{ __('Editar Afiliado') }}
        <span class="ml-3 text-lg font-normal text-indigo-600">
            (ID: {{ $afiliado->id_afiliado }})
        </span>
    </h2>
@endsection

@section('content')

    <div class="py-6"> 
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-10">
            
            {{-- INICIO: MUESTRA ERRORES DE VALIDACIÓN (ESTILIZADO) --}}
            @if ($errors->any())
                <div class="mb-6 p-4 border border-red-500 bg-red-50 text-red-800 rounded-lg transition duration-150 ease-in-out" role="alert">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <strong class="font-bold">¡Error de Validación!</strong>
                    </div>
                    <ul class="mt-2 list-disc list-inside ml-2 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{-- FIN: MUESTRA ERRORES DE VALIDACIÓN --}}
            
            <form action="{{ route('afiliados.update', $afiliado->id_afiliado) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf 
                @method('PUT') 

                {{-- Campos Ocultos Requeridos por el Modelo/Controlador --}}
                <input type="hidden" name="id_puesto" value="{{ old('id_puesto', $afiliado->id_puesto ?? '') }}">
                <input type="hidden" name="id_usuario" value="{{ $afiliado->id_usuario ?? '' }}">

                <div class="border-b border-gray-200 pb-8">
                    <h2 class="text-2xl font-bold text-indigo-700 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                        1. Datos Personales & Acceso
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        {{-- CAMPO: EMAIL --}}
                        <div class="col-span-1 md:col-span-2">
                            <label class="block font-medium text-sm text-gray-700" for="email">📧 Correo Electrónico (Usuario de Acceso): <span class="text-red-500">*</span></label>
                            <div class="relative mt-1">
                                <input 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full pr-10 @error('email') border-red-500 @enderror" 
                                    type="email" 
                                    name="email" 
                                    id="email" 
                                    value="{{ old('email', $afiliado->usuario->email ?? '') }}" 
                                    required
                                />
                                <span class="absolute right-0 top-0 mt-3 mr-3 text-xs text-gray-500 italic hidden sm:block" title="Nombre de usuario para el sistema.">
                                    Usuario
                                </span>
                            </div>
                            @error('email') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- CAMPO: C.I. --}}
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="ci">🆔 C.I.: <span class="text-red-500">*</span></label>
                            <div class="flex items-center mt-1">
                                <input 
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-l-md shadow-sm block w-full @error('ci') border-red-500 @enderror"
                                    type="text" 
                                    name="ci" 
                                    id="ci" 
                                    value="{{ old('ci', $afiliado->ci) }}" 
                                    inputmode="numeric"     
                                    onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                                    required
                                />
                                <span class="p-2 bg-indigo-100 text-indigo-600 rounded-r-md cursor-help text-lg" title="La contraseña temporal se genera con 'AF' + C.I. si el campo de contraseña se deja vacío.">
                                    💡
                                </span>
                            </div>
                            @error('ci') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- CAMPO: NOMBRE --}}
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="nombre_afiliado">👤 Nombre: <span class="text-red-500">*</span></label>
                            <input 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('nombre_afiliado') border-red-500 @enderror"
                                type="text" 
                                required onkeypress="return /[A-Za-zÁÉÍÓÚáéíóúÑñ\s]/.test(event.key)"
                                name="nombre_afiliado" 
                                id="nombre_afiliado" 
                                value="{{ old('nombre_afiliado', $afiliado->nombre_afiliado) }}" 
                                onkeypress="return !(event.charCode >= 48 && event.charCode <= 57)"
                                required
                            />
                            @error('nombre_afiliado') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        {{-- CAMPO: APELLIDO PATERNO --}}
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="apellido_paterno">Apellido Paterno: <span class="text-red-500">*</span></label>
                            <input 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('apellido_paterno') border-red-500 @enderror"
                                type="text" 
                                name="apellido_paterno" 
                                id="apellido_paterno" 
                                 onkeypress="return !(event.charCode >= 48 && event.charCode <= 57)"
                                value="{{ old('apellido_paterno', $afiliado->apellido_paterno) }}" 
                                required
                            />
                            @error('apellido_paterno') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        {{-- CAMPO: APELLIDO MATERNO --}}
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="apellido_materno">Apellido Materno: <span class="text-red-500">*</span></label>
                            <input 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('apellido_materno') border-red-500 @enderror"
                                type="text" 
                                name="apellido_materno" 
                                id="apellido_materno" 
                                 onkeypress="return !(event.charCode >= 48 && event.charCode <= 57)"
                                value="{{ old('apellido_materno', $afiliado->apellido_materno) }}" 
                                required
                            />
                            @error('apellido_materno') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- CAMPO: FECHA DE NACIMIENTO --}}
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="fecha_nacimiento">🎂 Fecha de Nacimiento: <span class="text-red-500">*</span></label>
                            <input 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('fecha_nacimiento') border-red-500 @enderror" 
                                type="date" 
                                name="fecha_nacimiento" 
                                id="fecha_nacimiento" 
                                value="{{ old('fecha_nacimiento', $afiliado->fecha_nacimiento) }}" 
                                required
                            />
                            @error('fecha_nacimiento') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- CAMPO: TELÉFONO CELULAR --}}
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="telefono">📱 Teléfono Celular:</label>
                            <input 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('telefono') border-red-500 @enderror"
                                type="text" 
                                name="telefono" 
                                id="telefono" 
                                 inputmode="numeric"     
                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                                value="{{ old('telefono', $afiliado->telefono) }}"
                            />
                            @error('telefono') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div> 
                        
                        {{-- CAMPO: ESTADO CIVIL (SELECT) --}}
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="estado_civil">❤️ Estado Civil:</label>
                            <select 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('estado_civil') border-red-500 @enderror"
                                name="estado_civil" 
                                id="estado_civil"
                            >
                                @php $selectedEstado = old('estado_civil', $afiliado->estado_civil); @endphp
                                <option value="" class="text-gray-500">Seleccione</option>
                                <option value="Soltero" @selected($selectedEstado == 'Soltero')>Soltero(a)</option>
                                <option value="Casado" @selected($selectedEstado == 'Casado')>Casado(a)</option>
                                <option value="Viudo" @selected($selectedEstado == 'Viudo')>Viudo(a)</option>
                                <option value="Divorciado" @selected($selectedEstado == 'Divorciado')>Divorciado(a)</option>
                            </select>
                            @error('estado_civil') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- CAMPO: NRO. DE FAMILIARES --}}
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="numero_familia">👨‍👩‍👧‍👦 Nro. de Familiares:</label>
                            <input 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('numero_familia') border-red-500 @enderror"
                                type="number" 
                                name="numero_familia" 
                                id="numero_familia" 
                                value="{{ old('numero_familia', $afiliado->numero_familia) }}"
                                min="0"
                            />
                            @error('numero_familia') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- CAMPO: NOMBRE DEL CÓNYUGE (Ocupa 2 columnas para más espacio) --}}
                        <div class="col-span-1 md:col-span-2">
                            <label class="block font-medium text-sm text-gray-700" for="nombre_conyuge">💍 Nombre del Cónyuge (si aplica):</label>
                            <input 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('nombre_conyuge') border-red-500 @enderror"
                                type="text" 
                                name="nombre_conyuge" 
                                id="nombre_conyuge" 
                                onkeypress="return !(event.charCode >= 48 && event.charCode <= 57)"
                                value="{{ old('nombre_conyuge', $afiliado->nombre_conyuge) }}"
                                placeholder="Nombre completo del cónyuge"
                            />
                            @error('nombre_conyuge') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        {{-- CAMPO: DIRECCIÓN (Ocupa 3 columnas) --}}
                        <div class="lg:col-span-3">
                            <label class="block font-medium text-sm text-gray-700" for="direccion">🏠 Dirección: <span class="text-red-500">*</span></label>
                            <input 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('direccion') border-red-500 @enderror"
                                type="text" 
                                name="direccion" 
                                id="direccion" 
                                value="{{ old('direccion', $afiliado->direccion) }}" 
                                required
                                placeholder="Ej: Calle Principal N° 123, Zona Central"
                            />
                            @error('direccion') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                    </div>
                </div>

                <div class="border-b border-gray-200 pb-8 pt-4">
                    <h2 class="text-2xl font-bold text-indigo-700 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" /></svg>
                        2. Puesto de Venta & Actividad
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                        {{-- CAMPO: NRO. KARDEX --}}
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="nro_kardex">📋 Nro. Kardex: <span class="text-red-500">*</span></label>
                            <input 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('nro_kardex') border-red-500 @enderror"
                                type="text" 
                                name="nro_kardex" 
                                id="nro_kardex" 
                                 inputmode="numeric"     
                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                                value="{{ old('nro_kardex', $afiliado->puesto->nro_kardex ?? '') }}" 
                                required
                            />
                            @error('nro_kardex') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        {{-- CAMPO: NRO. DE LIBRO --}}
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="nro_libro">📚 Nro. de Libro: <span class="text-red-500">*</span></label>
                            <input 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('nro_libro') border-red-500 @enderror"
                                type="text" 
                                name="nro_libro" 
                                id="nro_libro" 
                                 inputmode="numeric"     
                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                                value="{{ old('nro_libro', $afiliado->puesto->nro_libro ?? '') }}" 
                                required
                            />
                            @error('nro_libro') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        {{-- CAMPO: FILA (SELECT) --}}
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="fila">📐 Fila: <span class="text-red-500">*</span></label>
                            <select 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('fila') border-red-500 @enderror"
                                name="fila" 
                                id="fila" 
                                required
                            >
                                @php $currentFila = old('fila', $afiliado->puesto->fila ?? ''); @endphp
                                <option value="" class="text-gray-500">Seleccione la Fila</option>
                                @foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $fila)
                                    <option value="{{ $fila }}" @selected($currentFila == $fila)>Fila {{ $fila }}</option>
                                @endforeach
                            </select>
                            @error('fila') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- CAMPO: MEDIDA PUESTO --}}
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="medida_puesto">📏 Medida (m): <span class="text-red-500">*</span></label>
                            <input 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('medida_puesto') border-red-500 @enderror"
                                type="text" 
                                name="medida_puesto" 
                                id="medida_puesto" 
                                value="{{ old('medida_puesto', $afiliado->puesto->medida_puesto ?? '') }}" 
                                required 
                                inputmode="decimal" 
                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 46"
                                placeholder="Ej: 3.50"
                            />
                            @error('medida_puesto') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- CAMPO: UBICACIÓN DE VENTA (Ocupa 2 columnas) --}}
                        <div class="col-span-1 md:col-span-2">
                            <label class="block font-medium text-sm text-gray-700" for="ubicacion_venta">📍 Ubicación de Venta: <span class="text-red-500">*</span></label>
                            <input 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('ubicacion_venta') border-red-500 @enderror"
                                type="text" 
                                name="ubicacion_venta" 
                                id="ubicacion_venta" 
                                value="{{ old('ubicacion_venta', $afiliado->puesto->ubicacion_venta ?? '') }}" 
                                required
                                placeholder="Descripción detallada de la ubicación"
                            />
                            @error('ubicacion_venta') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- CAMPO: ID MERCADERÍA (SELECT) (Ocupa 2 columnas) --}}
                        <div class="col-span-1 md:col-span-2">
                            <label class="block font-medium text-sm text-gray-700" for="id_mercaderia">🏷️ Clase de Mercadería: <span class="text-red-500">*</span></label>
                            <select 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('id_mercaderia') border-red-500 @enderror"
                                name="id_mercaderia" 
                                id="id_mercaderia" 
                                required
                            >
                                <option value="" class="text-gray-500">Seleccione la Mercadería</option>
                                @foreach ($mercaderias as $mercaderia)
                                    <option 
                                        value="{{ $mercaderia->id_mercaderia }}"
                                        @selected(old('id_mercaderia', $afiliado->id_mercaderia) == $mercaderia->id_mercaderia)>
                                        {{ $mercaderia->clase_mercaderia }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_mercaderia') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                    </div>
                </div>

                <div class="border-b border-gray-200 pb-8 pt-4">
                    <h2 class="text-2xl font-bold text-indigo-700 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" /></svg>
                        3. Gestión y Archivos
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                        {{-- CAMPO: ID GESTIÓN (SELECT) --}}
                        <div class="col-span-1 md:col-span-2">
                            <label class="block font-medium text-sm text-gray-700" for="id_gestion">🗓️ Gestión de Afiliación:</label>
                            <select 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('id_gestion') border-red-500 @enderror"
                                name="id_gestion" 
                                id="id_gestion"
                            >
                                <option value="" class="text-gray-500">Seleccione la Gestión</option>
                                @foreach ($gestiones as $gestion)
                                    <option 
                                        value="{{ $gestion->id_gestion }}"
                                        @selected(old('id_gestion', $afiliado->id_gestion) == $gestion->id_gestion)>
                                        {{ $gestion->nombre_gestion }} 
                                    </option>
                                @endforeach
                            </select>
                            @error('id_gestion') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- CAMPO: FECHA DE AFILIACIÓN --}}
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="fecha_afiliacion">📅 Fecha de Afiliación: <span class="text-red-500">*</span></label>
                            <input 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('fecha_afiliacion') border-red-500 @enderror"
                                type="date" 
                                name="fecha_afiliacion" 
                                id="fecha_afiliacion" 
                                value="{{ old('fecha_afiliacion', $afiliado->fecha_afiliacion) }}" 
                                required
                            />
                            @error('fecha_afiliacion') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
{{-- CAMPO: RECARNETIZACIÓN (SEPARADO PARA PREFIJO FIJO Y SUFIJO EDITABLE) --}}
{{-- Este campo registra una NUEVA recarnetización, manteniendo la fecha y gestión ACTUALES como prefijo. --}}
<div class="lg:col-span-4">
    <label class="block font-medium text-sm text-gray-700 mb-1" for="recarnetizacion_suffix">
        **🎟️ Recarnetización (Solo se edita el periodo):**
    </label>
    <div class="mt-1 flex items-stretch">
        
        {{-- PREFIJO FIJO (Muestra la fecha actual y el texto fijo "gestión ") --}}
        <input 
            type="text" 
            class="w-auto bg-green-50 border-gray-300 border-r-0 rounded-l-md rounded-r-none shadow-sm text-sm text-gray-600 pointer-events-none focus:ring-0 focus:border-gray-300" 
            value="{{ \Carbon\Carbon::today()->format('d/m/Y') . ' gestión ' }}" 
            readonly 
        />
        
        {{-- SUFIJO EDITABLE (Donde el usuario pone solo los años) --}}
        <input 
            id="recarnetizacion_suffix" 
            name="recarnetizacion_suffix" 
            type="text" 
            class="flex-grow w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-r-md rounded-l-none shadow-sm @error('recarnetizacion_suffix') border-red-500 @enderror" 
            {{-- Lógica old(): mantiene el valor anterior, si no existe, usa el valor guardado en $afiliado --}}
            value="{{ old('recarnetizacion_suffix', $afiliado->recarnetizacion_suffix ?? '') }}" 
            placeholder="Ej: 2020-2025 (Solo números y guiones)" 
            {{-- Validaciones de teclas permitidas --}}
            onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 45"
        />
    </div>
    
    {{-- Mensaje de error de validación --}}
    @error('recarnetizacion_suffix') 
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
    @enderror

    {{-- Nota para el usuario sobre la acción --}}
    <p class="mt-2 text-xs text-indigo-600 italic">
        *Al ingresar un periodo aquí y guardar, se registrará una nueva Recarnetización con la fecha de hoy.
    </p>
</div>
                        
                        {{-- CAMPO: CARGO HISTÓRICO (Ocupa 4 columnas, es más largo) --}}
                        <div class="lg:col-span-4">
                            <label class="block font-medium text-sm text-gray-700" for="cargo_alguna_vez">📜 Cargo Histórico (Directiva, etc.):</label>
                            <input 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('cargo_alguna_vez') border-red-500 @enderror"
                                type="text" 
                                name="cargo_alguna_vez" 
                                id="cargo_alguna_vez" 
                                value="{{ old('cargo_alguna_vez', $afiliado->cargo_alguna_vez) }}"
                                placeholder="Ej: Vocal 2020-2022"
                            />
                            @error('cargo_alguna_vez') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        {{-- CAMPO: FOTO (FILE INPUT) y FIMA (FILE INPUT) en una fila de 2 columnas --}}
                        <div class="col-span-1 md:col-span-2 border p-4 rounded-lg bg-gray-50">
                            <label class="block font-medium text-sm text-gray-700 mb-2" for="foto">🖼️ Foto de Perfil:</label>
                            <input 
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                                type="file" 
                                name="foto" 
                                id="foto"
                            />
                            @if ($afiliado->foto)
                                <p class="mt-2 text-xs text-gray-600">
                                    ✅ **Actual:** <a href="{{ asset('storage/' . $afiliado->foto) }}" target="_blank" class="text-green-500 hover:text-green-700 font-semibold underline">Ver Foto</a>. 
                                    <span class="italic">Sube un nuevo archivo para reemplazar.</span>
                                </p>
                            @endif
                            @error('foto') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="col-span-1 md:col-span-2 border p-4 rounded-lg bg-gray-50">
                            <label class="block font-medium text-sm text-gray-700 mb-2" for="firma">✍️ Firma Digital:</label>
                            <input 
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                type="file" 
                                name="firma" 
                                id="firma"
                            />
                            @if ($afiliado->firma)
                                <p class="mt-2 text-xs text-gray-600">
                                    ✅ **Actual:** <a href="{{ asset('storage/' . $afiliado->firma) }}" target="_blank" class="text-blue-500 hover:text-blue-700 font-semibold underline">Ver Firma</a>. 
                                    <span class="italic">Sube un nuevo archivo para reemplazar.</span>
                                </p>
                            @endif
                            @error('firma') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                    </div>
                </div>

                <div class="pt-4">
                    <h2 class="text-2xl font-bold text-indigo-700 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                        4. Notas Adicionales
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- CAMPO: OBSERVACIONES (TEXTAREA) --}}
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="observaciones">📝 Observaciones:</label>
                            <textarea 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('observaciones') border-red-500 @enderror"
                                name="observaciones" 
                                id="observaciones"
                                rows="4"
                                placeholder="Notas internas sobre el afiliado o su puesto..."
                            >{{ old('observaciones', $afiliado->observaciones) }}</textarea>
                            @error('observaciones') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- CAMPO: OTROS DATOS (TEXTAREA) --}}
                        <div>
                            <label class="block font-medium text-sm text-gray-700" for="otros">✨ Otros Datos:</label>
                            <textarea 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full @error('otros') border-red-500 @enderror"
                                name="otros" 
                                id="otros"
                                rows="4"
                                placeholder="Cualquier otra información relevante no cubierta por los campos anteriores."
                            >{{ old('otros', $afiliado->otros) }}</textarea>
                            @error('otros') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                    </div>
                </div>


                {{-- BOTÓN DE SUBMIT Y CANCELAR --}}
                <div class="pt-6 border-t border-gray-200 flex justify-end space-x-4">
                    <a href="{{ route('afiliados.index') }}" class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 uppercase tracking-wider shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        ↩️ Cancelar y Volver
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider shadow-md hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        ✅ Guardar Cambios
                    </button>
                </div>
            </form>
            </div>
        </div>
    </div>
@endsection