<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PagosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $pagos;
    
    public function __construct($pagos)
    {
        $this->pagos = $pagos;
    }
    
    public function collection()
    {
        return $this->pagos;
    }
    
    public function headings(): array
    {
        return [
            'ID Pago',
            'CI Afiliado',
            'Nro. Kardex',
            'Afiliado',
            'Concepto',
            'Monto (Bs)',
            'Fecha Pago',
            'Nro. Recibo',
            'Registrado Por',
            'Fecha Creación'
        ];
    }
    
    public function map($pago): array
    {
        $nombreAfiliado = ($pago->afiliado->apellido_paterno ?? '') . ' ' . 
                          ($pago->afiliado->apellido_materno ?? '') . ', ' . 
                          ($pago->afiliado->nombre_afiliado ?? '');
        
        $nroKardex = $pago->afiliado->puesto->nro_kardex ?? 'N/A';
        
        return [
            $pago->id,
            $pago->afiliado->ci ?? 'N/A',
            $nroKardex,
            trim($nombreAfiliado, ', '),
            $pago->concepto,
            $pago->monto,
            \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y'),
            $pago->nro_recibo ?? 'N/A',
            $pago->user->name ?? 'N/A',
            $pago->created_at->format('d/m/Y H:i:s'),
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->pagos->count() + 1;
        
        // Estilo de encabezados
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Bordes para toda la tabla
        $sheet->getStyle('A1:J' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        
        // Formato de moneda para columna F
        $sheet->getStyle('F2:F' . $lastRow)
              ->getNumberFormat()
              ->setFormatCode('#,##0.00');
        
        return [];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 15,
            'C' => 15,
            'D' => 35,
            'E' => 30,
            'F' => 12,
            'G' => 15,
            'H' => 15,
            'I' => 20,
            'J' => 20,
        ];
    }
}