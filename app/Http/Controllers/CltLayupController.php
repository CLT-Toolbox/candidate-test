<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCltLayupRequest;
use App\Http\Requests\UpdateCltLayupRequest;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Http\Request;

class CltLayupController extends Controller
{
    public function index()
    {
        $layups = CltLayup::with('supplier')->latest()->get();
        return view('clt_layups.index', compact('layups'));
    }

    public function create(Request $request)
    {
        $supplierId = $request->supplier_id;

        $supplier = Supplier::findOrFail($supplierId);

        return view('clt_layups.create', compact('supplierId', 'supplier'));
    }

    public function store(StoreCltLayupRequest $request)
    {
        $data = $request->validated();

        $supplier = Supplier::findOrFail($request->supplier_id);

        $layup = CltLayup::create([
            'supplier_id' => $supplier->id,
            'name' => $data['name'],
        ]);

        return redirect()
            ->route('suppliers.show', $supplier->id)
            ->with('success', 'Layup created');
    }

    public function edit($id)
    {
        $layup = CltLayup::findOrFail($id);

        return view('clt_layups.edit', compact('layup'));
    }

    public function show($id)
    {
        $layup = CltLayup::with(['supplier', 'layers'])
            ->findOrFail($id);

        return view('clt_layups.show', compact('layup'));
    }

    public function update(UpdateCltLayupRequest $request, $id)
    {
        $layup = CltLayup::findOrFail($id);

        $layup->update($request->validated());

        return redirect()
            ->route('suppliers.show', $layup->supplier_id)
            ->with('success', 'Layup updated');
    }

    public function destroy($id)
    {
        $layup = CltLayup::findOrFail($id);
        $supplierId = $layup->supplier_id;

        $layup->delete();

        return redirect()
            ->route('suppliers.show', $supplierId)
            ->with('success', 'Layup deleted');
    }
}