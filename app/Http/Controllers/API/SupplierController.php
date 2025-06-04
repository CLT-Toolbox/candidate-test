<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
// use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Return a JSON list of all suppliers.
     */
    public function index(): JsonResponse
    {
        $suppliers = Supplier::orderBy('id')
            ->select('id', 'name', 'material_type')
            ->get();

        return response()->json($suppliers);
    }

    /**
     * Return a JSON response for a specific supplier.
     */
    public function show(Supplier $supplier): JsonResponse
    {
        $supplier = $supplier->only(['id', 'name', 'material_type']);
        return response()->json($supplier);
    }
}
