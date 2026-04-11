<?php

namespace App\Http\Controllers\Suppliers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Suppliers\PreviewSupplierImportRequest;
use App\Http\Requests\Suppliers\ResolveSupplierImportRequest;
use App\Models\Supplier;
use App\Services\Suppliers\SupplierExportService;
use App\Services\Suppliers\SupplierImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use JsonException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupplierTransferController extends Controller
{
    public function __construct(
        private readonly SupplierExportService $exportService,
        private readonly SupplierImportService $importService,
    ) {
    }

    public function export(Supplier $supplier): StreamedResponse
    {
        $this->authorize('export', $supplier);

        $payload = $this->exportService->buildPayload($supplier);
        $fileName = str($supplier->name)->slug()->append('-export.json')->value();

        return response()->streamDownload(
            static function () use ($payload): void {
                echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            },
            $fileName,
            ['Content-Type' => 'application/json'],
        );
    }

    public function preview(PreviewSupplierImportRequest $request, Supplier $supplier): RedirectResponse
    {
        $payload = $this->decodeImportFile($request->file('import_file')->get());
        $prepared = $this->importService->prepareImport($supplier, $payload);
        $strategy = $request->validated('conflict_strategy');
        $previewKey = $this->previewKey($supplier);

        session()->forget($previewKey);

        if (($prepared['conflicts'] ?? []) !== [] && $strategy === SupplierImportService::STRATEGY_MANUAL) {
            session()->put($previewKey, $prepared);

            return redirect()
                ->route('suppliers.import.conflicts', $supplier)
                ->with('success', 'Conflicts detected. Resolve them one by one before importing.');
        }

        if (($prepared['conflicts'] ?? []) !== [] && $strategy === SupplierImportService::STRATEGY_REJECT) {
            return redirect()
                ->route('suppliers.show', $supplier)
                ->with('warning', 'Import aborted because conflicts were detected.')
                ->with('import_report', [
                    'strategy' => $strategy,
                    'conflicts' => $prepared['conflicts'],
                ]);
        }

        $summary = $this->importService->commitPreparedImport($supplier, $prepared, $strategy);

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Import finished successfully.')
            ->with('import_summary', $summary);
    }

    public function conflicts(Supplier $supplier): View|RedirectResponse
    {
        $this->authorize('import', $supplier);

        $preview = session($this->previewKey($supplier));

        if (! $preview) {
            return redirect()
                ->route('suppliers.show', $supplier)
                ->with('warning', 'There is no pending conflict resolution for this supplier.');
        }

        return view('suppliers.conflicts', [
            'supplier' => $supplier,
            'preview' => $preview,
            'conflicts' => $preview['conflicts'] ?? [],
        ]);
    }

    public function resolve(
        ResolveSupplierImportRequest $request,
        Supplier $supplier,
    ): RedirectResponse {
        $previewKey = $this->previewKey($supplier);
        $preview = session($previewKey);

        if (! $preview) {
            return redirect()
                ->route('suppliers.show', $supplier)
                ->with('warning', 'The pending import preview has expired. Please upload the file again.');
        }

        $summary = $this->importService->commitPreparedImport(
            $supplier,
            $preview,
            SupplierImportService::STRATEGY_MANUAL,
            $request->validated('resolutions'),
        );

        session()->forget($previewKey);

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Conflicts resolved and import completed successfully.')
            ->with('import_summary', $summary);
    }

    private function decodeImportFile(string $rawJson): array
    {
        try {
            /** @var array $decoded */
            $decoded = json_decode($rawJson, true, 512, JSON_THROW_ON_ERROR);

            return $decoded;
        } catch (JsonException) {
            throw ValidationException::withMessages([
                'import_file' => 'The uploaded file must contain valid JSON.',
            ]);
        }
    }

    private function previewKey(Supplier $supplier): string
    {
        return 'supplier-import-preview.'.$supplier->id;
    }
}
