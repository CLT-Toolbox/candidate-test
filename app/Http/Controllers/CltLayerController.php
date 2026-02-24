<?php

namespace App\Http\Controllers;

use App\Http\Requests\CltLayerRequest;
use App\Interfaces\CltLayerRepositoryInterface;
use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;

class CltLayerController extends Controller
{
    protected CltLayerRepositoryInterface $cltLayerRepository;

    public function __construct(CltLayerRepositoryInterface $cltLayerRepository)
    {
        $this->cltLayerRepository = $cltLayerRepository;
    }

    public function index(Supplier $supplier, CltLayup $layup)
    {
        $layers = $this->cltLayerRepository->getPaginate(3, $layup);

        return view('supplier.layups.layers.index', [
            'supplier' => $supplier,
            'layup' => $layup,
            'layers' => $layers,
        ]);
    }

    public function store(Supplier $supplier, CltLayup $layup, CltLayerRequest $request)
    {
        $layer_order = $layup->cltLayers()->count() === 0 ? 1 : $layup->cltLayers()->max('layer_order') + 1;
        $this->cltLayerRepository->create($layup, array_merge($request->validated(), ['layer_order' => $layer_order]));

        return redirect()->route('suppliers.layups.layers.index', [$supplier->id, $layup->id]);
    }

    public function update(CltLayerRequest $request, Supplier $supplier, CltLayup $layup, CltLayer $layer)
    {
        $this->cltLayerRepository->update($layer, $request->validated());

        return redirect()->route('suppliers.layups.layers.index', [$supplier->id, $layup->id]);
    }

    public function destroy(Supplier $supplier, CltLayup $layup, CltLayer $layer)
    {
        $this->cltLayerRepository->delete($layer);

        return redirect()->route('suppliers.layups.layers.index', [$supplier->id, $layup->id]);
    }
}
