<?php

namespace App\Http\Controllers\dashboards;

use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReviewsDashboard extends Controller
{
    /**
     * Obtener las reseñas por cada producto
     */
    public function totalReviews(): Collection
    {
        return DB::table('reviews')
            ->selectRaw('count(product_id) as reviews')
            ->groupBy('product_id')
            ->get();
    }
}
