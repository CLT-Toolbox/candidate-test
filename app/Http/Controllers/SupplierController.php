<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function __construct(protected SupplierService $service) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $suppliers = $this->service->getAll();

            return DataTables::of($suppliers)
                ->addIndexColumn()
                ->addColumn('total_layups', function ($data) {
                    return $data->layups->count();
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('M d, Y');
                })
                ->addColumn('action', function ($data) {
                    return '
                        <a href="' . route('supplier.export', $data->id) . '" class="btn btn-sm btn-info me-1">
                            <span class="mdi mdi-download"></span> Export
                        </a>
                        <button class="btn btn-sm btn-warning me-1" id="btnEdit" data-id="' . $data->id . '">
                            <span class="mdi mdi-pencil"></span> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" id="btnDelete" data-id="' . $data->id . '">
                            <span class="mdi mdi-trash-can"></span> Delete
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('app.supplier.index');
    }

    public function store(SupplierRequest $request)
    {
        try {
            $this->service->save(
                $request->only('name'),
                $request->id
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Data saved successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred, please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Supplier $supplier)
    {
        try {
            return response()->json($supplier);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred, please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $this->service->remove($supplier->id);

            return response()->json([
                'status'  => 'success',
                'message' => 'Data deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred, please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Supplier $supplier)
    {
        return view('app.clt-layup.index', compact('supplier'));
    }

    public function export(Supplier $supplier)
    {
        try {
            return $this->service->exportExcel($supplier->id);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred, please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
