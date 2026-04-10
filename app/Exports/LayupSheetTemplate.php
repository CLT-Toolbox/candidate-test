<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LayupSheetTemplate implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    public function __construct(protected string $sheetName) {}

    public function array(): array
    {
        return [
            ['', '', '', ''],
            ['Fill in the Layup name in cell A1. Add layer data starting from row 4.', '', '', ''],
            ['Layer Order', 'Thickness (mm)', 'Width (mm)', 'Angle (°)'],
            ...array_fill(0, 20, ['', '', '', '']),
        ];
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function columnWidths(): array
    {
        return ['A' => 18, 'B' => 20, 'C' => 20, 'D' => 18, 'E' => 38];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws = $event->sheet->getDelegate();
                $ws->setShowGridlines(false);
                $ws->freezePane('A4');

                $ws->mergeCells('A1:D1');
                $ws->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '111827']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
                    'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '111827']]],
                ]);
                $ws->getRowDimension(1)->setRowHeight(32);

                $ws->setCellValue('E1', '← Enter the Layup name in cell A1');
                $ws->getStyle('E1')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $ws->mergeCells('A2:D2');
                $ws->getStyle('A2')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6B7280']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
                ]);
                $ws->getRowDimension(2)->setRowHeight(18);

                $ws->getStyle('A3:D3')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '111827']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN,   'color' => ['rgb' => '111827']],
                        'outline'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '111827']],
                    ],
                ]);
                $ws->getRowDimension(3)->setRowHeight(24);

                for ($row = 4; $row <= 23; $row++) {
                    $bg = ($row % 2 === 0) ? 'FFFFFF' : 'F5F5F5';
                    $ws->getStyle("A{$row}:D{$row}")->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                    ]);
                    $ws->getRowDimension($row)->setRowHeight(20);
                }

                $ws->getStyle('A3:D23')->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '374151']]],
                ]);

                $ws->setCellValue('E4', '← First data row (Layer Order = 1)');
                $ws->getStyle('E4')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6B7280']],
                ]);
                $ws->setCellValue('E5', '← Add subsequent rows below');
                $ws->getStyle('E5')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '9CA3AF']],
                ]);
            },
        ];
    }
}
