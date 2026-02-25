<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierLayoupsRequest;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Models\CLT_Layup;
use App\Models\CLT_Layer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::paginate(5);
        return view("suppliers", compact("suppliers"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("suppliers-create-edit");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierRequest $request)
    {
        Supplier::create($request->validated());
        return redirect()->route('suppliers')->with('success', 'Supplier created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        $supplier->load('layups.layers');
        return view("suppliers.show", compact("supplier"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view("suppliers-create-edit", compact("supplier"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());
        return redirect()->route('suppliers')->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers')->with('success', 'Supplier deleted successfully.');
    }

    /**
     * Store a newly created layup for the specified supplier.
     */
    public function storeLayup(SupplierLayoupsRequest $request, Supplier $supplier)
    {
        if(!$request->validated()){
            return redirect()->route('suppliers.show', $supplier)->withErrors($request->errors())->withInput();
        }

        DB::beginTransaction();

        $checkDuplicate = CLT_Layup::where('supplier_id', $supplier->id)->where('name', 'ilike', "%$request->name%")->first();

        if ($checkDuplicate) {
            DB::rollback();
            return redirect()->route('suppliers.show', $supplier)->with('error', 'Layup with this name already exists.')->withInput();
        }

        $layup = CLT_Layup::create([
            'supplier_id' => $supplier->id,
            'name' => $request->name,
        ]);

        foreach ($request->layers as $layerData) {
            CLT_Layer::create([
                'layup_id' => $layup->id,
                'layer_order' => $layerData['layer_order'],
                'thickness' => $layerData['thickness'],
                'width' => $layerData['width'],
                'angle' => $layerData['angle'],
            ]);
        }
        DB::commit();

        return redirect()->route('suppliers.show', $supplier)->with('success', 'Layup added successfully.');
    }

    /**
     * Update an existing layup for the supplier.
     */
    public function updateLayup(SupplierLayoupsRequest $request, Supplier $supplier, CLT_Layup $layup)
    {
        if ($layup->supplier_id !== $supplier->id) {
            abort(404);
        }

        if(!$request->validated()){
            return redirect()->route('suppliers.show', $supplier)->withErrors($request->errors())->withInput();
        }

        DB::beginTransaction();

        $checkDuplicate = CLT_Layup::where('supplier_id', $supplier->id)
            ->where('name', 'ilike', "%$request->name%")
            ->where('id', '!=', $layup->id)
            ->first();

        if ($checkDuplicate) {
            DB::rollback();
            return redirect()->route('suppliers.show', $supplier)->with('error', 'Layup with this name already exists.')->withInput();
        }

        $layup->update(['name' => $request->name]);

        $layup->layers()->delete();

        foreach ($request->layers as $layerData) {
            CLT_Layer::create([
                'layup_id' => $layup->id,
                'layer_order' => $layerData['layer_order'],
                'thickness' => $layerData['thickness'],
                'width' => $layerData['width'],
                'angle' => $layerData['angle'],
            ]);
        }
        DB::commit();

        return redirect()->route('suppliers.show', $supplier)->with('success', 'Layup updated successfully.');
    }

    /**
     * Delete an existing layup for the supplier.
     */
    public function destroyLayup(Supplier $supplier, CLT_Layup $layup)
    {
        if ($layup->supplier_id !== $supplier->id) {
            abort(404);
        }

        $layup->delete();

        return redirect()->route('suppliers.show', $supplier)->with('success', 'Layup deleted successfully.');
    }
}
