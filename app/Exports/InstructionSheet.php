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
        return 'Petunjuk';
    }
    public function columnWidths(): array
    {
        return ['A' => 4, 'B' => 28, 'C' => 52, 'D' => 28, 'E' => 4];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws = $event->sheet->getDelegate();
                $ws->setShowGridlines(false);

                $this->banner($ws);
                $this->section($ws, 4, '📁  STRUKTUR FILE', '1A56DB');
                $rows = [
                    [5,  '1 Sheet = 1 Layup',        'Setiap tab (sheet) di Excel mewakili satu Layup. Jika Anda punya 3 Layup, buat 3 sheet.', 'F9FAFB'],
                    [6,  'Nama Sheet',                'Nama sheet bebas. Contoh: "Sheet1", "Layup-A", dll.', 'FFFFFF'],
                    [7,  'Baris 1 — Nama Layup',      'Isi CELL A1 dengan nama Layup Anda. Ini yang akan tersimpan di sistem.', 'F9FAFB'],
                    [8,  'Baris 2 — Hint',            'Baris ini adalah petunjuk pengisian. Tidak terbaca oleh sistem.', 'FFFFFF'],
                    [9,  'Baris 3 — Header Kolom',    'Baris ini adalah judul kolom. JANGAN diubah atau dihapus.', 'F9FAFB'],
                    [10, 'Baris 4+ — Data Layer',     'Isi data layer mulai dari baris ke-4. Satu baris = satu layer.', 'FFFFFF'],
                ];
                foreach ($rows as [$r, $lbl, $desc, $bg]) $this->infoRow($ws, $r, $lbl, $desc, $bg);

                $this->section($ws, 12, '📊  PENJELASAN KOLOM DATA', '1A56DB');
                $cols = [
                    [13, 'Kolom A — Layer Order', 'Urutan / nomor urut layer. Harus berupa ANGKA BULAT (1, 2, 3, ...). Tidak boleh duplikat dalam satu layup.', 'F9FAFB'],
                    [14, 'Kolom B — Thickness',   'Ketebalan material dalam mm. Gunakan angka desimal jika perlu. Contoh: 0.5 atau 1.25', 'FFFFFF'],
                    [15, 'Kolom C — Width',        'Lebar material dalam mm. Contoh: 100 atau 125.5', 'F9FAFB'],
                    [16, 'Kolom D — Angle',        'Sudut orientasi serat dalam derajat (°). Contoh: 0, 45, 90, -45. Boleh angka negatif.', 'FFFFFF'],
                ];
                foreach ($cols as [$r, $lbl, $desc, $bg]) $this->infoRow($ws, $r, $lbl, $desc, $bg);

                $this->section($ws, 18, '⚠️  ATURAN PENTING', 'D97706');
                $rules = [
                    [19, 'Jangan hapus Baris 3',      'Baris header wajib ada. Sistem menggunakannya sebagai penanda awal data.', 'FFFBEB'],
                    [20, 'Jangan kosongkan Cell A1',   'Cell A1 adalah nama Layup. Jika kosong, sheet akan dilewati sistem.', 'FEF3C7'],
                    [21, 'Semua kolom wajib diisi',    'Setiap baris data harus lengkap (kolom A–D). Baris tidak lengkap dilewati.', 'FFFBEB'],
                    [22, 'Gunakan angka, bukan teks',  'Kolom B, C, D harus berisi ANGKA. Jangan tulis "0.5 mm", cukup "0.5".', 'FEF3C7'],
                    [23, 'Layer Order unik per layup', 'Tidak boleh ada dua baris dengan Layer Order yang sama dalam satu layup.', 'FFFBEB'],
                ];
                foreach ($rules as [$r, $lbl, $desc, $bg]) $this->infoRow($ws, $r, $lbl, $desc, $bg);

                $this->section($ws, 25, '🔄  APA ITU "CONFLICT"?', '4338CA');
                $conflicts = [
                    [26, 'Conflict terjadi ketika...', 'Data import memiliki Layer Order yang sama dengan data di sistem, NAMUN nilai Thickness/Width/Angle berbeda.', 'EEF2FF'],
                    [27, 'Apa yang akan terjadi?',     'Sistem menampilkan layar "Conflict Resolution" — data lama vs data baru ditampilkan berdampingan.', 'FFFFFF'],
                    [28, 'Pilihan Anda',               '"Keep Existing" → data lama tetap dipakai.  "Accept Incoming" → data baru menggantikan data lama.', 'EEF2FF'],
                    [29, 'Data tanpa conflict',        'Layer yang tidak konflik akan langsung diimport otomatis tanpa tindakan apapun.', 'FFFFFF'],
                ];
                foreach ($conflicts as [$r, $lbl, $desc, $bg]) $this->infoRow($ws, $r, $lbl, $desc, $bg);
            },
        ];
    }

    private function fill(string $hex): array
    {
        return ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $hex]];
    }

    private function banner($ws): void
    {
        $ws->mergeCells('B1:D1');
        $ws->setCellValue('B1', '📄  PANDUAN PENGISIAN TEMPLATE IMPORT LAYUP');
        $ws->getStyle('B1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => $this->fill('1A56DB'),
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(1)->setRowHeight(36);

        $ws->mergeCells('B2:D2');
        $ws->setCellValue('B2', 'Baca panduan ini sebelum mengisi data agar proses import berjalan lancar.');
        $ws->getStyle('B2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '374151']],
            'fill'      => $this->fill('EFF6FF'),
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(2)->setRowHeight(22);
        $ws->getRowDimension(3)->setRowHeight(10);
    }

    private function section($ws, int $row, string $title, string $bg): void
    {
        $ws->mergeCells("B{$row}:D{$row}");
        $ws->setCellValue("B{$row}", "  {$title}");
        $ws->getStyle("B{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => $this->fill($bg),
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension($row)->setRowHeight(26);
    }

    private function infoRow($ws, int $row, string $label, string $desc, string $bg): void
    {
        $border = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']];
        $style  = ['fill' => $this->fill($bg), 'borders' => ['allBorders' => $border]];

        $ws->setCellValue("B{$row}", $label);
        $ws->getStyle("B{$row}")->applyFromArray(array_merge($style, [
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '111827']],
            'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
        ]));

        $ws->setCellValue("C{$row}", $desc);
        $ws->getStyle("C{$row}")->applyFromArray(array_merge($style, [
            'font'      => ['size' => 10, 'color' => ['rgb' => '374151']],
            'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
        ]));

        $ws->getRowDimension($row)->setRowHeight(42);
    }
}
