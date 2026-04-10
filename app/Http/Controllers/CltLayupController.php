<?php

namespace App\Http\Controllers;

use App\Http\Requests\CltLayupRequest;
use App\Models\CltLayup;
use App\Services\CltLayupService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CltLayupController extends Controller
{
    public function __construct(protected CltLayupService $service) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cltLayups = $this->service->getAll($request->supplier_id);

            return DataTables::of($cltLayups)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return '
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
    }

    public function store(CltLayupRequest $request)
    {
        try {
            $this->service->save(
                $request->only('supplier_id', 'name'),
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

    public function edit(CltLayup $cltLayup)
    {
        try {
            return response()->json($cltLayup);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred, please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(CltLayup $cltLayup)
    {
        try {
            $this->service->remove($cltLayup->id);

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

    public function show(CltLayup $cltLayup)
    {
        return view('app.clt-layer.index', compact('cltLayup'));
    }
}
