<?php

namespace App\Repositories;

use App\Models\CltLayer;
use App\Repositories\Interfaces\CltLayerRepositoryInterface;

class CltLayerRepository extends BaseRepository implements CltLayerRepositoryInterface
{
    public function __construct(CltLayer $model)
    {
        parent::__construct($model);
    }
}
