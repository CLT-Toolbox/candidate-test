<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCltLayerRequest;
use App\Http\Requests\UpdateCltLayerRequest;
use App\Models\CltLayer;
use App\Models\CltLayup;
use Illuminate\Http\Request;

class CltLayerController extends Controller
{
    public function index()
    {
        $layers = CltLayer::with('layup')->latest()->get();
        return view('clt_layers.index', compact('layers'));
    }

    public function create(Request $request)
    {
        $layupId = $request->layup_id;
        $layup = CltLayup::findOrFail($layupId);
        return view('clt_layers.create', compact('layup'));
    }

    public function store(StoreCltLayerRequest $request)
    {
        $data = $request->validated();
        $layer = CltLayer::create($data);
        return redirect()
            ->route('clt-layups.show', $layer->layup_id)
            ->with('success', 'Layer created');
    }

    public function edit($id)
    {
        $layer = CltLayer::findOrFail($id);
        $layups = CltLayup::all();

        return view('clt_layers.edit', compact('layer', 'layups'));
    }

    public function update(UpdateCltLayerRequest $request, $id)
    {
        $layer = CltLayer::findOrFail($id);
        $layer->update($request->validated());

        return redirect()->route('clt-layups.show', $layer->layup_id)
            ->with('success', 'Layer updated');
    }

    public function destroy($id)
    {
        CltLayer::findOrFail($id)->delete();

        return redirect()->route('clt-layers.index')
            ->with('success', 'Layer deleted');
    }
}
