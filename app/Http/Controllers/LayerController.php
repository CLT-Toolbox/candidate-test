<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLayerRequest;
use App\Http\Requests\UpdateLayerRequest;
use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Repositories\Contracts\LayerRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LayerController extends Controller
{
    public function __construct(private readonly LayerRepositoryInterface $layerRepository)
    {
    }

    /**
     * Menampilkan daftar layer dalam satu layup.
     * Layers diurutkan berdasarkan layer_order.
     */
    public function index(Supplier $supplier, Layup $layup): View
    {
        $this->authorize('viewAny', Layer::class);

        return view('layers.index', [
            'supplier' => $supplier,
            'layup' => $layup,
            'layers' => $this->layerRepository->getByLayup($layup),
        ]);
    }

    /**
     * Menampilkan form untuk membuat layer baru dalam layup.
     */
    public function create(Supplier $supplier, Layup $layup): View
    {
        $this->authorize('create', Layer::class);

        return view('layers.create', compact('supplier', 'layup'));
    }

    /**
     * Menyimpan layer baru ke layup.
     * Validasi dilakukan via StoreLayerRequest.
     */
    public function store(StoreLayerRequest $request, Supplier $supplier, Layup $layup): RedirectResponse
    {
        $this->authorize('create', Layer::class);

        $layer = $this->layerRepository->createForLayup($layup, $request->validated());

        return redirect()
            ->route('suppliers.layups.layers.show', [$supplier, $layup, $layer])
            ->with('status', 'Layer created successfully.');
    }

    /**
     * Menampilkan detail layer.
     */
    public function show(Supplier $supplier, Layup $layup, Layer $layer): View
    {
        $this->authorize('view', $layer);

        $layer = $this->layerRepository->findForLayupOrFail($layup, $layer->id);

        return view('layers.show', compact('supplier', 'layup', 'layer'));
    }

    /**
     * Menampilkan form untuk edit layer.
     */
    public function edit(Supplier $supplier, Layup $layup, Layer $layer): View
    {
        $this->authorize('update', $layer);

        return view('layers.edit', compact('supplier', 'layup', 'layer'));
    }

    /**
     * Update data layer.
     * Validasi dilakukan via UpdateLayerRequest.
     */
    public function update(UpdateLayerRequest $request, Supplier $supplier, Layup $layup, Layer $layer): RedirectResponse
    {
        $this->authorize('update', $layer);

        $this->layerRepository->update($layer, $request->validated());

        return redirect()
            ->route('suppliers.layups.layers.show', [$supplier, $layup, $layer])
            ->with('status', 'Layer updated successfully.');
    }

    /**
     * Menghapus layer dari database.
     */
    public function destroy(Supplier $supplier, Layup $layup, Layer $layer): RedirectResponse
    {
        $this->authorize('delete', $layer);

        $this->layerRepository->delete($layer);

        return redirect()
            ->route('suppliers.layups.show', [$supplier, $layup])
            ->with('status', 'Layer deleted successfully.');
    }

    /**
     * Menampilkan katalog semua layer dari semua layup dan supplier dengan pagination.
     * Fitur search berdasarkan nama layup atau supplier.
     */
    public function catalog(Request $request): View
    {
        $this->authorize('viewAny', Layer::class);

        $search = trim((string) $request->query('q', ''));

        $layers = Layer::query()
            ->with([
                'layup:id,supplier_id,name',
                'layup.supplier:id,name',
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $query->whereHas('layup', function ($layupQuery) use ($search): void {
                    $layupQuery->where('name', 'like', '%'.$search.'%')
                        ->orWhereHas('supplier', function ($supplierQuery) use ($search): void {
                            $supplierQuery->where('name', 'like', '%'.$search.'%');
                        });
                });
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('layers.catalog', [
            'layers' => $layers,
            'search' => $search,
        ]);
    }
}
