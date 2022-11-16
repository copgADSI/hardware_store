<?php

namespace App\Http\Controllers\dashboards;

use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BrandsDashboard extends Controller
{
    /**
     * Obtener las marcas  y el total
     * @return Collection
     */
    public function getCountByBrands(): Collection
    {
        return DB::table('brands')
            ->selectRaw('brands.id,brand, count(*) as total')
            ->join('products', 'brands.id', '=', 'products.brand_id')
            ->groupBy('brands.id')
            ->get();
    }
}
