<?php

namespace App\Http\Controllers;

use App\Exports\LayupTemplateExport;
use App\Http\Requests\CltLayupRequest;
use App\Http\Requests\ImportLayupRequest;
use App\Models\CltLayup;
use App\Services\CltLayupService;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CltLayupController extends Controller
{
    public function __construct(
        protected CltLayupService $service,
        protected ImportService $importService
    ) {}

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

    public function downloadTemplate()
    {
        return Excel::download(new LayupTemplateExport, 'layup_template.xlsx');
    }

    public function uploadImport(ImportLayupRequest $request)
    {
        try {
            $sheets = $this->importService->readFile($request->file('file'));
            $result = $this->importService->detectConflicts($sheets, $request->supplier_id);

            return response()->json([
                'status'     => 'success',
                'conflicts'  => $result['conflicts'],
                'clean_data' => $result['clean_data'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred, please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function processImport(Request $request)
    {
        try {
            $this->importService->processImport(
                $request->supplier_id,
                $request->clean_data ?? [],
                $request->resolved_conflicts ?? []
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Import completed successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred, please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
