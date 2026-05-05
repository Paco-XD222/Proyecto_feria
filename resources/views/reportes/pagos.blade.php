<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Historial de Pagos</title>
    {{-- Agregamos un poco de Tailwind para mejor estilo interactivo, si no usas Tailwind, estos estilos no se verán bien --}}
    {{-- Si no usas Tailwind, la parte de filtros se verá con estilos básicos, pero será funcional --}}
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; margin: 0; padding: 20px; }
        h1 { font-size: 24px; text-align: center; margin-bottom: 20px; }
        h2 { font-size: 18px; margin-top: 20px; margin-bottom: 10px; }
        
        /* Estilos de la tabla de impresión (mantener) */
        table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .total { font-weight: bold; background-color: #e0ffe0; }
        
        /* Estilos para el formulario de filtro (INTERACTIVO) */
        .filtro-container { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; background-color: #f9f9f9; border-radius: 5px; }
        .filtro-group { display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; }
        .filtro-item { flex: 1; min-width: 200px; }
        .filtro-item label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 12px; }
        .filtro-item input, .filtro-item button, .filtro-item a { 
            width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 12px; 
            text-decoration: none; text-align: center; display: inline-block; cursor: pointer;
        }
        .btn-primary { background-color: #007bff; color: white; border: none; }
        .btn-secondary { background-color: #ccc; color: #333; border: none; }

        /* Para impresión */
        .fecha-reporte { text-align: right; font-size: 10px; margin-bottom: 10px; }
        @media print { 
            .no-print { display: none; } 
            body { font-size: 10px; padding: 0; }
        }
    </style>
</head>
<body>
    
    <h1>REPORTE DE HISTORIAL DE PAGOS</h1>

    {{-- BLOQUE DE FILTRADO INTERACTIVO (NO SE IMPRIME) --}}
    <div class="no-print filtro-container">
        <h2>Aplicar Filtro</h2>
        
        {{-- El formulario apunta a la misma URL (Reporte de Pagos) --}}
        <form action="{{ route('reportes.pagos') }}" method="GET">
            <div class="filtro-group">
                
                {{-- Filtro por Afiliado (CI) --}}
                <div class="filtro-item">
                    <label for="afiliado_ci">CI del Afiliado:</label>
                    <input type="text" name="afiliado_ci" id="afiliado_ci" inputmode="numeric"     
                                    onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                           value="{{ $searchAfiliado ?? '' }}">
                </div>

                {{-- Filtro por Fecha de Inicio --}}
                <div class="filtro-item">
                    <label for="fecha_inicio">Fecha Desde:</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" 
                           value="{{ $fechaInicio ?? '' }}">
                </div>

                {{-- Filtro por Fecha de Fin --}}
                <div class="filtro-item">
                    <label for="fecha_fin">Fecha Hasta:</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" 
                           value="{{ $fechaFin ?? '' }}">
                </div>

                {{-- Botones --}}
                <div class="filtro-item" style="flex: 0; min-width: 120px;">
                    <button type="submit" class="btn-primary" style="margin-bottom: 5px;">
                        Aplicar Filtro
                    </button>
                    @if ($searchAfiliado || $fechaInicio || $fechaFin)
                        {{-- Botón para Limpiar el Filtro --}}
                        <a href="{{ route('reportes.pagos') }}" class="btn-secondary">
                            Limpiar Filtro
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
    {{-- FIN BLOQUE DE FILTRADO --}}

    {{-- Resumen de Totales y Botones de Acción (NO SE IMPRIME) --}}
<div class="no-print" style="text-align: right; margin-bottom: 15px;">
    
     <!-- <span style="font-weight: bold; margin-right: 20px; font-size: 14px;">TOTAL RECAUDADO: Bs. {{ number_format($montoTotal, 2) }}</span>-->
    
        <button onclick="window.print()" class="btn-primary" style="padding: 10px; margin-right: 10px;">Imprimir Reporte</button>
    
        {{-- Envía todos los parámetros de la URL actual (filtros) a la ruta de exportación --}}
    <a href="{{ route('exportar.pagos.simple', request()->query()) }}" class="btn-primary" style="padding: 10px; background-color: #107c10; color: white; border: none; border-radius: 4px; display: inline-block; text-decoration: none;">
    📊 Descargar Excel (.xlsx)
</a>
</div>

    {{-- Bloque de Título/Fecha para Impresión --}}
    <div class="fecha-reporte">
        Fecha de Generación: {{ now()->format('d/m/Y H:i:s') }}
    </div>

    {{-- Bloque para mostrar el Resumen de Filtros aplicado (SOLO PARA IMPRESIÓN) --}}
    <div class="no-print-interactivo" style="font-size: 12px; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 1px solid #ccc;">
        @if ($searchAfiliado || $fechaInicio || $fechaFin)
            <p><strong>Filtro Aplicado:</strong>
            @if ($searchAfiliado)
                Afiliado (CI): **{{ $searchAfiliado }}** | 
            @endif
            @if ($fechaInicio)
                Desde: **{{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}** | 
            @endif
            @if ($fechaFin)
                Hasta: **{{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}**
            @endif
            </p>
        @else
            <p><strong>Filtro:</strong> Todos los registros</p>
        @endif
    </div>

    {{-- TABLA DE RESULTADOS --}}
    <table>
        <thead>
            <tr>
                <th>Nro.</th>
                <th>Kárdex</th>
                <th>Afiliado (CI)</th>
                <th>Fecha Pago</th>
                <th>Concepto</th>
                <th>Recibo Nro.</th>
                <th style="text-align: right;">Monto (Bs.)</th>
                <th>Registrado por</th>
            </tr>
        </thead>
        <tbody>
            @php $count = 1; @endphp
            {{-- Recorremos la variable $pagos --}}
            @foreach ($pagos as $pago)
                <tr>
                    <td>{{ $count++ }}</td>
                    <td>{{ $pago->afiliado->puesto->nro_kardex ?? 'N/A' }}</td>
                    <td>
                        {{ $pago->afiliado->apellido_paterno }} {{ $pago->afiliado->apellido_materno }}, {{ $pago->afiliado->nombre_afiliado }} 
                        <span style="font-size: 9px; display: block;">CI: {{ $pago->afiliado->ci }}</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                    <td>{{ $pago->concepto }}</td>
                    <td>{{ $pago->nro_recibo ?? 'N/A' }}</td>
                    <td style="text-align: right;">{{ number_format($pago->monto, 2) }}</td>
                    <td>{{ $pago->user->name ?? 'Sistema' }}</td>
                </tr>
            @endforeach
            {{-- Fila de Total (usa la variable $montoTotal del controlador) --}}
            <!--
<tr class="total">
    <td colspan="6" style="text-align: right;">TOTAL DE INGRESOS REGISTRADOS:</td>
    <td style="text-align: right;">Bs. {{ number_format($montoTotal, 2) }}</td>
    <td></td>
</tr>
-->

        </tbody>
    </table>

</body>
</html>