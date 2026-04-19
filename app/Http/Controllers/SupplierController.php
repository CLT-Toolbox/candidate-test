<?php

namespace App\Http\Controllers;

use App\Exceptions\ImportConflictException;
use App\Http\Requests\ImportSupplierRequest;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\SupplierImportExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Supplier::class);

        $suppliers = Supplier::query()->withCount('cltLayups')->orderBy('name')->paginate(15);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        $this->authorize('create', Supplier::class);

        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $supplier = Supplier::create($request->validated());

        return redirect()->route('suppliers.show', $supplier)->with('status', 'Supplier created.');
    }

    public function show(Supplier $supplier): View
    {
        $this->authorize('view', $supplier);

        $supplier->load(['cltLayups' => fn ($q) => $q->orderBy('name')->withCount('cltLayers')]);

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier): View
    {
        $this->authorize('update', $supplier);

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return redirect()->route('suppliers.show', $supplier)->with('status', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->authorize('delete', $supplier);
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('status', 'Supplier deleted.');
    }

    public function export(Supplier $supplier, SupplierImportExportService $service): Response
    {
        $this->authorize('view', $supplier);

        $data = $service->exportPayload($supplier);
        $filename = 'supplier-'.$supplier->id.'-export.json';

        return response(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function importForm(Supplier $supplier): View
    {
        $this->authorize('update', $supplier);

        return view('suppliers.import', compact('supplier'));
    }

    public function import(ImportSupplierRequest $request, Supplier $supplier, SupplierImportExportService $service): RedirectResponse
    {
        $this->authorize('update', $supplier);

        $strategy = $request->validated('strategy');
        $payload = $request->validated('payload');

        try {
            $result = $service->import($supplier, $payload, $strategy);
        } catch (ImportConflictException $e) {
            return redirect()
                ->route('suppliers.import.form', $supplier)
                ->withInput()
                ->withErrors(['import' => 'Conflicts detected (reject strategy).'])
                ->with('conflicts', $e->conflicts);
        } catch (\Throwable $e) {
            return redirect()
                ->route('suppliers.import.form', $supplier)
                ->withInput()
                ->withErrors(['import' => $e->getMessage()]);
        }

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('status', $result['message'].' Layups created: '.$result['created_layups'].', layers updated: '.$result['updated_layers'].', skipped: '.$result['skipped_layers']);
    }
}
