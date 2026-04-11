<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class InstructionSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    public function array(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Guide';
    }

    public function columnWidths(): array
    {
        return ['A' => 4, 'B' => 30, 'C' => 55, 'D' => 4];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws = $event->sheet->getDelegate();
                $ws->setShowGridlines(false);

                $this->banner($ws);

                $this->section($ws, 4, 'FILE STRUCTURE');
                $this->row($ws, 5,  '1 Sheet = 1 Layup',         'Each tab (sheet) in the Excel file represents one Layup. If you have 3 Layups, create 3 sheets.', 'F9FAFB');
                $this->row($ws, 6,  'Sheet Name',                 'Sheet name can be anything. Example: "Sheet1", "Layup-A", etc.', 'FFFFFF');
                $this->row($ws, 7,  'Row 1 — Layup Name',         'Fill CELL A1 with the name of your Layup. This is what will be saved to the system.', 'F9FAFB');
                $this->row($ws, 8,  'Row 2 — Hint',               'This row is a hint for filling in the data. It is not read by the system.', 'FFFFFF');
                $this->row($ws, 9,  'Row 3 — Column Headers',     'This row contains column titles. Do NOT modify or delete it.', 'F9FAFB');
                $this->row($ws, 10, 'Row 4+ — Layer Data',        'Fill in layer data starting from row 4. One row = one layer.', 'FFFFFF');

                $this->section($ws, 12, 'COLUMN REFERENCE');
                $this->row($ws, 13, 'Column A — Layer Order',     'The sequence number of the layer. Must be a WHOLE NUMBER (1, 2, 3, ...). No duplicates allowed within one layup.', 'F9FAFB');
                $this->row($ws, 14, 'Column B — Thickness',       'Material thickness in mm. Decimal values are allowed. Example: 0.5 or 1.25', 'FFFFFF');
                $this->row($ws, 15, 'Column C — Width',           'Material width in mm. Example: 100 or 125.5', 'F9FAFB');
                $this->row($ws, 16, 'Column D — Angle',           'Fiber orientation angle in degrees (°). Example: 0, 45, 90, -45. Negative values are allowed.', 'FFFFFF');

                $this->section($ws, 18, 'IMPORTANT RULES');
                $this->row($ws, 19, 'Do not delete Row 3',        'The header row must remain. The system uses it as a data start marker.', 'F9FAFB');
                $this->row($ws, 20, 'Do not leave Cell A1 empty', 'Cell A1 is the Layup name. If empty, the sheet will be skipped by the system.', 'FFFFFF');
                $this->row($ws, 21, 'All columns are required',   'Every data row must be complete (columns A–D). Incomplete rows will be skipped.', 'F9FAFB');
                $this->row($ws, 22, 'Use numbers, not text',      'Columns B, C, D must contain NUMBERS only. Do not write "0.5 mm", just write "0.5".', 'FFFFFF');
                $this->row($ws, 23, 'Layer Order must be unique', 'No two rows in the same layup may share the same Layer Order value.', 'F9FAFB');

                $this->section($ws, 25, 'UNDERSTANDING CONFLICTS');
                $this->row($ws, 26, 'When does a conflict occur?', 'A conflict occurs when imported data has the same Layer Order as an existing record, but with different Thickness, Width, or Angle values.', 'F9FAFB');
                $this->row($ws, 27, 'What happens next?',          'The system will display a Conflict Resolution screen — existing data and incoming data are shown side by side.', 'FFFFFF');
                $this->row($ws, 28, 'Your options',                '"Keep Existing" → the current data is retained.  "Accept Incoming" → the new data replaces the current data.', 'F9FAFB');
                $this->row($ws, 29, 'Data without conflicts',      'Layers with no conflicts will be imported automatically without any action required.', 'FFFFFF');
            },
        ];
    }

    private function fill(string $hex): array
    {
        return ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $hex]];
    }

    private function banner($ws): void
    {
        $ws->mergeCells('B1:C1');
        $ws->setCellValue('B1', 'IMPORT GUIDE — LAYUP TEMPLATE');
        $ws->getStyle('B1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => $this->fill('111827'),
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(1)->setRowHeight(36);

        $ws->mergeCells('B2:C2');
        $ws->setCellValue('B2', 'Read this guide before filling in the template to ensure a smooth import process.');
        $ws->getStyle('B2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '374151']],
            'fill'      => $this->fill('F5F5F5'),
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(2)->setRowHeight(22);
        $ws->getRowDimension(3)->setRowHeight(10);
    }

    private function section($ws, int $row, string $title): void
    {
        $ws->mergeCells("B{$row}:C{$row}");
        $ws->setCellValue("B{$row}", "  {$title}");
        $ws->getStyle("B{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => $this->fill('374151'),
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '111827']]],
        ]);
        $ws->getRowDimension($row)->setRowHeight(26);
    }

    private function row($ws, int $row, string $label, string $desc, string $bg): void
    {
        $border = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']];
        $fill   = $this->fill($bg);

        $ws->setCellValue("B{$row}", $label);
        $ws->getStyle("B{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '111827']],
            'fill'      => $fill,
            'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true, 'indent' => 1],
            'borders'   => ['allBorders' => $border],
        ]);

        $ws->setCellValue("C{$row}", $desc);
        $ws->getStyle("C{$row}")->applyFromArray([
            'font'      => ['size' => 10, 'color' => ['rgb' => '374151']],
            'fill'      => $fill,
            'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true, 'indent' => 1],
            'borders'   => ['allBorders' => $border],
        ]);

        $ws->getRowDimension($row)->setRowHeight(42);
    }
}
