<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte General de Afiliados (Kárdex)</title>
    {{-- Estilos simples para la versión interactiva y la impresión --}}
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; margin: 0; padding: 20px; }
        h1 { font-size: 24px; text-align: center; margin-bottom: 20px; }
        h2 { font-size: 18px; margin-top: 20px; margin-bottom: 10px; }
        
        /* Estilos generales de la tabla */
        table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        
        /* Estilos para el formulario de filtro (INTERACTIVO) */
        .filtro-container { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; background-color: #f9f9f9; border-radius: 5px; }
        .filtro-group { display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; }
        .filtro-item { flex: 1; min-width: 150px; }
        .filtro-item label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 10px; }
        .filtro-item input, .filtro-item button, .filtro-item a { 
            width: 100%; padding: 5px; border: 1px solid #ccc; border-radius: 3px; box-sizing: border-box; font-size: 10px; 
            text-decoration: none; text-align: center; display: inline-block; cursor: pointer;
        }
        .btn-primary { background-color: #007bff; color: white; border: none; }
        .btn-secondary { background-color: #ccc; color: #333; border: none; }
        .fecha-reporte { text-align: right; font-size: 10px; margin-bottom: 10px; }
        .filtro-resumen { font-size: 12px; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 1px solid #ccc; }

        /* Oculta elementos en la impresión */
        @media print { 
            .no-print { display: none; } 
            body { font-size: 10px; padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: right; margin-bottom: 15px;">
        {{-- Botón para abrir la ventana de impresión del navegador --}}
        <button onclick="window.print()" class="btn-primary" style="padding: 10px;">Imprimir Reporte</button>
        {{-- Envía todos los parámetros de la URL actual (filtros) a la ruta de exportación --}}
    <a href="{{ route('exportar.afiliados.simple', request()->query()) }}" class="btn-secondary" style="padding: 10px; background-color: #107c10; color: white; border-radius: 3px; display: inline-block; text-decoration: none;">
    📊  Descargar Excel (XLSX)
</a>
    </div>

    <h1>REPORTE GENERAL DE AFILIADOS (KÁRDEX)</h1>

    {{-- 🚨 BLOQUE 1: FORMULARIO DE FILTRADO INTERACTIVO --}}
    <div class="no-print filtro-container">
        <h2>Filtro de Afiliados</h2>
        
        {{-- RUTA CORREGIDA: Usando 'reportes.afiliados' --}}
        <form action="{{ route('reportes.afiliados') }}" method="GET">
            <div class="filtro-group">
                
                {{-- Filtro por CI (usa la variable $searchCI) --}}
                <div class="filtro-item">
                    <label for="search_ci">CI:</label>
                    <input type="text" name="search_ci" id="search_ci" inputmode="numeric"     
                                    onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                           value="{{ $searchCI ?? '' }}">
                </div>

                {{-- Filtro por Apellido Paterno (usa la variable $searchApellido) --}}
                <div class="filtro-item">
                    <label for="search_apellido">Apellido P.:</label>
                    <input type="text" name="search_apellido" id="search_apellido" type="text" onkeypress="return !(event.charCode >= 48 && event.charCode <= 57)"
                           value="{{ $searchApellido ?? '' }}">
                </div>

                {{-- Filtro por Nro. Kárdex (usa la variable $searchKardex) --}}
                <div class="filtro-item">
                    <label for="search_kardex">Nro. Kárdex:</label>
                    <input type="text" name="search_kardex" id="search_kardex" inputmode="numeric"     
                                    onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                           value="{{ $searchKardex ?? '' }}">
                </div>

                {{-- Botones --}}
                <div class="filtro-item" style="flex: 0; min-width: 120px;">
                    <button type="submit" class="btn-primary" style="margin-bottom: 5px;">
                        Aplicar Filtro
                    </button>
                    @if ($searchCI || $searchApellido || $searchKardex)
                        {{-- Botón para Limpiar el Filtro --}}
                        {{-- RUTA CORREGIDA: Usando 'reportes.afiliados' --}}
                        <a href="{{ route('reportes.afiliados') }}" class="btn-secondary">
                            Limpiar
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
    {{-- FIN BLOQUE 1 --}}

    {{-- Bloque de Título/Fecha para Impresión --}}
    <div class="fecha-reporte">
        Fecha de Generación: {{ now()->format('d/m/Y H:i:s') }}
    </div>

    {{-- Bloque de Resumen de Filtros aplicado (VISIBLE en impresión) --}}
    <div class="filtro-resumen">
        @if ($searchCI || $searchApellido || $searchKardex)
            <strong>Filtro Aplicado:</strong>
            @if ($searchCI)
                CI: **{{ $searchCI }}** | 
            @endif
            @if ($searchApellido)
                Apellido: **{{ $searchApellido }}** | 
            @endif
            @if ($searchKardex)
                Kárdex: **{{ $searchKardex }}**
            @endif
        @else
            <strong>Filtro:</strong> Todos los registros
        @endif
    </div>


    {{-- 🚨 BLOQUE 2: TABLA DE RESULTADOS --}}
    <table>
        <thead>
            <tr>
                <th>Nro.</th>
                <th>CI</th>
                <th>Apellidos y Nombres</th>
                <th>Kárdex</th>
                <th>Fila</th>
                <th>Medida</th>
                <th>Mercadería</th>
                <th>Gestión Afiliación</th>
                <th>Teléfono</th>
            </tr>
        </thead>
        <tbody>
            {{-- Recorremos la variable $afiliados que enviamos desde el controlador --}}
            @foreach ($afiliados as $index => $afiliado)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $afiliado->ci }}</td>
                    {{-- Concatenamos los apellidos y nombre --}}
                    <td>{{ $afiliado->apellido_paterno }} {{ $afiliado->apellido_materno }} {{ $afiliado->nombre_afiliado }}</td>
                    {{-- Accedemos a las relaciones cargadas (puesto, mercaderia, gestion) --}}
                    <td>{{ $afiliado->puesto->nro_kardex ?? 'N/A' }}</td>
                    <td>{{ $afiliado->puesto->fila ?? 'N/A' }}</td>
                    <td>{{ $afiliado->puesto->medida_puesto ?? 'N/A' }} m</td>
                    <td>{{ $afiliado->mercaderia->clase_mercaderia ?? 'N/A' }}</td>
                    <td>{{ $afiliado->gestion->nombre_gestion ?? 'N/A' }}</td>
                    <td>{{ $afiliado->telefono ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- FIN BLOQUE 2 --}}
    {{-- 🚨 BLOQUE 3: ENLACES DE PAGINACIÓN --}}
    <div class="no-print" style="margin-top: 20px;">
        {{-- Renderiza los enlaces de paginación de Laravel --}}
        {{ $afiliados->appends(request()->query())->links() }}
    </div>
    {{-- FIN BLOQUE 3 --}}

</body>
</html>