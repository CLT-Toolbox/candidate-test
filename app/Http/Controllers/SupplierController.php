<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::paginate(5);
        return view('supplier', compact('suppliers'));
    }

    public function store(Request $request)
    {        
        $validated = $request->validate([
            'name' => 'required|string|max:255',      
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',  
            'is_active' => 'required|boolean',
        ]);

        try {
            Supplier::create($validated);

            return redirect()
                ->back()
                ->with('success', 'Supplier created successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to create supplier: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create supplier.');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',      
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',  
            'is_active' => 'required|boolean', 
        ]);

        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->update($validated);

            return redirect()
                ->back()
                ->with('success', 'Supplier updated successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to update supplier: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update supplier.');
        }
    }

    public function destroy($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();

            return redirect()
                ->back()
                ->with('success', 'Supplier deleted successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to delete supplier: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Failed to delete supplier.');
        }
    }

    public function show($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            return view('supplier_detail', compact('supplier'));
        } catch (\Exception $e) {
            Log::error('Failed to retrieve supplier: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Failed to retrieve supplier.');
        }
    }
}
