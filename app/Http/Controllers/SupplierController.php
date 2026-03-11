<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Interfaces\SupplierRepositoryInteface;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected SupplierRepositoryInteface $supplierRepository;
    protected SupplierService $supplierService;

    public function __construct(SupplierRepositoryInteface $supplierRepository, SupplierService $supplierService) {
        $this->supplierRepository = $supplierRepository;
        $this->supplierService = $supplierService;
    }

    public function index(Request $request)
    {
        $suppliers = $this->supplierRepository->getPaginate(10, $request->input('search'));

        return view('supplier.index', [
            'suppliers' => $suppliers,
        ]);
    }

    public function store(SupplierRequest $request)
    {
        $this->supplierRepository->create($request->validated());

        return redirect()->route('suppliers.index');
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $this->supplierRepository->update($supplier, $request->validated());

        return redirect()->route('suppliers.index');
    }

    public function destroy(Supplier $supplier)
    {
        $this->supplierRepository->delete($supplier);

        return redirect()->route('suppliers.index');
    }

    public function export()
    {
        return $this->supplierService->exportToXlsx();
    }
}
