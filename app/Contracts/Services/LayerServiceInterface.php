<?php

namespace App\Contracts\Services;

use App\Models\Layer;
use App\Models\Layup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LayerServiceInterface
{
    public function index(Layup $layup, array $filters = []): LengthAwarePaginator;

    public function store(Layup $layup, array $data): Layer;

    public function show(Layer $layer): Layer;

    public function update(Layer $layer, array $data): Layer;

    public function destroy(Layer $layer): void;
}
