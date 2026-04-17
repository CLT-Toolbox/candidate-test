<?php

namespace App\Contracts\Services;

use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LayupServiceInterface
{
    public function index(Supplier $supplier, array $filters = []): LengthAwarePaginator;

    public function store(Supplier $supplier, array $data): Layup;

    public function show(Layup $layup): Layup;

    public function update(Layup $layup, array $data): Layup;

    public function destroy(Layup $layup): void;
}
