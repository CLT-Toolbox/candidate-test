<?php

namespace App\Http\Controllers;

use App\Models\Layer;
use App\Models\Layup;
use Illuminate\Http\Request;

class LayerController extends Controller
{
     public function index()
    {
        $layers = Layer::with('layup')->latest()->get();

        return view('layers.index', compact('layers'));
    }

    public function create()
    {
        $layups = Layup::all();

        return view('layers.form', [
            'layer' => null,
            'layups' => $layups
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'layup_id' => 'required',
            'layer_order' => 'required|integer',
            'thickness' => 'required|numeric',
            'width' => 'required|numeric',
            'angle' => 'required|numeric',
        ]);

        Layer::create($request->all());

        return redirect()->route('layers.index');
    }

    public function edit(Layer $layer)
    {
        $layups = Layup::all();

        return view('layers.form', [
            'layer' => $layer,
            'layups' => $layups
        ]);
    }

    public function update(Request $request, Layer $layer)
    {
        $request->validate([
            'layup_id' => 'required',
            'layer_order' => 'required|integer',
            'thickness' => 'required|numeric',
            'width' => 'required|numeric',
            'angle' => 'required|numeric',
        ]);

        $layer->update($request->all());

        return redirect()->route('layers.index');
    }

    public function destroy(Layer $layer)
    {
        $layer->delete();

        return redirect()->route('layers.index');
    }
}
