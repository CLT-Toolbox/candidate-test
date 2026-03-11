<?php

namespace App\Exports;

use App\Interfaces\CltLayupRepositoryInterface;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CltLayupExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        protected CltLayupRepositoryInterface $cltLayupRepository,
        protected $supplier
    ) {}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->cltLayupRepository->getExport($this->supplier);
    }

    public function headings(): array
    {
        return [
            'Layup ID',
            'Layup Name',
            'Layer Order',
            'Thickness',
            'Width',
            'Angle'
        ];
    }

    public function map($cltLayer): array
    {
        return [
            $cltLayer->cltLayup->id,
            $cltLayer->cltLayup->name,
            $cltLayer->layer_order,
            $cltLayer->thickness,
            $cltLayer->width,
            $cltLayer->angle,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
            ]
        ];
    }
}
