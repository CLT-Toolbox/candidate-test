<?php

namespace App\Services;

use App\Contracts\Services\LayupServiceInterface;
use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LayupService implements LayupServiceInterface
{
    public function index(Supplier $supplier, array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 15);

        return $supplier->layups()
            ->with('layers')
            ->when(
                $filters['name'] ?? null,
                fn ($query, $name) => $query->where('name', 'like', '%'.$name.'%')
            )
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function store(Supplier $supplier, array $data): Layup
    {
        return $supplier->layups()->create($data)->load('layers');
    }

    public function show(Layup $layup): Layup
    {
        return $layup->loadMissing(['supplier', 'layers']);
    }

    public function update(Layup $layup, array $data): Layup
    {
        $layup->update($data);

        return $layup->load(['supplier', 'layers']);
    }

    public function destroy(Layup $layup): void
    {
        $layup->delete();
    }
}
