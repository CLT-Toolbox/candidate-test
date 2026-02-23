<?php

namespace App\Http\Controllers;

use App\Http\Requests\CltLayupRequest;
use App\Interfaces\CltLayupRepositoryInterface;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Http\Request;

class CltLayupController extends Controller
{
    protected CltLayupRepositoryInterface $cltLayupRepository;

    public function __construct(CltLayupRepositoryInterface $cltLayupRepository)
    {
        $this->cltLayupRepository = $cltLayupRepository;
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

        return redirect()->route('supplier.layups.index');
    }

    public function update(CltLayupRequest $request, CltLayup $layup)
    {
        $this->cltLayupRepository->update($layup, $request->validated());

        return redirect()->route('supplier.layups.index');
    }

    public function destroy(CltLayup $layup)
    {
        $this->cltLayupRepository->delete($layup);

        return redirect()->route('supplier.layups.index');
    }
}
