<?php

namespace App\Services;

use App\Models\CltLayup;
use App\Models\CltLayer;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LayupImport;

class ImportService
{
   public function readFile($file): array
   {
      return Excel::toArray(new LayupImport, $file);
   }

  public function detectConflicts(array $sheets, int $supplierId): array
{
    $conflicts = [];
    $cleanData = [];

    foreach ($sheets as $sheetIndex => $rows) {
        if (empty($rows)) continue;

        $layupName = isset($rows[0][0]) ? trim((string) $rows[0][0]) : null;
        if (!$layupName) continue;

        $existingLayup = CltLayup::where('supplier_id', $supplierId)
            ->where('name', $layupName)
            ->first();

        $layerRows = array_slice($rows, 3);

        foreach ($layerRows as $row) {
            if (count($row) < 4) continue;
            if (empty($row[0]) || empty($row[1]) || empty($row[2])) continue;
            if (!is_numeric($row[0]) || !is_numeric($row[1]) ||
                !is_numeric($row[2]) || !is_numeric($row[3])) continue;

            $layerOrder = (int)   $row[0];
            $thickness  = (float) $row[1];
            $width      = (float) $row[2];
            $angle      = (float) $row[3];

            if ($existingLayup) {
                $existingLayer = CltLayer::where('layup_id', $existingLayup->id)
                    ->where('layer_order', $layerOrder)
                    ->first();

                if ($existingLayer) {
                    $isDifferent =
                        (float) $existingLayer->thickness !== $thickness ||
                        (float) $existingLayer->width     !== $width     ||
                        (float) $existingLayer->angle     !== $angle;

                    if ($isDifferent) {
                        $conflicts[] = [
                            'layup_name'  => $layupName,
                            'layer_order' => $layerOrder,
                            'existing'    => [
                                'thickness' => $existingLayer->thickness,
                                'width'     => $existingLayer->width,
                                'angle'     => $existingLayer->angle,
                            ],
                            'incoming' => [
                                'thickness' => $thickness,
                                'width'     => $width,
                                'angle'     => $angle,
                            ],
                        ];
                        continue;
                    }

                    continue;
                }
            }

            $cleanData[] = [
                'layup_name'  => $layupName,
                'layer_order' => $layerOrder,
                'thickness'   => $thickness,
                'width'       => $width,
                'angle'       => $angle,
            ];
        }
    }

    return [
        'conflicts'  => $conflicts,
        'clean_data' => $cleanData,
    ];
}

   public function processImport(int $supplierId, array $cleanData, array $resolvedConflicts): void
   {
      $allData = array_merge($cleanData, $resolvedConflicts);

      foreach ($allData as $item) {
         $layup = CltLayup::firstOrCreate(
            ['supplier_id' => $supplierId, 'name' => $item['layup_name']],
         );

         CltLayer::updateOrCreate(
            ['layup_id' => $layup->id, 'layer_order' => $item['layer_order']],
            [
               'thickness' => $item['thickness'],
               'width'     => $item['width'],
               'angle'     => $item['angle'],
            ]
         );
      }
   }
}
