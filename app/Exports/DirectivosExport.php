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

class DirectivosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $directivos;
    
    public function __construct($directivos)
    {
        $this->directivos = $directivos;
    }
    
    public function collection()
    {
        return $this->directivos;
    }
    
    public function headings(): array
    {
        return [
            'Nro.',
            'CI Afiliado',
            'Apellidos y Nombres',
            'Cargo',
            'Gestión',
            'Posesión',
            'Conclusión',
            'Observaciones'
        ];
    }
    
    public function map($directivo): array
    {
        static $contador = 0;
        $contador++;
        
        $nombreCompleto = $directivo->apellido_paterno_directivo . ' ' . 
                          $directivo->apellido_materno_directivo . ' ' . 
                          $directivo->nombre_directivo;
        
        return [
            $contador,
            $directivo->afiliado->ci ?? 'N/A',
            $nombreCompleto,
            $directivo->cargo_directivo,
            $directivo->gestion->nombre_gestion ?? 'N/A',
            \Carbon\Carbon::parse($directivo->fecha_posesion)->format('d/m/Y'),
            $directivo->fecha_conclusion ? \Carbon\Carbon::parse($directivo->fecha_conclusion)->format('d/m/Y') : 'Vigente',
            $directivo->observaciones ?? 'Sin obs.',
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->directivos->count() + 1;
        
        // Estilo de encabezados
        $sheet->getStyle('A1:H1')->applyFromArray([
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
        $sheet->getStyle('A1:H' . $lastRow)->applyFromArray([
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
            'D' => 25,
            'E' => 20,
            'F' => 15,
            'G' => 15,
            'H' => 30,
        ];
    }
}