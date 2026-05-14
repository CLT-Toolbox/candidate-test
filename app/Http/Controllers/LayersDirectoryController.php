<?php

namespace App\Http\Controllers;

use App\Models\Layer;

class LayersDirectoryController extends Controller
{
    /**
     * Daftar semua layer (semua layup / supplier).
     */
    public function __invoke()
    {
        $layers = Layer::query()
            ->with(['layup.supplier'])
            ->orderBy('layup_id')
            ->orderBy('layer_order')
            ->get();

        return view('layers.index', [
            'layers' => $layers,
        ]);
    }
}
