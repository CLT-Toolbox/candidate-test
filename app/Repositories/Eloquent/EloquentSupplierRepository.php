<?php

namespace App\Repositories\Eloquent;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentSupplierRepository implements SupplierRepositoryInterface
{
    public function paginateWithCounts(int $perPage = 10): LengthAwarePaginator
    {
        return Supplier::query()
            ->withCount('layups')
            ->withCount([
                'layups as layers_count' => fn ($query) => $query->join('clt_layers', 'clt_layers.layup_id', '=', 'clt_layups.id'),
            ])
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Supplier
    {
        return Supplier::query()->create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);

        return $supplier->refresh();
    }

    public function delete(Supplier $supplier): void
    {
        $supplier->delete();
    }

    public function findWithRelations(Supplier $supplier): Supplier
    {
        return $supplier->load([
            'layups.layers',
        ]);
    }
}
