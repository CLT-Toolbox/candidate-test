<?php

namespace App\Exports;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LayupSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(protected $layup) {}

    public function array(): array
    {
        $rows = [];
        $rows[] = [$this->layup->name];
        $rows[] = ['Total Layers', $this->layup->layers->count()];
        $rows[] = ['No', 'Layer Order', 'Thickness (mm)', 'Width (mm)', 'Angle (°)'];

        $no = 1;
        foreach ($this->layup->layers->sortBy('layer_order') as $layer) {
            $rows[] = [
                $no++,
                $layer->layer_order,
                $layer->thickness,
                $layer->width,
                $layer->angle,
            ];
        }

        return $rows;
    }

    public function title(): string
    {
        return Str::limit($this->layup->name, 28, '...');
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14,
            'B' => 16,
            'C' => 20,
            'D' => 16,
            'E' => 14,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $totalLayers = $this->layup->layers->count();
        $lastRow     = $totalLayers + 3;

        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'indent'     => 1,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color'       => ['rgb' => '333333'],
                ],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['bold' => true],
            'alignment' => ['indent' => 1],
        ]);
        $sheet->getStyle('B2')->applyFromArray([
            'alignment' => ['indent' => 1],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        $sheet->getStyle('A3:E3')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '404040'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '404040'],
                ],
            ],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(24);

        for ($row = 4; $row <= $lastRow; $row++) {
            $bgColor = ($row % 2 === 0) ? 'FFFFFF' : 'F5F5F5';

            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $bgColor],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'E0E0E0'],
                    ],
                    'left' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'E0E0E0'],
                    ],
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'E0E0E0'],
                    ],
                ],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(20);
        }

        $sheet->getStyle("A3:E{$lastRow}")->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color'       => ['rgb' => '333333'],
                ],
            ],
        ]);

        $sheet->getStyle("A4:A{$lastRow}")->applyFromArray([
            'font'      => ['color' => ['rgb' => '888888']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }
}
