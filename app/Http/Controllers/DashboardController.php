<?php

namespace App\Http\Controllers;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'stats' => [
                'suppliers' => Supplier::query()->count(),
                'layups' => Layup::query()->count(),
                'layers' => Layer::query()->count(),
            ],
            'recentSuppliers' => Supplier::query()
                ->withCount('layups')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
