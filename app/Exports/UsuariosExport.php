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

class UsuariosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $usuarios;
    
    public function __construct($usuarios)
    {
        $this->usuarios = $usuarios;
    }
    
    public function collection()
    {
        return $this->usuarios;
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'Nombre Completo',
            'Email',
            'Rol',
            'Fecha Creación',
            'Última Actualización'  // ✅ AGREGADO
        ];
    }
    
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->role->nombre ?? 'N/A',
            $user->created_at->format('d/m/Y H:i:s'),
            $user->updated_at->format('d/m/Y H:i:s'),  // ✅ AGREGADO
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->usuarios->count() + 1;
        
        // Estilo de encabezados (cambiar A1:E1 por A1:F1)
        $sheet->getStyle('A1:F1')->applyFromArray([  // ✅ CAMBIADO de E1 a F1
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
        
        // Bordes para toda la tabla (cambiar A1:E por A1:F)
        $sheet->getStyle('A1:F' . $lastRow)->applyFromArray([  // ✅ CAMBIADO de E a F
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
            'B' => 30,
            'C' => 35,
            'D' => 20,
            'E' => 20,
            'F' => 20,  // ✅ AGREGADO
        ];
    }
}