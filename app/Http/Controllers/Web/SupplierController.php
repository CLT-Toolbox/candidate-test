<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

use App\Exports\SupplierExport;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        // 🔍 Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 🔥 SORT A-Z / Z-A
        if ($request->sort == 'asc') {
            $query->orderBy('name', 'asc');
        } elseif ($request->sort == 'desc') {
            $query->orderBy('name', 'desc');
        }

        $suppliers = $query->withCount('layups')->get();

        return view('suppliers.index', compact('suppliers'));
    }


    public function export()
    {
        return Excel::download(
            new SupplierExport(),
            'suppliers.xlsx'
        );
    }
}
