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

class SupplierInfoSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    public function __construct(protected $supplier) {}

    public function array(): array
    {
        return [
            ['Supplier Information'],
            [],
            ['Name',         $this->supplier->name],
            ['Total Layups', $this->supplier->layups->count()],
            ['Exported At',  now()->format('d M Y, H:i')],
        ];
    }

    public function title(): string
    {
        return 'Supplier Info';
    }

    public function columnWidths(): array
    {
        return ['A' => 20, 'B' => 38];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws = $event->sheet->getDelegate();
                $ws->setShowGridlines(false);

                $ws->mergeCells('A1:B1');
                $ws->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '111827']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
                    'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '111827']]],
                ]);
                $ws->getRowDimension(1)->setRowHeight(32);
                $ws->getRowDimension(2)->setRowHeight(20);

                foreach ([3, 4, 5] as $row) {
                    $bg = ($row % 2 === 0) ? 'F5F5F5' : 'FFFFFF';
                    $ws->getStyle("A{$row}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '111827']],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                    ]);
                    $ws->getStyle("B{$row}")->applyFromArray([
                        'font'      => ['size' => 10, 'color' => ['rgb' => '374151']],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                    ]);
                    $ws->getRowDimension($row)->setRowHeight(22);
                }

                $ws->getStyle('A3:B5')->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '374151']]],
                ]);
            },
        ];
    }
}
