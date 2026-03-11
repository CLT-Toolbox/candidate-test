<?php

namespace App\Exports;

use App\Interfaces\SupplierRepositoryInteface;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplierExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        protected SupplierRepositoryInteface $supplierRepository
    ) {}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->supplierRepository->getExport();
    }

    public function headings(): array
    {
        return [
            'Supplier ID',
            'Supplier Name',
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
            $cltLayer->cltLayup->supplier->id,
            $cltLayer->cltLayup->supplier->name,
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
