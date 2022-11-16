<?php

namespace App\Http\Controllers\dashboards;

use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CategoriesDashboard extends Controller
{
    /**
     * Obtener la categoria y el total de productos
     * @return Collection
     */
    public function getCountByCategory(): Collection
    {
        return DB::table('categories')
            ->selectRaw('categories.id, category, count(*) as total')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->groupBy('categories.id')
            ->get();
    }
}
