<?php

namespace App\Http\Controllers;

use App\Models\Layup;

class LayupsDirectoryController extends Controller
{
    /**
     * Daftar semua layup (semua supplier).
     */
    public function __invoke()
    {
        $layups = Layup::query()
            ->with('supplier')
            ->withCount('layers')
            ->orderByDesc('updated_at')
            ->get();

        return view('layups.index', [
            'layups' => $layups,
        ]);
    }
}
