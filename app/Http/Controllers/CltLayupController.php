<?php

namespace App\Http\Controllers;

use App\Http\Requests\CltLayupImportRequest;
use App\Http\Requests\CltLayupImportResolveRequest;
use App\Http\Requests\CltLayupRequest;
use App\Imports\CltLayupImport;
use App\Interfaces\CltLayupRepositoryInterface;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\CltLayupService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CltLayupController extends Controller
{
    protected CltLayupRepositoryInterface $cltLayupRepository;
    protected CltLayupService $cltLayupService;

    public function __construct(CltLayupRepositoryInterface $cltLayupRepository, CltLayupService $cltLayupService)
    {
        $this->cltLayupRepository = $cltLayupRepository;
        $this->cltLayupService = $cltLayupService;
    }

    public function index(Request $request, Supplier $supplier)
    {
        $layups = $this->cltLayupRepository->getPaginate(3, $request->input('search'), $supplier);

        return view('supplier.layups.index', [
            'supplier' => $supplier,
            'layups' => $layups,
        ]);
    }

    public function store(Supplier $supplier, CltLayupRequest $request)
    {
        $this->cltLayupRepository->create($supplier, $request->validated());

        return redirect()->route('suppliers.layups.index', $supplier->id);
    }

    public function update(CltLayupRequest $request, Supplier $supplier, CltLayup $layup)
    {
        $this->cltLayupRepository->update($layup, $request->validated());

        return redirect()->route('suppliers.layups.index', $supplier->id);
    }

    public function destroy(Supplier $supplier, CltLayup $layup)
    {
        $this->cltLayupRepository->delete($layup);

        return redirect()->route('suppliers.layups.index', $supplier->id);
    }

    public function export(Supplier $supplier, CltLayup $layup)
    {
        return $this->cltLayupService->exportToXlxs($supplier);
    }

    public function import(CltLayupImportRequest $request, Supplier $supplier)
    {
        $data = Excel::toCollection(new CltLayupImport, $request->file('file'))->first();

        $result = $this->cltLayupService->checkImport($supplier, $data);

        return view('supplier.layups.import', $result);
    }

    public function resolve(CltLayupImportResolveRequest $request, Supplier $supplier)
    {
        $this->cltLayupService->resolveImport($supplier, $request->validated());

        return redirect()->route('suppliers.layups.index', $supplier->id)->with('success', 'Layup data imported successfully.');
    }
}
