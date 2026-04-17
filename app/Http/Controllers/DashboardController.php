<?php

namespace App\Http\Controllers;

use App\Models\Supplier;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSuppliers = Supplier::count();

        return view('dashboard', [
            'totalSuppliers' => $totalSuppliers,
        ]);
    }
}
