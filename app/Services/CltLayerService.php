<?php

namespace App\Services;

use App\Repositories\Interfaces\CltLayerRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CltLayerService extends BaseService
{
    public function __construct(CltLayerRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function reorder(array $layersData): void
    {
        DB::transaction(function () use ($layersData) {
            foreach ($layersData as $index => $data) {
                $this->repository->update($data['id'], [
                    'layer_order' => $index + 1,
                    'thickness'   => $data['thickness'] ?? null,
                    'width'       => $data['width'] ?? null,
                    'angle'       => $data['angle'] ?? null,
                ]);
            }
        });
    }
}
