<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte General de Usuarios</title>
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
        .filtro-item input, .filtro-item select, .filtro-item button, .filtro-item a { 
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
       <a href="{{ route('exportar.usuarios.simple', request()->query()) }}" class="btn-primary" style="padding: 10px; background-color: #107c10; color: white; border: none; border-radius: 3px; display: inline-block; text-decoration: none;">
    📊 Descargar Excel (.xlsx)
</a>
    </div>

    <h1>REPORTE GENERAL DE USUARIOS DEL SISTEMA</h1>

    {{-- BLOQUE 1: FORMULARIO DE FILTRADO INTERACTIVO --}}
    <div class="no-print filtro-container">
        <h2>Filtro de Usuarios</h2>
        
        {{-- Usamos la ruta 'reportes.usuarios' --}}
        <form action="{{ route('reportes.usuarios') }}" method="GET">
            <div class="filtro-group">
                
                {{-- Filtro por Nombre --}}
                <div class="filtro-item">
                    <label for="search_name">Nombre:</label>
                    <input type="text" name="search_name" id="search_name" type="text" onkeypress="return !(event.charCode >= 48 && event.charCode <= 57)"
                            value="{{ $searchName ?? '' }}">
                </div>

                {{-- Filtro por Email --}}
                <div class="filtro-item">
                    <label for="search_email">Email:</label>
                    <input type="email" name="search_email" id="search_email" 
                            value="{{ $searchEmail ?? '' }}">
                </div>

                {{-- Filtro por Rol (Selector) --}}
                <div class="filtro-item">
                    <label for="search_role">Rol:</label>
                    <select name="search_role" id="search_role">
                        <option value="">-- Todos los Roles --</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" 
                                    {{ ($searchRole == $role->id) ? 'selected' : '' }}>
                                {{ $role->nombre }} {{-- ✅ Nombre del Rol CORREGIDO (1/3) --}}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Botones --}}
                <div class="filtro-item" style="flex: 0; min-width: 120px;">
                    <button type="submit" class="btn-primary" style="margin-bottom: 5px;">
                        Aplicar Filtro
                    </button>
                    @if ($searchName || $searchRole || $searchEmail)
                        {{-- Botón para Limpiar el Filtro --}}
                        <a href="{{ route('reportes.usuarios') }}" class="btn-secondary">
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
        @if ($searchName || $searchRole || $searchEmail)
            <strong>Filtro Aplicado:</strong>
            @if ($searchName)
                Nombre: **{{ $searchName }}** | 
            @endif
            @if ($searchEmail)
                Email: **{{ $searchEmail }}** | 
            @endif
            @if ($searchRole)
                Rol: **{{ $roles->find($searchRole)->nombre ?? 'N/A' }}** {{-- ✅ Nombre del Rol CORREGIDO (2/3) --}}
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
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Fecha Creación</th>
                <th>Última Actualización</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($usuarios as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    {{-- Usamos la relación role cargada --}}
                    <td>{{ $user->role->nombre ?? 'N/A' }}</td> {{-- ✅ Nombre del Rol CORREGIDO (3/3) --}}
                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No se encontraron usuarios que coincidan con los filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{-- FIN BLOQUE 2 --}}

</body>
</html>