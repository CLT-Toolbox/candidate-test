<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierFormRequest;
use App\Models\Supplier;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SuppliersController extends Controller
{
    /**
     * Display a listing of the suppliers.
     */
    public function index(Request $request): View
    {
        $query = Supplier::withCount('layups');

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort functionality
        $sortBy = $request->input('sort_by', 'created_at');
        $order = $request->input('order', 'desc');

        // Validate sort_by to prevent SQL injection
        $allowedSortFields = ['id', 'name', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        // Validate order
        $order = in_array($order, ['asc', 'desc']) ? $order : 'desc';

        $suppliers = $query->orderBy($sortBy, $order)
            ->paginate(5)
            ->withQueryString(); // Preserve all query parameters in pagination links

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function store(SupplierFormRequest $request): RedirectResponse
    {
        Supplier::create($request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(SupplierFormRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return back()
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified supplier from storage.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
