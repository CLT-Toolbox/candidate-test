<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\CltLayup;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LayupsController extends Controller
{
    /**
     * Display layups for a specific supplier.
     */
    public function index(Request $request, Supplier $supplier): View
    {
        $query = $supplier->layups()->with('layers');

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $layups = $query->orderBy('created_at', 'desc')
            ->paginate(4)
            ->withQueryString();

        return view('layups.index', compact('supplier', 'layups'));
    }

    /**
     * Store a newly created layup in storage.
     */
    public function store(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $supplier->layups()->create($validated);

        return redirect()->route('suppliers.layups', $supplier->id)
            ->with('success', 'Layup created successfully.');
    }

    /**
     * Update the specified layup in storage.
     */
    public function update(Request $request, Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        // Ensure the layup belongs to the supplier
        if ($layup->supplier_id !== $supplier->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $layup->update($validated);

        return redirect()->route('suppliers.layups', $supplier->id)
            ->with('success', 'Layup updated successfully.');
    }

    /**
     * Remove the specified layup from storage.
     */
    public function destroy(Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        // Ensure the layup belongs to the supplier
        if ($layup->supplier_id !== $supplier->id) {
            abort(404);
        }

        $layup->delete();

        return redirect()->route('suppliers.layups', $supplier->id)
            ->with('success', 'Layup deleted successfully.');
    }
}
