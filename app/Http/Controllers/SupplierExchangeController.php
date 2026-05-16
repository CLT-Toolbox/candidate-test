<?php

namespace App\Http\Controllers;

use App\Services\SupplierExchangeService;
use Illuminate\Http\Request;

class SupplierExchangeController extends Controller
{
    protected $exchangeService;

    public function __construct(SupplierExchangeService $service)
    {
        $this->exchangeService = $service;
    }

    public function export($id)
    {
        $data = $this->exchangeService->exportSupplier($id);

        if (!$data) {
            return redirect()->route('dashboard')->with('error', 'Data Supplier tidak ditemukan! Silakan import data terlebih dahulu.');
        }
        
        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="test-export-supplier-'.$id.'.json"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:json']);
        
        $fileContent = json_decode(file_get_contents($request->file('file')), true);
        $this->exchangeService->importData($fileContent);
        return redirect()->route('dashboard')->with('message', 'Import Berhasil dengan Resolusi Konflik (Overwrite)!');
    }
}