@php
// Esta función es un *reemplazo temporal* para NumberFormatter
// y evita el error "Class "NumberFormatter" not found".
// La solución recomendada a largo plazo es activar la extensión "intl" en php.ini.
function convertirNumeroATexto($number) {
    $formatter = [
        0 => 'cero', 1 => 'un', 2 => 'dos', 3 => 'tres', 4 => 'cuatro', 5 => 'cinco',
        6 => 'seis', 7 => 'siete', 8 => 'ocho', 9 => 'nueve', 10 => 'diez',
        11 => 'once', 12 => 'doce', 13 => 'trece', 14 => 'catorce', 15 => 'quince',
        20 => 'veinte', 30 => 'treinta', 40 => 'cuarenta', 50 => 'cincuenta',
        60 => 'sesenta', 70 => 'setenta', 80 => 'ochenta', 90 => 'noventa',
        100 => 'cien', 200 => 'doscientos', 300 => 'trescientos', 400 => 'cuatrocientos',
        500 => 'quinientos', 600 => 'seiscientos', 700 => 'setecientos',
        800 => 'ochocientos', 900 => 'novecientos'
    ];
    
    $units = ['', 'mil', 'millón', 'mil millones'];
    
    $number = number_format($number, 2, '.', '');
    list($entero, $decimal) = explode('.', $number);
    $entero = (int)$entero;
    $decimal = (int)$decimal;

    if ($entero == 0) return 'cero';

    $text = '';
    $groups = array_reverse(str_split(str_pad($entero, ceil(strlen($entero)/3)*3, '0', STR_PAD_LEFT), 3));
    
    foreach ($groups as $i => $group) {
        $group = (int)$group;
        if ($group === 0) continue;

        $sub = '';
        $h = floor($group / 100) * 100;
        $t = $group % 100;

        if ($h > 0) {
            if ($h === 100 && $t === 0) {
                // Caso exacto 100
                $sub .= 'cien';
            } elseif ($h === 100 && $t > 0) {
                // Caso 101 a 199 (ciento...)
                $sub .= 'ciento';
            } elseif ($t === 0) {
                // Casos exactos 200, 300, 500, etc. (doscientos, trescientos, etc.)
                $sub .= $formatter[$h];
            } else {
                // Casos 201, 301, 501, etc. (doscientos, trescientos, etc. con algo más)
                // Se toma la palabra base (doscientos) y se deja el espacio para las decenas/unidades
                $sub .= $formatter[$h]; 
            }
        }

        // Si $h es mayor a 100 y $t es mayor a 0, $h ya incluye 'tos' (doscientos).
        // Si $h es 100, ya puso 'ciento'.
        
        if ($t > 0) {
            $dec_part = '';
            if ($t <= 15) {
                // 1 a 15 (un, dos, ..., quince)
                $dec_part .= (isset($formatter[$t]) ? ' ' . $formatter[$t] : '');
            } elseif ($t < 20) {
                // 16 a 19 (dieciséis, diecisiete, etc.)
                // No se puede manejar fácilmente con este array, mejor usar lógica:
                // Si el número es 16, la decena es 10 (diez) y la unidad 6 (seis). Lo manejaremos como 'dieci' + unidad
                $unit_str = $formatter[$t % 10];
                if ($t === 16) $dec_part .= ' dieciséis';
                if ($t === 17) $dec_part .= ' diecisiete';
                if ($t === 18) $dec_part .= ' dieciocho';
                if ($t === 19) $dec_part .= ' diecinueve';
            } elseif ($t < 30) {
                // 20 a 29 (veinte, veintiuno, veintidós, etc.)
                if ($t === 20) {
                    $dec_part .= ' ' . $formatter[20];
                } else {
                    $dec_part .= ' veinti' . $formatter[$t % 10]; // 21-29
                }
            } else {
                // 30 a 99 (treinta, cuarenta, ..., noventa y uno, y dos, etc.)
                $dec = floor($t / 10) * 10;
                $uni = $t % 10;
                $dec_part .= (isset($formatter[$dec]) ? ' ' . $formatter[$dec] : '');
                if ($uni > 0) {
                    $dec_part .= ' y ' . $formatter[$uni];
                }
            }
            $sub .= $dec_part;
        }
        
        $sub = trim($sub);
        
        // Ajuste especial para 'ciento' + palabra (ej. 'ciento cincuenta')
        if ($h === 100 && $t > 0 && strpos($sub, 'ciento') !== false) {
             $sub = str_replace('ciento ', 'ciento', $sub);
        }

        if ($i > 0) {
            $unit = $units[$i];
            
            // Singular/Plural para Millones/Billon
            if ($i === 2 && $group > 1) $unit = 'millones';
            if ($i === 2 && $group === 1) $unit = 'millón';

            // Arreglo para "un mil" -> "mil"
            if ($i === 1 && $group === 1) $sub = ''; 
            
            $text = trim($sub) . ($sub ? ' ' : '') . $unit . ($i > 0 && $group > 0 ? ' ' : '') . $text;
        } else {
            $text = trim($sub) . ' ' . $text;
        }
    }
    
    // Limpieza final de espacios extra y casos especiales
    $text = trim(preg_replace('/\s+/', ' ', $text));
    $text = str_replace('ciento doce', 'ciento doce', $text); // Un fix preventivo
    $text = trim($text);

    // Add decimal part (Cambiado para usar la palabra "centavos" en lugar de "/100" según solicitud)
    $centavos = str_pad($decimal, 2, '0', STR_PAD_LEFT);
    $text .= ' con ' . $centavos . ' centavos';

    // Final formatting (Capitalize first letter)
    return ucfirst($text) . ' bolivianos';
}
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pago #{{ $pago->nro_recibo }}</title>
    <style>
        /* --- AJUSTES PARA IMPRESIÓN COMPACTA --- */
        @media print {
            .no-print { display: none; }
            /* Reducimos el margen de impresión a 1.5cm */
            @page { margin: 1.5cm; } 
        }
        
        body {
            font-family: Arial, sans-serif;
            /* Reducimos el ancho máximo de 800px a 600px */
            max-width: 600px; 
            margin: 15px auto;
            padding: 10px; /* Reducimos el padding del body */
            background: #f5f5f5;
        }
        
        .recibo {
            background: white;
            /* Reducimos el padding de 40px a 20px */
            padding: 20px; 
            border: 2px solid #333;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 15px; /* Reducimos el padding inferior */
            margin-bottom: 20px; /* Reducimos el margen inferior */
        }
        
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px; /* Reducimos el tamaño del título */
        }
        
        .header p {
            margin: 3px 0; /* Reducimos el margen entre líneas */
            color: #666;
            font-size: 11px; /* Letra más pequeña para la información de contacto */
        }
        
        .recibo-numero {
            text-align: right;
            font-size: 16px; /* Reducimos ligeramente */
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 15px; 
        }
        
        .info-section {
            margin: 15px 0; /* Reducimos margen de sección */
            font-size: 13px; /* Tamaño de fuente más pequeño para la info */
        }
        
        .info-row {
            display: flex;
            padding: 7px 0; /* Reducimos el padding de la fila */
            border-bottom: 1px solid #eee;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px; /* Reducimos el ancho de la etiqueta */
            color: #555;
        }
        
        .info-value {
            flex: 1;
            color: #333;
        }
        
        .monto-section {
            background: #f8f9fa;
            padding: 15px; /* Reducimos el padding */
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
        }
        
        .monto-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px; 
        }
        
        .monto-valor {
            font-size: 30px; /* Reducimos de 36px a 30px */
            font-weight: bold;
            color: #27ae60;
        }
        
        .footer {
            margin-top: 30px; /* Reducimos el margen superior */
            padding-top: 15px; 
            border-top: 2px solid #333;
        }
        
        .firma-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px; /* Reducimos el espacio para las firmas */
        }
        
        .firma-box {
            text-align: center;
            width: 45%; /* Ajustamos el ancho para que quepan */
            font-size: 12px;
        }
        
        .firma-linea {
            border-top: 2px solid #333;
            margin-bottom: 5px; 
            padding-top: 5px;
        }
        
        .observaciones {
            margin-top: 20px;
            padding: 10px;
            background: #fffacd;
            border-left: 4px solid #ffd700;
            font-style: italic;
            font-size: 12px;
        }
        
        .no-print {
            text-align: center;
            margin: 10px 0;
        }
        
        /* Mantenemos el tamaño de los botones para usabilidad */
        .btn-imprimir, .btn-volver {
            padding: 10px 20px;
            font-size: 14px;
        }
        
        .btn-imprimir {
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-imprimir:hover {
            background: #2980b9;
        }
        
        .btn-volver {
            background: #95a5a6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }
        
        .btn-volver:hover {
            background: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn-imprimir">🖨️ Imprimir Recibo</button>
        <a href="{{ route('pagos.index') }}" class="btn-volver">← Volver al Listado</a>
    </div>

    <div class="recibo">
        <div class="header">
            <h1>RECIBO DE PAGO</h1>
            <p>Asociación de Comerciantes</p>
        </div>

        <div class="recibo-numero">
            Recibo Nº: {{ $pago->nro_recibo ?? 'S/N' }}
        </div>

        <div class="info-section">
            <div class="info-row">
                <div class="info-label">📅 Fecha de Pago:</div>
                <div class="info-value">{{ $pago->fecha_pago->format('d/m/Y') }}</div>
            </div>

            @if($pago->afiliado)
            <div class="info-row">
                <div class="info-label">👤 Recibido de:</div>
                <div class="info-value">
                    {{ $pago->afiliado->nombre_afiliado }} 
                    {{ $pago->afiliado->apellido_paterno }} 
                    {{ $pago->afiliado->apellido_materno }}
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">🆔 C.I.:</div>
                <div class="info-value">{{ $pago->afiliado->ci }}</div>
            </div>

            @if($pago->afiliado->puesto)
            <div class="info-row">
                <div class="info-label">🏪 Nº Kardex:</div>
                <div class="info-value">{{ $pago->afiliado->puesto->nro_kardex }}</div>
            </div>
            @endif
            @else
            <div class="info-row">
                <div class="info-label">👤 Recibido de:</div>
                <div class="info-value" style="color: #999; font-style: italic;">
                    Información del afiliado no disponible
                </div>
            </div>
            @endif

            <div class="info-row">
                <div class="info-label">📝 Concepto:</div>
                <div class="info-value">{{ $pago->concepto }}</div>
            </div>
        </div>

        <div class="monto-section">
            <div class="monto-label">MONTO TOTAL</div>
            <div class="monto-valor">Bs. {{ number_format($pago->monto, 2) }}</div>
            <div style="margin-top: 10px; font-size: 14px; color: #666;">
                ({{ convertirNumeroATexto($pago->monto) }})
            </div>
        </div>

        <div class="footer">
            <div class="info-row">
                <div class="info-label">🧑‍💼 Registrado por:</div>
                <div class="info-value">{{ $pago->user->name ?? 'Sistema' }}</div>
            </div>

            <div class="firma-section">
                <div class="firma-box">
                    <div class="firma-linea">Firma del Afiliado</div>
                </div>
                <div class="firma-box">
                    <div class="firma-linea">Firma y Sello</div>
                </div>
            </div>
        </div>

        <div class="observaciones">
            <strong>Nota:</strong> Este recibo es válido como comprobante de pago. 
            Conserve este documento para futuros trámites.
        </div>
    </div>
</body>
</html>