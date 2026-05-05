<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('img/casa.png') }}" alt="Mi Aplicación" class="block h-9 w-auto"/>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Panel Principal') }}
    </x-nav-link>

    {{-- ENLACES DE GESTIÓN (SOLO VISIBLES PARA ROLES 1 Y 2) --}}
    @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
        
        {{-- INICIO DE LA AGRUPACIÓN DE GESTIÓN CON ESPACIO REDUCIDO --}}
        <div class="flex space-x-4">
        
            {{-- ENLACE AFILIADOS --}}
            <x-nav-link :href="route('afiliados.index')" :active="request()->routeIs('afiliados.*')">
                {{ __('Afiliados') }}
            </x-nav-link>
            {{-- NUEVO ENLACE: GESTIÓN DE PAGOS (AÑADIDO AQUÍ) --}}
            <x-nav-link :href="route('pagos.index')" :active="request()->routeIs('pagos.*')">
                {{ __('Gestión de Pagos') }} 
            </x-nav-link>

            {{-- ENLACE DIRECTIVOS --}}
            <x-nav-link :href="route('directivos.index')" :active="request()->routeIs('directivos.*')">
                {{ __('Directivos') }}
            </x-nav-link>
            {{-- NUEVO ENLACE: GESTIÓN DE PERÍODOS/AÑOS (ROL 1 Y 2) --}}
            <x-nav-link :href="route('gestion.index')" :active="request()->routeIs('gestion.*')">
                    {{ __('Gestión de Períodos') }}
            </x-nav-link>
            
            {{-- ENLACE MERCADERÍA --}}
            <x-nav-link :href="route('mercaderia.index')" :active="request()->routeIs('mercaderia.*')">
                {{ __('Mercadería') }}
            </x-nav-link>

            {{-- ENLACES EXCLUSIVOS PARA ADMINISTRADOR (SOLO ROL 1) --}}
            @if (Auth::user()->role_id == 1)
                <x-nav-link :href="route('usuarios.index')" :active="request()->routeIs('usuarios.*')">
                    {{ __('Usuarios y Roles') }}
                </x-nav-link>
            @endif
        
        </div> {{-- FIN DE LA AGRUPACIÓN DE GESTIÓN --}}
        
        <div class="hidden sm:flex sm:items-center sm:ms-6">
            <x-dropdown align="left" width="48">
                <x-slot name="trigger">
                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        <div>{{ __('Reportes') }}</div>

                        <div class="ms-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link :href="route('reportes.afiliados')">
                        {{ __('Kárdex General') }}
                    </x-dropdown-link>

                    <x-dropdown-link :href="route('reportes.pagos')">
                        {{ __('Historial de Pagos') }}
                    </x-dropdown-link>

                    {{-- NUEVO: REPORTE DE DIRECTIVOS (Limpio) --}}
                    <x-dropdown-link :href="route('reportes.directivos')">
                        {{ __('Reporte Directivos') }}
                    </x-dropdown-link>

                    {{-- REPORTE DE USUARIOS (Limpio) --}}
                    @if (Auth::user()->role_id == 1)
                        <x-dropdown-link :href="route('reportes.usuarios')">
                            {{ __('Usuarios y Permisos') }}
                        </x-dropdown-link>
                    @endif
                </x-slot>
            </x-dropdown>
        </div>
        {{-- ENLACES PARA AFILIADOS (ROL 3) --}}
    @elseif (Auth::user()->role_id == 3)
        
        @php
            // Se realiza la verificación de afiliación ANTES de acceder a cualquier propiedad
            $afiliado = Auth::user()->afiliado;
        @endphp

        @if ($afiliado)
            {{-- Enlace solo visible si el usuario tiene un registro de Afiliado válido --}}
            <x-nav-link :href="route('afiliados.show', $afiliado->id_afiliado)" :active="request()->routeIs('afiliados.show')">
                {{ __('Mi Kárdex') }}
            </x-nav-link>
        @else
            {{-- Mensaje de seguridad si el usuario es Rol 3 pero no tiene registro de afiliado (caso de datos anómalos) --}}
            <span class="text-gray-500 ms-3">Pendiente de Activación</span>
        @endif
        
    @endif {{-- FIN DEL @if principal de roles --}}
</div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                  
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                           <img src="{{ asset('img/perfil.png') }}" alt="Mi Aplicación" class="block h-9 w-auto"/>
                        <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                       
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('cerrar sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            
            {{-- 🟢 CÓDIGO NUEVO: BOTÓN HAMBURGER PARA ABRIR EL MENÚ MÓVIL 🟢 --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            {{-- ----------------------------------------------------------- --}}

        </div>
    </div>

    {{-- MENÚ RESPONSIVO (USANDO LA VARIABLE 'open') --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ __('Panel Principal') }}
        </x-responsive-nav-link>

        {{-- ENLACES DE GESTIÓN (SOLO VISIBLES PARA ROLES 1 Y 2) --}}
        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
            
            {{-- ENLACE AFILIADOS --}}
            <x-responsive-nav-link :href="route('afiliados.index')" :active="request()->routeIs('afiliados.*')">
                {{ __('Afiliados') }}
            </x-responsive-nav-link>
            {{-- NUEVO ENLACE RESPONSIVE: GESTIÓN DE PAGOS (AÑADIDO AQUÍ) --}}
            <x-responsive-nav-link :href="route('pagos.index')" :active="request()->routeIs('pagos.*')">
                {{ __('Gestión de Pagos') }} 
            </x-responsive-nav-link>
            {{-- ENLACE DIRECTIVOS (ROL 1 Y 2) --}}
            <x-responsive-nav-link :href="route('directivos.index')" :active="request()->routeIs('directivos.*')">
                {{ __('Directivos') }}
            </x-responsive-nav-link>
            {{-- NUEVO ENLACE: GESTIÓN DE PERÍODOS/AÑOS --}}
            <x-responsive-nav-link :href="route('gestion.index')" :active="request()->routeIs('gestion.*')">
                {{ __('Gestión de Períodos') }}
            </x-responsive-nav-link>

            {{-- NUEVO ENLACE: GESTIÓN DE MERCADERÍAS (ROL 1 Y 2) --}}
                <x-responsive-nav-link :href="route('mercaderia.index')" :active="request()->routeIs('mercaderia.*')">
                    {{ __('Mercadería') }}
                </x-responsive-nav-link>

            {{-- ENLACE USUARIOS Y ROLES (SOLO ROL 1) --}}
            @if (Auth::user()->role_id == 1)
                <x-responsive-nav-link :href="route('usuarios.index')" :active="request()->routeIs('usuarios.*')">
                    {{ __('Usuarios y Roles') }}
                </x-responsive-nav-link>
            @endif
            
            <div class="border-t border-gray-200 pt-2">
                <div class="px-4 text-sm font-semibold text-gray-500">
                    REPORTES 📊
                </div>

                <x-responsive-nav-link :href="route('reportes.afiliados')" :active="request()->routeIs('reportes.afiliados')">
                    {{ __('Kárdex General') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('reportes.pagos')" :active="request()->routeIs('reportes.pagos')">
                    {{ __('Historial de Pagos') }}
                </x-responsive-nav-link>
                {{-- 🟢 NUEVO: ENLACE RESPONSIVE REPORTE DE DIRECTIVOS --}}
                <x-responsive-nav-link :href="route('reportes.directivos')" :active="request()->routeIs('reportes.directivos')">
                    {{ __('Reporte Directivos') }}
                </x-responsive-nav-link>
                {{-- 🟢 NUEVO: ENLACE RESPONSIVE REPORTE DE USUARIOS (SOLO ROL 1) --}}
                @if (Auth::user()->role_id == 1)
                    <x-responsive-nav-link :href="route('reportes.usuarios')" :active="request()->routeIs('reportes.usuarios')">
                        {{ __('Usuarios y Permisos') }}
                    </x-responsive-nav-link>
                @endif
            </div>
            {{-- ENLACES PARA AFILIADOS (ROL 3) --}}
        @elseif (Auth::user()->role_id == 3)
            
            @php
                // Realizar la verificación de afiliación
                $afiliado = Auth::user()->afiliado;
            @endphp

            @if ($afiliado)
                {{-- Enlace Kárdex solo visible si la afiliación es válida --}}
                <x-responsive-nav-link :href="route('afiliados.show', $afiliado->id_afiliado)" :active="request()->routeIs('afiliados.show')">
                    {{ __('Mi Kárdex') }}
                </x-responsive-nav-link>
            @else
                <span class="text-gray-500 ms-3">Pendiente de Activación</span>
            @endif
            
        @endif
        </div>

        {{-- 🟢 CÓDIGO NUEVO: BLOQUE DE PERFIL RESPONSIVO (NOMBRE Y CERRAR SESIÓN) 🟢 --}}
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Cerrar Sesión') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        {{-- -------------------------------------------------------------------------- --}}

    </div>
</nav>