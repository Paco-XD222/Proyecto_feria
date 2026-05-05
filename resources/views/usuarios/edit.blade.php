@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        ✏️ {{ __('Editar Usuario del Sistema') }}
    </h2>
@endsection

@section('content')
<div class="py-4">
    <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <h1 class="text-2xl font-bold text-gray-800">
                    Editando Usuario: <span class="text-indigo-600">{{ $user->name }}</span>
                </h1>
                <a href="{{ route('usuarios.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Volver
                </a>
            </div>

            {{-- Manejo de Errores de Validación --}}
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <p class="font-bold">Por favor, corrija los siguientes errores:</p>
                    <ul class="list-disc list-inside mt-2">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('usuarios.update', $user->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- SECCIÓN 1: DATOS BÁSICOS Y ROL --}}
                <div class="mb-8 border-b pb-4">
                    <h2 class="text-xl font-semibold text-indigo-700 mb-4">1. Información Básica y Permisos</h2>

                    {{-- NOMBRE COMPLETO --}}
                    <div>
                        <label for="name" class="block font-medium text-sm text-gray-700">Nombre Completo <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- CORREO ELECTRÓNICO --}}
                    <div>
                        <label for="email" class="block font-medium text-sm text-gray-700">Correo Electrónico <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- ASIGNAR ROL --}}
                    <div>
                        <label for="role_id" class="block font-medium text-sm text-gray-700">Asignar Rol <span class="text-red-500">*</span></label>
                        <select name="role_id" id="role_id" required
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Seleccione un Rol</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- SECCIÓN 2: CAMBIO DE CONTRASEÑA --}}
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b border-dashed pb-2">2. Cambio de Contraseña (Opcional)</h2>
                    <p class="text-sm text-gray-500 mb-4">Solo ingrese los campos si desea cambiar la contraseña actual del usuario.</p>

                    {{-- NUEVA CONTRASEÑA --}}
                    <div>
                        <label for="password" class="block font-medium text-sm text-gray-700">Nueva Contraseña</label>
                        <div class="relative mt-1">
                            <input 
                                type="password" 
                                name="password" 
                                id="password"
                                {{-- CLASE CLAVE: Añadir pr-10 para dejar espacio al icono --}}
                                class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pr-10"
                            >
                            {{-- Icono del Ojo (Toggle) --}}
                            <button type="button" 
                                    id="togglePassword" 
                                    {{-- CLASES CLAVE: inset-y-0 y h-full para estirar y centrar verticalmente --}}
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-600 h-full" 
                                    onclick="toggleVisibility('password', 'togglePasswordIcon')">
                                {{-- SVG Ojo Tachado (Estado inicial) --}}
                                <svg id="togglePasswordIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.603-3.327m9.897 1.258a1 1 0 10-1.258-1.258m1.258 1.258L22 12c-1.275 4.057-5.065 7-9.543 7-1.605 0-3.136-.328-4.543-.918" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z" />
                                </svg>
                            </button>
                        </div>
                        @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- CONFIRMAR NUEVA CONTRASEÑA --}}
                    <div class="mt-4">
                        <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirmar Nueva Contraseña</label>
                        <div class="relative mt-1">
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                id="password_confirmation"
                                {{-- CLASE CLAVE: Añadir pr-10 para dejar espacio al icono --}}
                                class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pr-10"
                            >
                            {{-- Icono del Ojo (Toggle) --}}
                            <button type="button" 
                                    id="togglePasswordConfirmation" 
                                    {{-- CLASES CLAVE: inset-y-0 y h-full para estirar y centrar verticalmente --}}
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-600 h-full"
                                    onclick="toggleVisibility('password_confirmation', 'toggleConfirmIcon')">
                                {{-- SVG Ojo Tachado (Estado inicial) --}}
                                <svg id="toggleConfirmIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.603-3.327m9.897 1.258a1 1 0 10-1.258-1.258m1.258 1.258L22 12c-1.275 4.057-5.065 7-9.543 7-1.605 0-3.136-.328-4.543-.918" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- BOTONES DE FORMULARIO --}}
                <div class="flex items-center justify-end pt-4 space-x-3 border-t">
                    <a href="{{ route('usuarios.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Cancelar
                    </a>

                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                        ✔️ Actualizar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    /**
     * Alterna la visibilidad de un campo de contraseña y cambia el icono.
     * @param {string} inputId - El ID del campo de input (e.g., 'password').
     * @param {string} iconId - El ID del SVG del icono (e.g., 'togglePasswordIcon').
     */
    function toggleVisibility(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        // SVG Path para el Ojo Tachado (Ocultar) - Estado inicial (Tailwind Heroicons: Eye Off)
        const closedEyePaths = [
            'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.603-3.327m9.897 1.258a1 1 0 10-1.258-1.258m1.258 1.258L22 12c-1.275 4.057-5.065 7-9.543 7-1.605 0-3.136-.328-4.543-.918',
            'M12 12c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z'
        ];

        // SVG Path para el Ojo Abierto (Mostrar) (Tailwind Heroicons: Eye)
        const openEyePaths = [
            'M2.049 13.918C4.07 18.33 8.018 21 12 21s7.93-2.67 9.951-7.082a.997.997 0 000-1.836C19.93 5.67 15.982 3 12 3s-7.93 2.67-9.951 7.082a.997.997 0 000 1.836z',
            'M12 15a3 3 0 100-6 3 3 0 000 6z'
        ];


        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            // Cambiar a icono de Ojo Abierto
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${openEyePaths[0]}" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${openEyePaths[1]}" />`;
        } else {
            passwordInput.type = "password";
            // Cambiar a icono de Ojo Tachado (Ocultar)
             icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${closedEyePaths[0]}" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${closedEyePaths[1]}" />`;
        }
    }
</script>
@endpush