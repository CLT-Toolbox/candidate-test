<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    /**
     * Export supplier data with all layups and layers as JSON.
     */
    public function exportJson(Supplier $supplier)
    {
        $data = $this->prepareExportData($supplier);

        $fileName = 'supplier_' . $supplier->id . '_' . $supplier->name . '_' . date('Y-m-d_His') . '.json';

        return Response::json($data, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Export supplier data with all layups and layers as CSV.
     */
    public function exportCsv(Supplier $supplier)
    {
        $data = $this->prepareExportData($supplier);

        $fileName = 'supplier_' . $supplier->id . '_' . $supplier->name . '_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Write CSV headers with supplier_id
            fputcsv($file, ['supplier_id', 'supplier_name', 'layup_name', 'layer_order', 'thickness', 'width', 'angle']);

            // Write data rows
            foreach ($data['layups'] as $layup) {
                foreach ($layup['layers'] as $layer) {
                    fputcsv($file, [
                        $data['supplier_id'],
                        $data['supplier_name'],
                        $layup['name'],
                        $layer['layer_order'],
                        $layer['thickness'],
                        $layer['width'],
                        $layer['angle'],
                    ]);
                }
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export supplier data with all layups and layers as Excel.
     */
    public function exportExcel(Supplier $supplier)
    {
        $data = $this->prepareExportData($supplier);

        $fileName = 'supplier_' . $supplier->id . '_' . $supplier->name . '_' . date('Y-m-d_His') . '.xlsx';

        try {
            // Create new Spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set document properties
            $spreadsheet->getProperties()
                ->setCreator('CLT Management System')
                ->setTitle('Supplier Export - ' . $supplier->name)
                ->setSubject('Layup Data Export')
                ->setDescription('Export of supplier layups and layers')
                ->setKeywords('clt layup layers export')
                ->setCategory('Data Export');

            // Set sheet title
            $sheet->setTitle('Layups & Layers');

            // Header row styling
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3f7a5c'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];

            // Set headers
            $headers = ['Supplier Name', 'Layup Name', 'Layer Order', 'Thickness (mm)', 'Width (mm)', 'Angle (°)'];
            $sheet->fromArray($headers, null, 'A1');
            $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(25);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(18);
            $sheet->getColumnDimension('E')->setWidth(18);
            $sheet->getColumnDimension('F')->setWidth(15);

            // Populate data
            $row = 2;
            foreach ($data['layups'] as $layup) {
                foreach ($layup['layers'] as $layer) {
                    $sheet->setCellValue('A' . $row, $data['supplier_name']);
                    $sheet->setCellValue('B' . $row, $layup['name']);
                    $sheet->setCellValue('C' . $row, $layer['layer_order']);
                    $sheet->setCellValue('D' . $row, $layer['thickness']);
                    $sheet->setCellValue('E' . $row, $layer['width']);
                    $sheet->setCellValue('F' . $row, $layer['angle']);

                    // Alternate row colors
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('F9FAFB');
                    }

                    $row++;
                }
            }

            // Add borders to all data
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB'],
                    ],
                ],
            ];
            $sheet->getStyle('A1:F' . ($row - 1))->applyFromArray($styleArray);

            // Add metadata sheet
            $metaSheet = $spreadsheet->createSheet();
            $metaSheet->setTitle('Export Info');
            $metaSheet->setCellValue('A1', 'Supplier ID:');
            $metaSheet->setCellValue('B1', $data['supplier_id']);
            $metaSheet->setCellValue('A2', 'Supplier Name:');
            $metaSheet->setCellValue('B2', $data['supplier_name']);
            $metaSheet->setCellValue('A3', 'Exported At:');
            $metaSheet->setCellValue('B3', $data['exported_at']);
            $metaSheet->setCellValue('A4', 'Total Layups:');
            $metaSheet->setCellValue('B4', count($data['layups']));

            $metaSheet->getStyle('A1:A4')->getFont()->setBold(true);
            $metaSheet->getColumnDimension('A')->setWidth(20);
            $metaSheet->getColumnDimension('B')->setWidth(30);

            // Set active sheet back to data sheet
            $spreadsheet->setActiveSheetIndex(0);

            // Write to file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            // Output to browser
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            // If PhpSpreadsheet is not available, fall back to CSV
            return $this->exportCsv($supplier);
        }
    }

    /**
     * Prepare export data structure.
     */
    private function prepareExportData(Supplier $supplier)
    {
        $supplier->load(['layups.layers' => function ($query) {
            $query->orderBy('layer_order');
        }]);

        $layupsData = $supplier->layups->map(function ($layup) {
            return [
                'name' => $layup->name,
                'layers' => $layup->layers->map(function ($layer) {
                    return [
                        'layer_order' => $layer->layer_order,
                        'thickness' => (float) $layer->thickness,
                        'width' => (float) $layer->width,
                        'angle' => (float) $layer->angle,
                    ];
                })->toArray(),
            ];
        })->toArray();

        return [
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->name,
            'exported_at' => now()->toIso8601String(),
            'layups' => $layupsData,
        ];
    }
}
