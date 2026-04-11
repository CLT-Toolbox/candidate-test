<?php

namespace App\Http\Controllers;

use App\Http\Requests\CltLayerRequest;
use App\Models\CltLayer;
use App\Services\CltLayerService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CltLayerController extends Controller
{
    public function __construct(protected CltLayerService $service) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cltLayers = $this->service->getAll($request->layup_id);

            return DataTables::of($cltLayers)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return '
                        <button class="btn btn-sm btn-warning me-1" id="btnEdit" data-id="' . $data->id . '">
                            <span class="mdi mdi-pencil"></span>
                        </button>
                        <button class="btn btn-sm btn-danger" id="btnDelete" data-id="' . $data->id . '">
                            <span class="mdi mdi-trash-can"></span>
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(CltLayerRequest $request)
    {
        try {
            $this->service->save(
                $request->only('layup_id', 'layer_order', 'thickness', 'width', 'angle'),
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

    public function edit(CltLayer $cltLayer)
    {
        try {
            return response()->json($cltLayer);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred, please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(CltLayer $cltLayer)
    {
        try {
            $this->service->remove($cltLayer->id);

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
}
