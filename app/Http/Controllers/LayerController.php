<?php

namespace App\Http\Controllers;

use App\Models\Layer;
use App\Models\Layup;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class LayerController extends Controller
{
    public function index(Layup $layup)
{
    return $layup->layers;
}

public function store(Request $request, Layup $layup)
{
    $request->validate([
        'layer_order' => 'required|integer',
        'thickness' => 'required|numeric',
        'width' => 'required|numeric',
        'angle' => 'required|numeric',
    ]);

    try {
        return $layup->layers()->create($request->all());
    } catch (QueryException $e) {
        return response()->json([
            'message' => 'Layer with same layer_order already exists'
        ], 409);
    }
}
public function show(Layup $layup, Layer $layer)
{
    return $layer;
}

public function update(Request $request, Layup $layup, Layer $layer)
{
    $layer->update($request->all());
    return $layer;
}

public function destroy(Layup $layup, Layer $layer)
{
    $layer->delete();
    return response()->noContent();
}
}
