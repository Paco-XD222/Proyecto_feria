<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kárdex para Imprimir: {{ $afiliado->nombre_afiliado }}</title>
    
    @vite('resources/css/app.css')

    <style>
        /* Oculta los elementos de navegación y establece márgenes A4 */
        @media print {
            body {
                margin: 0;
            }
            /* Configuración de la página para impresión A4 */
            @page {
                size: A4 portrait;
                margin: 1cm;
            }
            /* Forzar la impresión de colores y fondos */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            /* Asegurar que las imágenes se vean correctamente */
            img {
                max-width: 100%;
                height: auto;
            }
            /* Ocultar cualquier botón o enlace */
            .no-print {
                display: none !important;
            }
            .page-break {
        page-break-before: always;
    }
        }

        /* Estilos para la Marca de Agua (AÑADIDO) */
        .watermark {
            position: fixed; 
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70%; /* Ajusta el tamaño de la imagen */
            height: auto;
            opacity: 0.1; /* Transparencia */
            z-index: -1; 
            pointer-events: none;
        }

        /* Estilos para las secciones de datos */
        .data-section {
            border: 1px solid #e5e7eb; /* gray-200 */
            border-radius: 0.5rem; /* rounded-lg */
            margin-bottom: 1.5rem; /* mb-6 */
            padding: 1.25rem; /* p-5 */
        }
        .data-title {
            font-size: 1.125rem; /* text-xl */
            font-weight: 700; /* font-bold */
            color: #4b5563; /* text-gray-700 */
            margin-bottom: 1rem; /* mb-4 */
            border-bottom: 1px solid #d1d5db; /* border-b */
            padding-bottom: 0.5rem; /* pb-2 */
        }
    </style>
</head>
<body class="bg-white p-6 font-sans text-gray-800">

    {{-- MARCA DE AGUA: Insertada después del body (AÑADIDO) --}}
    @if(isset($afiliado)) 
        <img src="{{ asset('img/fondo_marca_agua.jpg') }}" class="watermark" alt="Fondo de marca de agua">
    @endif
    
    <div id="kardex-document" class="max-w-4xl mx-auto">
        
        <div class="no-print mb-8 flex justify-center sticky top-0 bg-white z-10 p-4 shadow-lg rounded-lg border-b border-gray-200">
            <button 
                onclick="window.print()" 
                class="flex items-center space-x-2 px-6 py-3 text-lg font-bold text-white bg-indigo-600 rounded-full hover:bg-indigo-700 transition duration-150 shadow-xl"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Descargar / Imprimir Kárdex</span>
            </button>
        </div>
        
        {{-- HEADER CORREGIDO: con doble logo y nombre de la feria --}}
        <header class="text-center mb-6 border-b-4 border-indigo-600 pb-3">
            
            <div class="flex items-center justify-center space-x-4 mb-2">
                
                {{-- Logo 1: Escudo de Potosí (RUTA: public/img/escudo_potosi.png) --}}
                <img src="{{ asset('img/escudo_potosi.png') }}" alt="Escudo Potosí" class="w-12 h-12 object-contain">
                
                <div class="flex flex-col">
                    <h2 class="text-lg font-extrabold text-gray-900 uppercase leading-none">
                        ASOCIACIÓN DE COMERCIANTES EN ROPAS Y RAMAS ANEXAS "FERIA POPULAR"
                    </h2>
                    <p class="text-xs text-gray-600 mt-1">
                        Potosí - Bolivia | Personería Jurídica Acreditada Res. Suprema N° 215720
                    </p>
                </div>
                
                {{-- Logo 2: Escudo de la Feria (RUTA: public/img/logo_feria.png) --}}
                <img src="{{ asset('img/logo_feria.jpg') }}" alt="Escudo Feria" class="w-12 h-12 object-contain">
                
            </div>

            <h1 class="text-3xl font-extrabold text-indigo-700 mt-4">KÁRDEX INDIVIDUAL DE AFILIADO</h1>
            <p class="text-base text-gray-600 mt-1">
                C.I. **{{ $afiliado->ci }}** | Fecha de Afiliación: {{ $afiliado->fecha_afiliacion }}
            </p>
        </header>

        <div class="grid grid-cols-12 gap-6">

            {{-- COLUMNA DE ARCHIVOS (4/12) - FOTO y FIRMA --}}
            <div class="col-span-4">
                <div class="data-section bg-indigo-50 border-indigo-300">
                    <h4 class="data-title text-indigo-700 border-indigo-300">🖼️ Documentos Gráficos</h4>
                    
                    {{-- FOTO --}}
                    <div class="mb-5 text-center">
                        <p class="font-medium text-gray-700 mb-2">Foto Afiliado:</p>
                        @if ($afiliado->foto)
                            <img src="{{ asset('storage/' . $afiliado->foto) }}" alt="Foto Afiliado" class="mx-auto w-32 h-32 object-cover rounded-full border-2 border-gray-400 shadow-md">
                        @else
                            <div class="text-sm text-red-500 p-3 bg-white rounded-lg border border-red-300">N/A - Sin Foto</div>
                        @endif
                    </div>
                    
                    {{-- FIRMA MODIFICADA: Eliminada la imagen, solo se deja la nota (CORREGIDO) --}}
                    <div class="text-center mt-6 pt-6 border-t border-indigo-300">
                        <p class="font-medium text-gray-700 mb-2">Firma Digitalizada:</p>
                        @if ($afiliado->firma)
                            <div class="text-sm text-green-600 p-3 bg-white rounded-lg border border-green-300">
                                Firma adjunta en el expediente digital.
                            </div>
                        @else
                            <div class="text-sm text-red-500 p-3 bg-white rounded-lg border border-red-300">N/A - Sin Firma Digital</div>
                        @endif
                        <p class="mt-4 text-sm font-semibold pt-2 text-gray-600 w-3/4 mx-auto">
                            (Solo para archivo digital)
                        </p>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DE DATOS (8/12) --}}
            <div class="col-span-8 space-y-6">

                {{-- 1. INFORMACIÓN PERSONAL Y CONTACTO --}}
                <div class="data-section">
                    <h4 class="data-title">1. Información Personal y Contacto</h4>
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div class="flex flex-col col-span-2">
                            <dt class="font-medium text-gray-500">Nombre Completo</dt>
                            <dd class="font-semibold text-gray-900">{{ $afiliado->nombre_afiliado }} {{ $afiliado->apellido_paterno }} {{ $afiliado->apellido_materno }}</dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="font-medium text-gray-500">Cédula de Identidad</dt>
                            <dd class="font-semibold text-gray-900">{{ $afiliado->ci }}</dd>
                        </div>
                         <div class="flex flex-col">
                            <dt class="font-medium text-gray-500">Fecha Nacimiento</dt>
                            <dd class="font-semibold text-gray-900">{{ $afiliado->fecha_nacimiento }}</dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="font-medium text-gray-500">Teléfono</dt>
                            <dd class="font-semibold text-gray-900">{{ $afiliado->telefono }}</dd>
                        </div>
                         <div class="flex flex-col">
                            <dt class="font-medium text-gray-500">Estado Civil</dt>
                            <dd class="text-gray-900">{{ $afiliado->estado_civil }}</dd>
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
                    </dl>
                </div>

                {{-- 2. DATOS DE PUESTO Y ACTIVIDAD --}}
                <div class="data-section">
                    <h4 class="data-title">2. Datos de Puesto y Actividad</h4>
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div class="flex flex-col">
                            <dt class="font-medium text-gray-500">Nro. Kárdex</dt>
                            <dd class="font-semibold text-gray-900">{{ $afiliado->puesto->nro_kardex ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="font-medium text-gray-500">Nro. Libro</dt>
                            <dd class="font-semibold text-gray-900">{{ $afiliado->puesto->nro_libro ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex flex-col col-span-2">
                            <dt class="font-medium text-gray-500">Mercadería/Clase</dt>
                            <dd class="font-semibold text-gray-900">{{ $afiliado->mercaderia->clase_mercaderia ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex flex-col col-span-2">
                            <dt class="font-medium text-gray-500">Ubicación Completa</dt>
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
                    </dl>
                </div>

               {{-- 3. DATOS HISTÓRICOS Y NOTAS (CORREGIDO Y AMPLIADO) --}}
<div class="data-section page-break">
    <h4 class="data-title">3. Datos Históricos y de Gestión</h4>
    <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
        
        {{-- Campo 1: Gestión de Afiliación --}}
        <div class="flex flex-col">
            <dt class="font-medium text-gray-500">Gestión de Afiliación</dt>
            <dd class="font-semibold text-gray-900">{{ $afiliado->gestion->nombre_gestion ?? 'N/A' }}</dd>
        </div>
        
        {{-- Campo 2: Fecha Afiliación (Nuevo) --}}
        <div class="flex flex-col">
            <dt class="font-medium text-gray-500">Fecha de Afiliación</dt>
            <dd class="font-semibold text-gray-900">{{ $afiliado->fecha_afiliacion }}</dd>
        </div>
        
        {{-- Campo 3: Recarnetización --}}
        <div class="flex flex-col">
            <dt class="font-medium text-gray-500">Recarnetización</dt>
            <dd class="font-semibold text-gray-900">{{ $afiliado->recarnetizacion ?? 'N/A' }}</dd>
        </div>
        
        {{-- Campo 4: Cargo Directivo Histórico (col-span-2) --}}
        <div class="flex flex-col col-span-2">
            <dt class="font-medium text-gray-500">Cargo Directivo Histórico</dt>
            <dd class="font-semibold text-gray-900">{{ $afiliado->cargo_alguna_vez ?? 'Ninguno' }}</dd>
        </div>
        
        {{-- Campo 5: Observaciones (col-span-2, con formato de bloque) --}}
        <div class="flex flex-col col-span-2">
            <dt class="font-medium text-gray-500">Observaciones</dt>
            {{-- Se mantiene el formato de caja para que las observaciones largas se vean limpias --}}
            <dd class="text-gray-900 whitespace-pre-wrap border border-gray-200 p-2 rounded min-h-[50px]">{{ $afiliado->observaciones ?? 'N/A' }}</dd>
        </div>

        {{-- Campo 6: Otros Datos (Nuevo, col-span-2, con formato de bloque si es largo) --}}
        <div class="flex flex-col col-span-2">
            <dt class="font-medium text-gray-500">Otros Datos</dt>
            <dd class="text-gray-900 whitespace-pre-wrap border border-gray-200 p-2 rounded min-h-[50px]">{{ $afiliado->otros ?? 'N/A' }}</dd>
        </div>
        
    </dl>
</div>
            </div> 
        </div> 

        <footer class="text-center pt-4 mt-6 border-t border-gray-400">
            <p class="text-xs text-gray-500">
                Documento generado el {{ date('d/m/Y H:i') }} por el Sistema de Gestión Interna.
            </p>
            <div class="mt-12 text-sm text-gray-700 flex justify-around">
    
                {{-- Bloque de la primera firma --}}
                <div class="text-center mx-4">
                    <p>______________________________________</p>
                    <p class="font-semibold">PRESIDENTE</p>
                </div>

                {{-- Bloque de la segunda firma --}}
                <div class="text-center mx-4">
                    <p>______________________________________</p>
                    <p class="font-semibold">VICEPRESIDENTE</p>
                </div>
                
            </div>

            <div class="mt-12 text-sm text-gray-700 flex justify-around">

                {{-- Bloque de la tercera firma --}}
                <div class="text-center mx-4">
                    <p>______________________________________</p>
                    <p class="font-semibold">SECRETARÍA DE HACIENDA</p>
                </div>

                {{-- Bloque de la cuarta firma --}}
                <div class="text-center mx-4">
                    <p>______________________________________</p>
                    <p class="font-semibold">SECRETARÍA DE ACTAS</p>
                </div>

            </div>

            {{-- BLOQUE: FIRMA Y HUELLA DIGITAL DEL INTERESADO --}}
            <div class="mt-16 flex justify-between items-end">
                
                {{-- Espacio para Firma del Interesado --}}
                <div class="text-center w-1/2">
                    <p>______________________________________</p>
                    <p class="font-semibold">FIRMA DEL INTERESADO</p>
                </div>

                {{-- Espacio para Huella Digital --}}
                <div class="text-center w-1/4 border border-gray-400 p-4 h-20 flex items-center justify-center bg-gray-50">
                    <p class="text-xs font-semibold text-gray-600">HUELLA DIGITAL</p>
                </div>

            </div>
        </footer>
    </div>
    
    <script class="no-print">
        // La impresión se ejecuta al hacer clic en el botón.
    </script>
</body>
</html>