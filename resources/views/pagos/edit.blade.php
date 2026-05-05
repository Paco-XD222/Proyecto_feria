@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        ✏️ {{ __('Editar Registro de Pago') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

            {{-- Manejo de Errores --}}
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

            <form method="POST" action="{{ route('pagos.update', $pago) }}">
                @csrf
                @method('PUT')

                <div class="space-y-6">

                    {{-- Campo 1: Afiliado (Dropdown con Select2) --}}
                    <div>
                        <label for="afiliado_id" class="block font-medium text-sm text-gray-700">
                            {{ __('Afiliado') }} <span class="text-red-500">*</span>
                        </label>
                        <select id="afiliado_id" name="afiliado_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="" disabled>Seleccione un Afiliado...</option>
                            @foreach ($afiliados as $afiliado)
                                <option value="{{ $afiliado->id_afiliado }}" 
                                    {{ (old('afiliado_id') == $afiliado->id_afiliado || $pago->afiliado_id == $afiliado->id_afiliado) ? 'selected' : '' }}>
                                    Kardex: {{ $afiliado->puesto?->nro_kardex ?? 'N/A' }} - {{ $afiliado->apellido_paterno }} {{ $afiliado->apellido_materno }}, {{ $afiliado->nombre_afiliado }} (C.I.: {{ $afiliado->ci }})
                                </option>
                            @endforeach
                        </select>
                        @error('afiliado_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        {{-- Campo 2: Concepto --}}
                        <div>
                            <label for="concepto" class="block font-medium text-sm text-gray-700">
                                {{ __('Concepto de Pago') }} <span class="text-red-500">*</span>
                            </label>
                            <input id="concepto" name="concepto" type="text" value="{{ old('concepto', $pago->concepto) }}" required 
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <p class="text-xs text-gray-500 mt-1">Ej: Pago de Afiliación Inicial, Recarnetización 2025</p>
                            @error('concepto')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo 3: Monto --}}
                        <div>
                            <label for="monto" class="block font-medium text-sm text-gray-700">
                                {{ __('Monto (Bs.)') }} <span class="text-red-500">*</span>
                            </label>
                            <input id="monto" name="monto" type="number" step="0.01" min="0.01" value="{{ old('monto', $pago->monto) }}" required 
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @error('monto')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Campo 4: Fecha de Pago (Se actualiza automáticamente) --}}
                        <div>
                            <label for="fecha_pago" class="block font-medium text-sm text-gray-700">
                                {{ __('Fecha de Pago') }} <span class="text-red-500">*</span>
                            </label>
                            <input id="fecha_pago" name="fecha_pago" type="date" value="{{ date('Y-m-d') }}" readonly 
                                class="mt-1 block w-full border-gray-300 bg-gray-100 cursor-not-allowed rounded-md shadow-sm">
                            <p class="text-xs text-gray-500 mt-1">📅 Fecha automática del sistema (se actualiza al editar)</p>
                            <p class="text-xs text-gray-400 mt-1">🕒 Fecha original del registro: {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</p>
                        </div>

                        {{-- Campo 5: Nro. Recibo (BLOQUEADO - No se puede modificar) --}}
                        <div>
                            <label for="nro_recibo" class="block font-medium text-sm text-gray-700">
                                {{ __('Nro. de Recibo') }}
                            </label>
                            <input id="nro_recibo" name="nro_recibo" type="text" value="{{ $pago->nro_recibo }}" readonly
                                class="mt-1 block w-full border-gray-300 bg-gray-100 cursor-not-allowed rounded-md shadow-sm">
                            <p class="text-xs text-gray-500 mt-1">🔢 Número de recibo original (no modificable)</p>
                        </div>
                    </div>

                </div>
                
                <div class="flex justify-end mt-6">
                    <a href="{{ route('pagos.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out duration-150 mr-3">
                        Cancelar
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-900 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Guardar Cambios
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

{{-- SELECT2: Inicializar búsqueda en el dropdown --}}
@push('scripts') 
<script>
    $(document).ready(function() {
        $('#afiliado_id').select2({
            placeholder: "Escriba el nombre, apellido o C.I. del afiliado...",
            allowClear: true
        });
    });
</script>
@endpush