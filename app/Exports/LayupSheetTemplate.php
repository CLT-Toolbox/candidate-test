<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LayupSheetTemplate implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    public function __construct(protected string $sheetName) {}

    public function array(): array
    {
        return [
            ['', '', '', ''],
            ['Nama Layup diisi di cell A1. Isi data layer mulai baris ke-4.', '', '', ''],
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
        return ['A' => 18, 'B' => 20, 'C' => 20, 'D' => 18, 'E' => 35];
    }

    public function styles($sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws = $event->sheet->getDelegate();

                $ws->freezePane('A4');

                $ws->mergeCells('A1:D1');
                $ws->mergeCells('A2:D2');

                $ws->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1E3A5F']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
                    'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1A56DB']]],
                ]);
                $ws->getRowDimension(1)->setRowHeight(32);

                $ws->setCellValue('E1', '← Isi nama Layup di cell A1');
                $ws->getStyle('E1')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $ws->getStyle('A2')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6B7280']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
                ]);
                $ws->getRowDimension(2)->setRowHeight(18);

                $headerStyles = [
                    'A3' => '1A56DB',
                    'B3' => '0369A1',
                    'C3' => '0369A1',
                    'D3' => '1A56DB',
                ];
                foreach ($headerStyles as $cell => $bg) {
                    $ws->getStyle($cell)->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => [
                            'top'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1E40AF']],
                            'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1E40AF']],
                        ],
                    ]);
                }
                $ws->getStyle('E3')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $ws->setCellValue('E3', 'Keterangan');
                $ws->getRowDimension(3)->setRowHeight(24);

                for ($row = 4; $row <= 23; $row++) {
                    $bg = ($row % 2 === 0) ? 'F0F9FF' : 'FFFFFF';
                    $ws->getStyle("A{$row}:D{$row}")->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'CBD5E1']]],
                    ]);
                    $ws->getRowDimension($row)->setRowHeight(20);
                }

                $ws->setCellValue('E4', '← Baris pertama data (Layer Order = 1)');
                $ws->getStyle('E4')->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('3B82F6');
                $ws->setCellValue('E5', '← Tambahkan baris berikutnya di sini');
                $ws->getStyle('E5')->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('9CA3AF');

                $ws->setShowGridlines(false);
            },
        ];
    }
}
