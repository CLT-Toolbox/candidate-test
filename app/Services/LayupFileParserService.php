<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class LayupFileParserService
{
    /**
     * Format CSV used by conflict-check upload (layer_order in column 5).
     *
     * @return array<int, array<string, mixed>>
     */
    public function parseConflictCheckCsv(UploadedFile $file): array
    {
        $layups = [];
        $handle = fopen($file->getRealPath(), 'r');

        fgetcsv($handle);

        $currentLayup = null;
        $layupKey = null;

        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            if (! empty($row[0]) && ($row[0] !== $currentLayup || $layupKey === null)) {
                $currentLayup = $row[0];
                $layupKey = count($layups);
                $layups[$layupKey] = [
                    'name' => $row[0],
                    'description' => $row[1] ?? null,
                    'thickness' => $row[2] ?? null,
                    'grade' => $row[3] ?? null,
                    'status' => $row[4] ?? 'ACTIVE',
                    'layers' => [],
                ];
            }

            if (! empty($row[4]) && is_numeric($row[4]) && isset($row[5])) {
                if (isset($layups[$layupKey])) {
                    $layups[$layupKey]['layers'][] = [
                        'layer_order' => (int) $row[4],
                        'thickness' => $row[5] ?? null,
                        'width' => $row[6] ?? null,
                        'angle' => $row[7] ?? null,
                    ];
                }
            }
        }

        fclose($handle);

        return $layups;
    }

    /**
     * Layup import CSV: Name|Description|Thickness|Grade|Status|LayerOrder|LayerThickness|LayerWidth|LayerAngle
     *
     * @return array<int, array<string, mixed>>
     */
    public function parseLayupImportCsv(UploadedFile $file): array
    {
        $layups = [];
        $handle = fopen($file->getRealPath(), 'r');

        fgetcsv($handle);

        $currentLayup = null;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 9 || empty($row[0])) {
                continue;
            }

            $name = $row[0];

            if ($name !== $currentLayup) {
                $currentLayup = $name;
                $layups[] = [
                    'name' => $row[0],
                    'description' => $row[1] ?? null,
                    'thickness' => $row[2] ?? null,
                    'grade' => $row[3] ?? null,
                    'status' => $row[4] ?? 'ACTIVE',
                    'layers' => [],
                ];
            }

            if (count($layups) > 0) {
                $lastKey = count($layups) - 1;
                $layups[$lastKey]['layers'][] = [
                    'layer_order' => (int) ($row[5] ?? 0),
                    'thickness' => $row[6] ?? null,
                    'width' => $row[7] ?? null,
                    'angle' => $row[8] ?? null,
                ];
            }
        }

        fclose($handle);

        return $layups;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function parseJsonFile(UploadedFile $file): array
    {
        $content = file_get_contents($file->getRealPath());
        $data = json_decode($content, true);

        return is_array($data) ? $data : [$data];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function parseForConflictCheck(UploadedFile $file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());

        return $ext === 'csv'
            ? $this->parseConflictCheckCsv($file)
            : $this->parseJsonFile($file);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function parseForLayupImport(UploadedFile $file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());

        return $ext === 'csv'
            ? $this->parseLayupImportCsv($file)
            : $this->parseJsonFile($file);
    }
}
