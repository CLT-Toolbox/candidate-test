<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LayupTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new InstructionSheet(),
            new LayupSheetTemplate('Layup A'),
        ];
    }
}
