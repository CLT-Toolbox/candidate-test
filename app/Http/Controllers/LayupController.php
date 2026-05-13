<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLayupRequest;
use App\Http\Requests\UpdateLayupRequest;
use App\Models\Layup;
use App\Models\Supplier;
use App\Repositories\Contracts\LayupRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LayupController extends Controller
{
    public function __construct(private readonly LayupRepositoryInterface $layupRepository)
    {
    }

    /**
     * Menampilkan daftar layup dalam satu supplier.
     */
    public function index(Supplier $supplier): View
    {
        $this->authorize('viewAny', Layup::class);

        return view('layups.index', [
            'supplier' => $supplier,
            'layups' => $this->layupRepository->getBySupplier($supplier),
        ]);
    }

    /**
     * Menampilkan form untuk membuat layup baru dalam supplier.
     */
    public function create(Supplier $supplier): View
    {
        $this->authorize('create', Layup::class);

        return view('layups.create', compact('supplier'));
    }

    /**
     * Menyimpan layup baru ke supplier.
     * Validasi dilakukan via StoreLayupRequest.
     */
    public function store(StoreLayupRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->authorize('create', Layup::class);

        $layup = $this->layupRepository->createForSupplier($supplier, $request->validated());

        return redirect()
            ->route('suppliers.layups.show', [$supplier, $layup])
            ->with('status', 'Layup created successfully.');
    }

    /**
     * Menampilkan detail layup beserta semua layers.
     */
    public function show(Supplier $supplier, Layup $layup): View
    {
        $this->authorize('view', $layup);

        $layup = $this->layupRepository->findForSupplierOrFail($supplier, $layup->id);

        return view('layups.show', compact('supplier', 'layup'));
    }

    /**
     * Menampilkan form untuk edit layup.
     */
    public function edit(Supplier $supplier, Layup $layup): View
    {
        $this->authorize('update', $layup);

        return view('layups.edit', compact('supplier', 'layup'));
    }

    /**
     * Update data layup.
     * Validasi dilakukan via UpdateLayupRequest.
     */
    public function update(UpdateLayupRequest $request, Supplier $supplier, Layup $layup): RedirectResponse
    {
        $this->authorize('update', $layup);

        $this->layupRepository->update($layup, $request->validated());

        return redirect()
            ->route('suppliers.layups.show', [$supplier, $layup])
            ->with('status', 'Layup updated successfully.');
    }

    /**
     * Menghapus layup dari database.
     * Cascade delete akan menghapus semua layers terkait.
     */
    public function destroy(Supplier $supplier, Layup $layup): RedirectResponse
    {
        $this->authorize('delete', $layup);

        $this->layupRepository->delete($layup);

        return redirect()
            ->route('suppliers.layups.index', $supplier)
            ->with('status', 'Layup deleted successfully.');
    }

    /**
     * Menduplikasi layup beserta semua layers.
     * Nama layup baru diberi suffix "(Copy)".
     * Jika sudah ada, tambahkan counter "(Copy) 2", "(Copy) 3", dst.
     * Menggunakan database transaction untuk memastikan atomicity.
     */
    public function duplicate(Supplier $supplier, Layup $layup): RedirectResponse
    {
        $this->authorize('duplicate', $layup);

        $duplicatedLayup = DB::transaction(function () use ($supplier, $layup): Layup {
            $baseName = $layup->name.' (Copy)';
            $candidateName = $baseName;
            $counter = 2;

            while ($this->layupRepository->findByNameInSupplier($supplier, $candidateName) !== null) {
                $candidateName = $baseName.' '.$counter;
                $counter++;
            }

            $duplicatedLayup = $this->layupRepository->createForSupplier($supplier, [
                'name' => $candidateName,
                'description' => $layup->description,
            ]);

            foreach ($layup->layers()->orderBy('layer_order')->get() as $layer) {
                $duplicatedLayup->layers()->create([
                    'layer_order' => $layer->layer_order,
                    'thickness' => $layer->thickness,
                    'width' => $layer->width,
                    'angle' => $layer->angle,
                ]);
            }

            return $duplicatedLayup;
        });

        return redirect()
            ->route('suppliers.layups.show', [$supplier, $duplicatedLayup])
            ->with('status', 'Layup duplicated successfully.');
    }

    /**
     * Menampilkan katalog semua layup dari semua supplier dengan pagination.
     * Fitur search berdasarkan nama layup, description, atau nama supplier.
     */
    public function catalog(Request $request): View
    {
        $this->authorize('viewAny', Layup::class);

        $search = trim((string) $request->query('q', ''));

        $layups = Layup::query()
            ->with('supplier:id,name')
            ->withCount('layers')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->orWhereHas('supplier', function ($supplierQuery) use ($search): void {
                            $supplierQuery->where('name', 'like', '%'.$search.'%');
                        });
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('layups.catalog', [
            'layups' => $layups,
            'search' => $search,
        ]);
    }
}
