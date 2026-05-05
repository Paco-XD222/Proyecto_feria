<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte General de Directivos</title>
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
        .filtro-item input, .filtro-item button, .filtro-item select, .filtro-item a { 
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
        <button onclick="window.print()" class="btn-primary" style="padding: 10px;">Imprimir Reporte</button>
        {{-- Envía todos los parámetros de la URL actual (filtros) a la ruta de exportación --}}
  <a href="{{ route('exportar.directivos.simple', request()->query()) }}" class="btn-primary" style="padding: 10px; background-color: #107c10; color: white; border: none; border-radius: 4px; display: inline-block; text-decoration: none;">
    📊 Descargar Excel (.xlsx)
</a>
    </div>

    <h1>REPORTE GENERAL DE DIRECTIVOS</h1>

    {{-- BLOQUE 1: FORMULARIO DE FILTRADO INTERACTIVO --}}
    <div class="no-print filtro-container">
        <h2>Filtro de Directivos</h2>
        
        <form action="{{ route('reportes.directivos') }}" method="GET">
            <div class="filtro-group">
                
                {{-- Filtro por Cargo (usa la variable $searchCargo) --}}
                <div class="filtro-item">
                    <label for="search_cargo">Cargo:</label>
                    <input type="text" name="search_cargo" id="search_cargo" type="text" onkeypress="return !(event.charCode >= 48 && event.charCode <= 57)"
                            value="{{ $searchCargo ?? '' }}">
                </div>

                {{-- Filtro por Gestión (usa la variable $searchGestion) --}}
                <div class="filtro-item">
                    <label for="search_gestion">Gestión:</label>
                    <select name="search_gestion" id="search_gestion">
                        <option value="">-- Todas las Gestiones --</option>
                        {{-- Recorremos las gestiones disponibles --}}
                        @foreach ($gestiones as $gestion)
                            <option value="{{ $gestion->id_gestion }}" 
                                {{ ($searchGestion == $gestion->id_gestion) ? 'selected' : '' }}>
                                {{ $gestion->nombre_gestion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Botones --}}
                <div class="filtro-item" style="flex: 0; min-width: 120px;">
                    <button type="submit" class="btn-primary" style="margin-bottom: 5px;">
                        Aplicar Filtro
                    </button>
                    @if ($searchCargo || $searchGestion)
                        {{-- Botón para Limpiar el Filtro --}}
                        <a href="{{ route('reportes.directivos') }}" class="btn-secondary">
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
        @if ($searchCargo || $searchGestion)
            <strong>Filtro Aplicado:</strong>
            @if ($searchCargo)
                Cargo: **{{ $searchCargo }}** | 
            @endif
            @if ($searchGestion)
                Gestión: **{{ $gestiones->firstWhere('id_gestion', $searchGestion)->nombre_gestion ?? 'N/A' }}**
            @endif
        @else
            <strong>Filtro:</strong> Todos los registros
        @endif
    </div>


    {{-- BLOQUE 2: TABLA DE RESULTADOS --}}
    <table>
        <thead>
            <tr>
                <th>Nro.</th>
                <th>CI Afiliado</th>
                <th>Apellidos y Nombres (Directivo)</th>
                <th>Cargo</th>
                <th>Gestión</th>
                <th>Posesión</th>
                <th>Conclusión</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            {{-- Recorremos la variable $directivos --}}
            @foreach ($directivos as $index => $directivo)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    {{-- Usamos la relación 'afiliado' --}}
                    <td>{{ $directivo->afiliado->ci ?? 'N/A' }}</td>
                    {{-- Concatenamos los apellidos y nombre del Directivo --}}
                    <td>{{ $directivo->apellido_paterno_directivo }} {{ $directivo->apellido_materno_directivo }} {{ $directivo->nombre_directivo }}</td>
                    <td>{{ $directivo->cargo_directivo }}</td>
                    {{-- Usamos la relación 'gestion' --}}
                    <td>{{ $directivo->gestion->nombre_gestion ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($directivo->fecha_posesion)->format('d/m/Y') }}</td>
                    <td>{{ $directivo->fecha_conclusion ? \Carbon\Carbon::parse($directivo->fecha_conclusion)->format('d/m/Y') : 'Vigente' }}</td>
                    <td>{{ $directivo->observaciones ?? 'Sin obs.' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- FIN BLOQUE 2 --}}

</body>
</html>