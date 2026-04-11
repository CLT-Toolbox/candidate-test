<?php

namespace App\Exports;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LayupSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    public function __construct(protected $layup) {}

    public function array(): array
    {
        $rows   = [];
        $rows[] = [$this->layup->name];
        $rows[] = ['Total Layers', $this->layup->layers->count()];
        $rows[] = ['No', 'Layer Order', 'Thickness (mm)', 'Width (mm)', 'Angle (°)'];

        $no = 1;
        foreach ($this->layup->layers->sortBy('layer_order') as $layer) {
            $rows[] = [$no++, $layer->layer_order, $layer->thickness, $layer->width, $layer->angle];
        }

        return $rows;
    }

    public function title(): string
    {
        return Str::limit($this->layup->name, 28, '...');
    }

    public function columnWidths(): array
    {
        return ['A' => 14, 'B' => 16, 'C' => 20, 'D' => 16, 'E' => 14];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws       = $event->sheet->getDelegate();
                $lastRow  = $this->layup->layers->count() + 3;
                $ws->setShowGridlines(false);

                $ws->mergeCells('A1:E1');
                $ws->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '111827']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
                    'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '111827']]],
                ]);
                $ws->getRowDimension(1)->setRowHeight(32);

                $ws->getStyle('A2')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '374151']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
                ]);
                $ws->getStyle('B2')->applyFromArray([
                    'font'      => ['size' => 10, 'color' => ['rgb' => '374151']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
                ]);
                $ws->getRowDimension(2)->setRowHeight(20);

                $ws->getStyle('A3:E3')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '111827']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '111827']]],
                ]);
                $ws->getRowDimension(3)->setRowHeight(24);

                for ($row = 4; $row <= $lastRow; $row++) {
                    $bg = ($row % 2 === 0) ? 'FFFFFF' : 'F5F5F5';
                    $ws->getStyle("A{$row}:E{$row}")->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                    ]);
                    $ws->getRowDimension($row)->setRowHeight(20);
                }

                $ws->getStyle("A3:E{$lastRow}")->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '374151']]],
                ]);

                $ws->getStyle("A4:A{$lastRow}")->applyFromArray([
                    'font'      => ['color' => ['rgb' => '9CA3AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            },
        ];
    }
}
