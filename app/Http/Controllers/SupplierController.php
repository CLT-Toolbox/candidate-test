<?php

namespace App\Http\Controllers;

use App\Exceptions\ImportConflictException;
use App\Http\Requests\ImportSupplierRequest;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Services\SupplierImportExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    private const PENDING_CONFLICTS_SESSION_KEY = 'pending_conflicts';

    public function __construct(
        private readonly SupplierRepositoryInterface $supplierRepository,
        private readonly SupplierImportExportService $importExportService,
    ) {
    }

    /**
     * Menampilkan daftar supplier dengan pagination dan fitur search.
     * Query parameter 'q' digunakan untuk pencarian.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Supplier::class);

        $search = trim((string) $request->query('q', ''));

        return view('suppliers.index', [
            'suppliers' => $this->supplierRepository->paginateWithLayupCount($search)->withQueryString(),
            'search' => $search,
        ]);
    }

    /**
     * Menampilkan form untuk membuat supplier baru.
     */
    public function create(): View
    {
        $this->authorize('create', Supplier::class);

        return view('suppliers.create');
    }

    /**
     * Menyimpan supplier baru ke database.
     * Validasi dilakukan via StoreSupplierRequest.
     */
    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $this->authorize('create', Supplier::class);

        $supplier = $this->supplierRepository->create($request->validated());

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('status', 'Supplier created successfully.');
    }

    /**
     * Menampilkan detail supplier beserta semua layups dan layers.
     * Juga menampilkan import report jika ada di session.
     */
    public function show(Supplier $supplier): View
    {
        $this->authorize('view', $supplier);

        $supplier = $this->supplierRepository->findWithRelationsOrFail($supplier->id);

        return view('suppliers.show', [
            'supplier' => $supplier,
            'importReport' => session('import_report'),
        ]);
    }

    /**
     * Menampilkan form untuk edit supplier.
     */
    public function edit(Supplier $supplier): View
    {
        $this->authorize('update', $supplier);

        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update data supplier.
     * Validasi dilakukan via UpdateSupplierRequest.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->authorize('update', $supplier);

        $this->supplierRepository->update($supplier, $request->validated());

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('status', 'Supplier updated successfully.');
    }

    /**
     * Menghapus supplier dari database.
     * Cascade delete akan menghapus semua layups dan layers terkait.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->authorize('delete', $supplier);

        $this->supplierRepository->delete($supplier);

        return redirect()
            ->route('suppliers.index')
            ->with('status', 'Supplier deleted successfully.');
    }

    /**
     * Export data supplier beserta semua layups dan layers dalam format JSON.
     * File di-download dengan nama supplier-{id}-export.json.
     */
    public function export(Supplier $supplier): Response
    {
        $this->authorize('export', $supplier);

        $payload = $this->importExportService->exportBySupplier($supplier);
        $fileName = 'supplier-'.$supplier->id.'-export.json';

        return response(
            json_encode($payload, JSON_PRETTY_PRINT),
            200,
            [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            ],
        );
    }

    /**
     * Import data layups dan layers ke supplier.
     * Mendukung 4 strategi conflict resolution: overwrite, skip, duplicate_layup, reject.
     * Jika strategy = reject dan ada konflik, redirect ke halaman conflict resolution.
     */
    public function import(ImportSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->authorize('import', $supplier);

        $payload = $request->payloadAsArray();

        Validator::make($payload, [
            'layups' => ['required', 'array'],
            'layups.*.name' => ['required', 'string'],
            'layups.*.layers' => ['nullable', 'array'],
            'layups.*.layers.*.layer_order' => ['required', 'integer', 'min:1'],
            'layups.*.layers.*.thickness' => ['required', 'numeric', 'gt:0'],
            'layups.*.layers.*.width' => ['required', 'numeric', 'gt:0'],
            'layups.*.layers.*.angle' => ['required', 'numeric', 'between:-90,90'],
        ])->validate();

        try {
            $report = $this->importExportService->importBySupplier(
                supplier: $supplier,
                payload: $payload,
                strategy: (string) $request->validated('strategy'),
            );
        } catch (ImportConflictException $exception) {
            return redirect()
                ->route('suppliers.conflicts', $supplier)
                ->withErrors(['import' => 'Import rejected because conflict strategy is reject.'])
                ->with('import_report', [
                    'strategy' => SupplierImportExportService::STRATEGY_REJECT,
                    'summary' => [
                        'created_layups' => 0,
                        'created_layers' => 0,
                        'updated_layers' => 0,
                        'skipped_layers' => 0,
                        'conflicts_count' => count($exception->conflicts),
                    ],
                    'conflicts' => $exception->conflicts,
                ])
                ->with(self::PENDING_CONFLICTS_SESSION_KEY, [
                    'supplier_id' => $supplier->id,
                    'created_at' => now()->toIso8601String(),
                    'conflicts' => $exception->conflicts,
                ]);
        }

        $request->session()->forget(self::PENDING_CONFLICTS_SESSION_KEY);

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('status', 'Import completed successfully.')
            ->with('import_report', $report);
    }

    /**
     * Export daftar semua supplier ke CSV.
     * Menggunakan streaming untuk efisiensi memory pada data besar.
     * Data di-chunk per 500 records untuk optimasi.
     */
    public function exportIndex(Request $request): StreamedResponse
    {
        $this->authorize('exportAny', Supplier::class);

        $search = trim((string) $request->query('q', ''));
        $fileName = 'suppliers-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($search): void {
            $handle = fopen('php://output', 'wb');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['id', 'name', 'code', 'address', 'layups_count', 'created_at']);

            Supplier::query()
                ->withCount('layups')
                ->when($search !== '', function ($query) use ($search): void {
                    $query->where(function ($subQuery) use ($search): void {
                        $subQuery
                            ->where('name', 'like', '%'.$search.'%')
                            ->orWhere('code', 'like', '%'.$search.'%')
                            ->orWhere('address', 'like', '%'.$search.'%');
                    });
                })
                ->orderBy('id')
                ->chunkById(500, function ($suppliers) use ($handle): void {
                    foreach ($suppliers as $supplier) {
                        fputcsv($handle, [
                            $supplier->id,
                            $supplier->name,
                            $supplier->code,
                            $supplier->address,
                            $supplier->layups_count,
                            optional($supplier->created_at)->toDateTimeString(),
                        ]);
                    }
                });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Menampilkan halaman conflict resolution UI.
     * Menampilkan konflik yang pending dari session.
     * Jika tidak ada konflik, redirect ke show supplier.
     */
    public function conflicts(Supplier $supplier): View|RedirectResponse
    {
        $this->authorize('resolveConflicts', $supplier);

        $pendingEnvelope = (array) session(self::PENDING_CONFLICTS_SESSION_KEY, []);
        $pendingSupplierId = (int) ($pendingEnvelope['supplier_id'] ?? 0);
        $pendingConflicts = (array) ($pendingEnvelope['conflicts'] ?? []);

        if ($pendingSupplierId !== $supplier->id || count($pendingConflicts) === 0) {
            return redirect()
                ->route('suppliers.show', $supplier)
                ->withErrors(['conflicts' => 'No pending conflicts found. Run import with reject strategy first.']);
        }

        return view('suppliers.conflicts', [
            'supplier' => $supplier,
            'pendingConflicts' => $pendingConflicts,
        ]);
    }

    /**
     * Menerapkan resolusi konflik yang dipilih user.
     * User bisa pilih 'keep_existing' atau 'accept_incoming' untuk setiap konflik.
     * Setelah selesai, pending conflicts dihapus dari session.
     */
    public function applyConflicts(Request $request, Supplier $supplier): RedirectResponse
    {
        $this->authorize('resolveConflicts', $supplier);

        $pendingEnvelope = (array) session(self::PENDING_CONFLICTS_SESSION_KEY, []);
        $pendingSupplierId = (int) ($pendingEnvelope['supplier_id'] ?? 0);
        $pendingConflicts = (array) ($pendingEnvelope['conflicts'] ?? []);

        if ($pendingSupplierId !== $supplier->id || count($pendingConflicts) === 0) {
            return redirect()
                ->route('suppliers.show', $supplier)
                ->withErrors(['conflicts' => 'No pending conflicts to resolve.']);
        }

        $validated = $request->validate([
            'resolutions' => ['required', 'array'],
            'resolutions.*.decision' => ['required', 'in:keep_existing,accept_incoming'],
        ]);

        $report = $this->importExportService->resolveConflicts(
            supplier: $supplier,
            conflicts: $pendingConflicts,
            resolutions: (array) $validated['resolutions'],
        );

        $request->session()->forget(self::PENDING_CONFLICTS_SESSION_KEY);

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('status', 'Conflict resolution applied successfully.')
            ->with('import_report', $report);
    }
}
