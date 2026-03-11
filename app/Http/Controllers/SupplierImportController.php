<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SupplierImportService;
use App\Services\ConflictDetector;
use Illuminate\Http\Request;

class SupplierImportController extends Controller
{
    protected SupplierImportService $importService;
    protected ConflictDetector $conflictDetector;

    public function __construct(SupplierImportService $importService, ConflictDetector $conflictDetector)
    {
        $this->importService = $importService;
        $this->conflictDetector = $conflictDetector;
    }

    /**
     * Show the import form for a supplier.
     */
    public function showForm(Supplier $supplier)
    {
        return view('suppliers.import', compact('supplier'));
    }

    /**
     * Handle the uploaded JSON file and process the import.
     */
    public function upload(Request $request, Supplier $supplier)
    {
        $request->validate([
            'json_file' => 'required|file|mimes:json|max:10240',
        ]);

        try {
            $content = file_get_contents($request->file('json_file')->getRealPath());
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['json_file' => 'Invalid JSON format: ' . json_last_error_msg()]);
            }

            $validation = $this->importService->validateImportData($data);
            if (!$validation['valid']) {
                return back()->withErrors(['validation' => $validation['errors']]);
            }

            $conflicts = $this->conflictDetector->detectConflicts($data, $supplier);
            if (!empty($conflicts)) {
                session(['pending_import' => $data]);
                return redirect()->route('suppliers.import.conflicts', $supplier)
                    ->with('conflicts', $conflicts);
            }

            $result = $this->importService->import($data, $supplier);

            return redirect()->route('suppliers.show', $supplier)
                ->with('success', 'Supplier data imported successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error processing file: ' . $e->getMessage()]);
        }
    }

    /**
     * show the conflict resolution page if there are conflicts detected during import
     */
    public function showConflicts(Supplier $supplier)
    {
        $importData = session('pending_import');
        $conflicts = session('conflicts');

        if (!$importData || !$conflicts) {
            return redirect()->route('suppliers.import.form', $supplier)
                ->withErrors(['error' => 'Session expired. Please try importing again.']);
        }

        $conflictDetails = [];
        foreach ($conflicts as $conflict) {
            $layupIndex = $conflict['layup_index'];
            $incomingLayup = $importData['layups'][$layupIndex];

            $conflictDetails[] = [
                'layup_index' => $layupIndex,
                'layup_name' => $conflict['layup_name'],
                'incoming_layup' => $incomingLayup,
                'existing_layup_id' => $conflict['existing_layup_id'],
                'layer_conflicts' => $conflict['layer_conflicts'],
            ];
        }

        return view('suppliers.import-conflicts', [
            'supplier' => $supplier,
            'conflicts' => $conflictDetails,
            'total_conflicts' => count($conflictDetails),
        ]);
    }

    /**
     * Resolve conflicts and complete the import process.
     */
    public function resolveConflicts(Request $request, Supplier $supplier)
    {
        $importData = session('pending_import');

        if (!$importData) {
            return back()->withErrors(['error' => 'Session expired. Please try importing again.']);
        }

        $resolutions = [];
        $resolutionInput = $request->input('resolution', []);

        foreach ($resolutionInput as $layupIndex => $strategy) {
            $resolutions[(int) $layupIndex] = ['strategy' => $strategy];
        }

        $result = $this->importService->import($importData, $supplier, $resolutions);

        session()->forget('pending_import');

        if ($result['success']) {
            return redirect()->route('suppliers.show', $supplier)
                ->with('success', 'Import completed successfully!')
                ->with('import_result', $result);
        }

        return back()->withErrors(['error' => 'Import failed: ' . $result['message']]);
    }

    /**
     * Handle direct JSON import via textarea input (alternative to file upload)
     */
    public function importJson(Request $request, Supplier $supplier)
    {
        $request->validate([
            'data' => 'required|json',
        ]);

        try {
            $data = json_decode($request->input('data'), true);

            $validation = $this->importService->validateImportData($data);
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'errors' => $validation['errors'],
                ], 422);
            }

            $result = $this->importService->import($data, $supplier);

            return response()->json($result, $result['success'] ? 200 : 409);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
