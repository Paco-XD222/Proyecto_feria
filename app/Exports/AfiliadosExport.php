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

class AfiliadosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $afiliados;
    
    public function __construct($afiliados)
    {
        $this->afiliados = $afiliados;
    }
    
    public function collection()
    {
        return $this->afiliados;
    }
    
    public function headings(): array
    {
        return [
            'Nro.',
            'CI',
            'Apellidos y Nombres',
            'Kárdex',
            'Fila',
            'Medida',
            'Mercadería',
            'Gestión Afiliación',
            'Teléfono'
        ];
    }
    
    public function map($afiliado): array
    {
        static $contador = 0;
        $contador++;
        
        $nombreCompleto = $afiliado->apellido_paterno . ' ' . 
                          $afiliado->apellido_materno . ' ' . 
                          $afiliado->nombre_afiliado;
        
        return [
            $contador,
            $afiliado->ci,
            $nombreCompleto,
            $afiliado->puesto->nro_kardex ?? 'N/A',
            $afiliado->puesto->fila ?? 'N/A',
            ($afiliado->puesto->medida_puesto ?? 'N/A') . ' m',
            $afiliado->mercaderia->clase_mercaderia ?? 'N/A',
            $afiliado->gestion->nombre_gestion ?? 'N/A',
            $afiliado->telefono ?? 'N/A',
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->afiliados->count() + 1;
        
        // Estilo de encabezados
        $sheet->getStyle('A1:I1')->applyFromArray([
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
        $sheet->getStyle('A1:I' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        
        return [];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 15,
            'C' => 35,
            'D' => 15,
            'E' => 10,
            'F' => 12,
            'G' => 25,
            'H' => 20,
            'I' => 15,
        ];
    }
}