<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SupplierInfoSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(protected $supplier) {}

    public function array(): array
    {
        return [
            ['Supplier Information'],           
            [],                                 
            ['Name',        $this->supplier->name],
            ['Total Layups', $this->supplier->layups->count()],
            ['Exported At', now()->format('d M Y, H:i')],
        ];
    }

    public function title(): string
    {
        return 'Supplier Info';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 35,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color'       => ['rgb' => '333333'],
                ],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->getStyle('A3:A5')->applyFromArray([
            'font' => ['bold' => true],
        ]);

        foreach (range(3, 5) as $row) {
            $sheet->getRowDimension($row)->setRowHeight(20);
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'DDDDDD'],
                    ],
                ],
            ]);
        }

        $sheet->getStyle('A1:B5')->applyFromArray([
            'alignment' => [
                'indent' => 1,
            ],
        ]);
    }
}
