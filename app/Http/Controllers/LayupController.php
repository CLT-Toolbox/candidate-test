<?php

namespace App\Http\Controllers;

use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\Http\Request;

class LayupController extends Controller
{
   public function index(Supplier $supplier)
{
    return $supplier->layups;
}

public function store(Request $request, Supplier $supplier)
{
    return $supplier->layups()->create($request->all());
}

public function show(Supplier $supplier, Layup $layup)
{
    return $layup;
}

public function update(Request $request, Supplier $supplier, Layup $layup)
{
    $layup->update($request->all());
    return $layup;
}

public function destroy(Supplier $supplier, Layup $layup)
{
    $layup->delete();
    return response()->noContent();
}
}
