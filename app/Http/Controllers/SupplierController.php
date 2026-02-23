<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Interfaces\SupplierRepositoryInteface;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected SupplierRepositoryInteface $supplierRepository;

    public function __construct(SupplierRepositoryInteface $supplierRepository) {
        $this->supplierRepository = $supplierRepository;
    }

    public function index(Request $request)
    {
        $suppliers = $this->supplierRepository->getPaginate(3, $request->input('search'));

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
}
