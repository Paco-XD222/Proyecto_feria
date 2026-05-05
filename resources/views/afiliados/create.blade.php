@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        ✨ {{ __('Registro de Nuevo Afiliado (Kárdex)') }} ✨
    </h2>
@endsection

@section('content')

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- INICIO: MUESTRA ERRORES DE VALIDACIÓN --}}
                @if ($errors->any())
                    <div class="mb-4 p-4 border border-red-400 bg-red-100 text-red-700 rounded">
                        <strong>⚠️ ¡Error de Validación!</strong> Por favor, corrige los siguientes problemas:
                        <ul class="list-disc list-inside mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{-- FIN: MUESTRA ERRORES DE VALIDACIÓN --}}

                <form action="{{ route('afiliados.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- ========================================================== --}}
                    <h2 class="text-xl font-semibold mb-2 text-indigo-700">1. 🧑‍🤝‍👩 Información Personal</h2>
                    <hr class="mb-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- CAMPO: EMAIL (Usando Componentes de Breeze) --}}
                        <div class="mt-2 md:col-span-2"> 
                            <x-input-label for="email" :value="__('📧 Correo Electrónico (Usuario de Acceso):')" />
                            <div class="flex items-start space-x-4"> 
                                <x-text-input 
                                    id="email" 
                                    name="email" 
                                    type="email" 
                                    class="w-3/4" 
                                    :value="old('email')" 
                                    autocomplete="gmail.com"
                                    required 
                                    onblur="autocompletarEmail(this)"
                                    placeholder="registra solo la primera parte (el @gmail.com se añade solo)"
                                />
                                <p class="mt-2 text-sm text-gray-500 italic hidden sm:block">
                                    🔑 Este será el nombre de usuario para acceder al sistema.
                                </p>
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        {{-- CAMPO: C.I. --}}
                        <div class="mt-2">
                            <x-input-label for="ci" :value="__('🆔 C.I. (Contraseña Temporal: AF + C.I.):')" />
                            <div class="flex items-center space-x-2"> 
                                <x-text-input 
                                    id="ci" 
                                    name="ci" 
                                    type="text" 
                                    class="w-full" 
                                    :value="old('ci')" 
                                    inputmode="numeric"     
                                    onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                                    required 
                                />
                                <span class="text-blue-500 cursor-pointer text-xl" title="La contraseña temporal se genera automáticamente concatenando 'AF' y el número de C.I.">
                                    💡
                                </span>
                            </div>
                            <x-input-error :messages="$errors->get('ci')" class="mt-2" />
                        </div>

                        {{-- CAMPO: FECHA DE NACIMIENTO --}}
                        <div class="mt-2">
                            <x-input-label for="fecha_nacimiento" :value="__('🎂 Fecha de Nacimiento:')" />
                            <x-text-input 
                                id="fecha_nacimiento" 
                                name="fecha_nacimiento" 
                                type="date" 
                                class="w-full" 
                                :value="old('fecha_nacimiento')" 
                                required 
                            />
                            <x-input-error :messages="$errors->get('fecha_nacimiento')" class="mt-2" />
                        </div>
                        
                        {{-- CAMPO: NOMBRE --}}
                        <div class="mt-2">
                            <x-input-label for="nombre_afiliado" :value="__('👤 Nombre:')" />
                            <x-text-input 
                                id="nombre_afiliado" 
                                name="nombre_afiliado" 
                                type="text" 
                                class="w-full" 
                                :value="old('nombre_afiliado')" 
                                onkeypress="return !(event.charCode >= 48 && event.charCode <= 57)"
                                required 
                            />
                            <x-input-error :messages="$errors->get('nombre_afiliado')" class="mt-2" />
                        </div>
                        
                        {{-- CAMPO: TELÉFONO CELULAR --}}
                        <div class="mt-2">
                            <x-input-label for="telefono" :value="__('📱 Teléfono Celular:')" />
                            <x-text-input 
                                id="telefono" 
                                name="telefono" 
                                type="text" 
                                class="w-full" 
                                inputmode="numeric"     
                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                                :value="old('telefono')" 
                            />
                            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                        </div>

                        {{-- CAMPO: APELLIDO PATERNO --}}
                        <div class="mt-2">
                            <x-input-label for="apellido_paterno" :value="__('👨 Apellido Paterno:')" />
                            <x-text-input 
                                id="apellido_paterno" 
                                name="apellido_paterno" 
                                type="text" 
                                class="w-full" 
                                :value="old('apellido_paterno')" 
                                onkeypress="return !(event.charCode >= 48 && event.charCode <= 57)"
                                required 
                            />
                            <x-input-error :messages="$errors->get('apellido_paterno')" class="mt-2" />
                        </div>

                        {{-- CAMPO: ESTADO CIVIL (SELECT) --}}
                        <div class="mt-2">
                            <x-input-label for="estado_civil" :value="__('💍 Estado Civil:')" />
                            <select 
                                id="estado_civil"
                                name="estado_civil" 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" 
                            >
                                <option value="" {{ old('estado_civil') == '' ? 'selected' : '' }}>Seleccione</option>
                                <option value="Soltero" {{ old('estado_civil') == 'Soltero' ? 'selected' : '' }}>Soltero(a) 🧍</option>
                                <option value="Casado" {{ old('estado_civil') == 'Casado' ? 'selected' : '' }}>Casado(a) 👰‍♀️🤵‍♂️</option>
                                <option value="Viudo" {{ old('estado_civil') == 'Viudo' ? 'selected' : '' }}>Viudo(a) 🥀</option>
                                <option value="Divorciado" {{ old('estado_civil') == 'Divorciado' ? 'selected' : '' }}>Divorciado(a) 💔</option>
                            </select>
                            <x-input-error :messages="$errors->get('estado_civil')" class="mt-2" />
                        </div>
                        
                        {{-- CAMPO: APELLIDO MATERNO --}}
                        <div class="mt-2">
                            <x-input-label for="apellido_materno" :value="__('👩 Apellido Materno:')" />
                            <x-text-input 
                                id="apellido_materno" 
                                name="apellido_materno" 
                                type="text" 
                                class="w-full" 
                                :value="old('apellido_materno')" 
                                onkeypress="return !(event.charCode >= 48 && event.charCode <= 57)"
                                required 
                            />
                            <x-input-error :messages="$errors->get('apellido_materno')" class="mt-2" />
                        </div>
                        
                        {{-- CAMPO: NOMBRE DEL CÓNYUGE --}}
                        <div class="mt-2">
                            <x-input-label for="nombre_conyuge" :value="__('❤️ Nombre del Cónyuge:')" />
                            <x-text-input 
                                id="nombre_conyuge" 
                                name="nombre_conyuge" 
                                type="text" 
                                class="w-full" 
                                :value="old('nombre_conyuge')" 
                                onkeypress="return !(event.charCode >= 48 && event.charCode <= 57)"
                                required 
                            />
                            <x-input-error :messages="$errors->get('nombre_conyuge')" class="mt-2" />
                        </div>

                        {{-- CAMPO: DIRECCIÓN (Ocupa las dos columnas) --}}
                        <div class="mt-2 md:col-span-2">
                            <x-input-label for="direccion" :value="__('🏠 Dirección:')" />
                            <x-text-input 
                                id="direccion" 
                                name="direccion" 
                                type="text" 
                                class="w-full" 
                                :value="old('direccion')" 
                                required 
                                
                            />
                            <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
                             <small class="text-gray-500">Ejemplo: Calle Ayacucho # 153</small>
                        </div>

                        {{-- CAMPO: NRO. DE FAMILIARES --}}
                        <div class="mt-2">
                            <x-input-label for="numero_familia" :value="__('👨‍👩‍👧‍👦 Nro. de Familiares:')" />
                            <x-text-input 
                                id="numero_familia" 
                                name="numero_familia" 
                                type="text" 
                                class="w-full" 
                                :value="old('numero_familia')" 
                                 required 
                                inputmode="numeric"     
                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                            />
                            <x-input-error :messages="$errors->get('numero_familia')" class="mt-2" />
                        </div>
                        
                    </div>
                    {{-- ========================================================== --}}

                    <h2 class="text-xl font-semibold mt-8 mb-2 text-indigo-700">2. 🏢 Datos de Puesto y Actividad</h2>
                    <hr class="mb-4">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        {{-- CAMPO: NRO. KARDEX --}}
                        <div class="mt-2">
                            <x-input-label for="nro_kardex" :value="__('📋 Nro. Kardex:')" />
                            <x-text-input 
                                id="nro_kardex" 
                                name="nro_kardex" 
                                type="text" 
                                class="w-full" 
                                :value="old('nro_kardex')" 
                                required 
                                inputmode="numeric"     
                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                            />
                            <x-input-error :messages="$errors->get('nro_kardex')" class="mt-2" />
                        </div>
                        
                        {{-- CAMPO: NRO. DE LIBRO --}}
                        <div class="mt-2">
                            <x-input-label for="nro_libro" :value="__('📖 Nro. de Libro:')" />
                            <x-text-input 
                                id="nro_libro" 
                                name="nro_libro" 
                                type="text" 
                                class="w-full" 
                                :value="old('nro_libro')" 
                                required 
                                inputmode="numeric"     
                                    onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                            />
                            <x-input-error :messages="$errors->get('nro_libro')" class="mt-2" />
                        </div>

                        {{-- CAMPO: MEDIDA PUESTO --}}
                        <div class="mt-2">
                            <x-input-label for="medida_puesto" :value="__('📏 Medida (m):')" />
                            <x-text-input 
                                id="medida_puesto" 
                                name="medida_puesto" 
                                type="text" 
                                class="w-full" 
                                :value="old('medida_puesto')" 
                                required 
                                inputmode="decimal" 
                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 46"
                                placeholder="Ej: 3.50"
                            />
                            <x-input-error :messages="$errors->get('medida_puesto')" class="mt-2" />
                        </div>

                        {{-- CAMPO: FILA (SELECT) --}}
                        <div class="mt-2">
                            <x-input-label for="fila" :value="__('⬅️ Fila:')" />
                            <select 
                                id="fila"
                                name="fila" 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" 
                                required
                            >
                                <option value="">Seleccione la Fila</option>
                                @foreach ($filasDisponibles as $fila)
                                    <option value="{{ $fila }}" {{ old('fila') == $fila ? 'selected' : '' }}>{{ $fila }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('fila')" class="mt-2" />
                        </div>
                        
                        {{-- CAMPO: ID MERCADERÍA (SELECT) --}}
                        <div class="mt-2 md:col-span-2">
                            <x-input-label for="id_mercaderia" :value="__('🛍️ Clase de Mercadería:')" />
                            <select 
                                id="id_mercaderia"
                                name="id_mercaderia" 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                required
                            >
                                <option value="">Seleccione la Mercadería</option>
                                @foreach ($mercaderias as $mercaderia)
                                    <option value="{{ $mercaderia->id_mercaderia }}" {{ old('id_mercaderia') == $mercaderia->id_mercaderia ? 'selected' : '' }}>{{ $mercaderia->clase_mercaderia }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('id_mercaderia')" class="mt-2" />
                        </div>

                        {{-- CAMPO: UBICACIÓN DE VENTA (Ocupa las tres columnas) --}}
                        <div class="mt-2 md:col-span-3">
                            <x-input-label for="ubicacion_venta" :value="__('📍 Ubicación de Venta (Detalle):')" />
                            <x-text-input 
                                id="ubicacion_venta" 
                                name="ubicacion_venta" 
                                type="text" 
                                class="w-full" 
                                :value="old('ubicacion_venta')" 
                                required 
                            />
                            <x-input-error :messages="$errors->get('ubicacion_venta')" class="mt-2" />
                             <small class="text-gray-500">Ejemplo: Av. Ferroviaria</small>
                        </div>
                        
                    </div>
                    {{-- ========================================================== --}}

                    <h2 class="text-xl font-semibold mt-8 mb-2 text-indigo-700">3. 📅 Datos Históricos y Gestión</h2>
                    <hr class="mb-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- CAMPO: ID GESTIÓN (SELECT) --}}
                        <div class="mt-2">
                            <x-input-label for="id_gestion" :value="__('🗓️ Gestión de Afiliación:')" />
                            <select 
                                id="id_gestion"
                                name="id_gestion" 
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                            >
                                <option value="">Seleccione la Gestión</option>
                                @foreach ($gestiones as $gestion)
                                    <option value="{{ $gestion->id_gestion }}" {{ old('id_gestion') == $gestion->id_gestion ? 'selected' : '' }}>{{ $gestion->nombre_gestion }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('id_gestion')" class="mt-2" />
                        </div>

                        {{-- CAMPO: FECHA DE AFILIACIÓN --}}
                        <div class="mt-2">
                            <x-input-label for="fecha_afiliacion" :value="__('➕ Fecha de Afiliación:')" />
                            <x-text-input 
                                id="fecha_afiliacion" 
                                name="fecha_afiliacion" 
                                type="date" 
                                class="w-full" 
                               :value="old('fecha_afiliacion')"
                                required 
                            />
                            <x-input-error :messages="$errors->get('fecha_afiliacion')" class="mt-2" />
                        </div>
                        
                        {{-- CAMPO: CARGO HISTÓRICO --}}
                        <div class="mt-2">
                            <x-input-label for="cargo_alguna_vez" :value="__('🎖️ Cargo Histórico:')" />
                            <x-text-input 
                                id="cargo_alguna_vez" 
                                name="cargo_alguna_vez" 
                                type="text" 
                                placeholder="Ej: Vocal 2020-2022"
                                class="w-full" 
                                :value="old('cargo_alguna_vez')" 
                                
                            />
                            <x-input-error :messages="$errors->get('cargo_alguna_vez')" class="mt-2" />
                        </div>
{{-- CAMPO: RECARNETIZACIÓN (SEPARADO PARA PREFIJO FIJO Y SUFIJO EDITABLE) --}}
<div class="mt-2 flex items-end space-x-2">
    <div class="flex-grow">
        <x-input-label :value="__('🔄 Recarnetización:')" />
        <div class="flex">
            {{-- CAMPO INEDITABLE (Muestra la fecha y el texto fijo) --}}
            <x-text-input 
                type="text" 
                class="w-auto bg-gray-100 border-r-0 rounded-r-none pointer-events-none" 
                value="{{ \Carbon\Carbon::today()->format('d/m/Y') . ' gestión ' }}" 
                readonly 
            />
            {{-- CAMPO EDITABLE (Aquí el usuario pone solo los años) --}}
            <x-text-input 
                id="recarnetizacion_suffix" 
                name="recarnetizacion_suffix" 
                type="text" 
                class="flex-grow w-full rounded-l-none" 
                :value="old('recarnetizacion_suffix')" 
                placeholder="Ej: 2020-2025" 
                required
                onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 45"
            />
        </div>
        {{-- Muestra el error de validación para el campo editable --}}
        <x-input-error :messages="$errors->get('recarnetizacion_suffix')" class="mt-2" />
        <small class="text-gray-500">Solo añada el rango de años de la gestión (Ej: 2020-2025).</small>
    </div>
</div>


                        {{-- CAMPO: OBSERVACIONES (TEXTAREA, Ocupa las dos columnas) --}}
                        <div class="mt-2 md:col-span-2">
                            <x-input-label for="observaciones" :value="__('📝 Observaciones:')" />
                            <textarea 
                                id="observaciones"
                                name="observaciones" 
                                placeholder="Notas internas sobre el afiliado o su puesto..."
                                onkeypress="return !(event.charCode >= 48 && event.charCode <= 57)"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                            >{{ old('observaciones') }}</textarea>
                            <x-input-error :messages="$errors->get('observaciones')" class="mt-2" />
                        </div>

                        {{-- CAMPO: OTROS DATOS (TEXTAREA, Ocupa las dos columnas) --}}
                        <div class="mt-2 md:col-span-2">
                            <x-input-label for="otros" :value="__('💡 Otros Datos:')" />
                            <textarea 
                                id="otros"
                                name="otros" 
                                placeholder="Cualquier otra información relevante no cubierta por los campos anteriores."
                                onkeypress="return !(event.charCode >= 48 && event.charCode <= 57)"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                            >{{ old('otros') }}</textarea>
                            <x-input-error :messages="$errors->get('otros')" class="mt-2" />
                        </div>
                        
                    </div>
                    {{-- ========================================================== --}}

                    <h2 class="text-xl font-semibold mt-8 mb-2 text-indigo-700">4. 🖼️ Fotos y Firma (Archivos)</h2>
                    <hr class="mb-4">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- CAMPO: FOTO (FILE INPUT) --}}
                        <div class="mt-2">
                            <x-input-label for="foto" :value="__('📸 Foto:')" />
                            <input 
                                id="foto"
                                name="foto"
                                type="file" 
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 mt-1"
                            />
                            <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                        </div>
                        
                        {{-- CAMPO: FIRMA (FILE INPUT) --}}
                        <div class="mt-2">
                            <x-input-label for="firma" :value="__('✍️ Firma:')" />
                            <input 
                                id="firma"
                                name="firma"
                                type="file" 
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 mt-1"
                            />
                            <x-input-error :messages="$errors->get('firma')" class="mt-2" />
                        </div>
                        
                    </div>
                    {{-- ========================================================== --}}
                    
                    <input type="hidden" name="id_usuario" value="">

                    {{-- BOTÓN DE SUBMIT (USANDO COMPONENTE DE BREEZE) --}}
                     
                    <div class="flex items-center justify-end mt-4 space-x-3">
                       <a href="{{ route('afiliados.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Cancelar
                    </a>
                        <x-primary-button>
                            ✅ {{ __('Registrar Afiliado') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    function autocompletarEmail(input) {
        let value = input.value.trim(); // Obtiene el valor y quita espacios
        const suffix = '@gmail.com';

        // 1. Si el campo está vacío, no hace nada
        if (value.length === 0) {
            return;
        }

        // 2. Si ya contiene '@', no hace nada (para no romper correos de otros dominios)
        if (value.includes('@')) {
            return;
        }

        // 3. Añade el sufijo y actualiza el valor del campo
        input.value = value + suffix;
    }
</script>
@endsection