<?php
namespace App\Helpers;

class RouteHelper
{
    public static function layupShow($layer)
    {
        // Safe route generation dengan fallback
        if ($layer && $layer->layup) {
            return route('dashboard.layups.show', $layup);
        }
        
        // Fallback ke suppliers index kalau layup null
        return route('dashboard.suppliers.index');
    }
}