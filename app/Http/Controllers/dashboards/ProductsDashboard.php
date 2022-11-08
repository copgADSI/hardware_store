<?php

namespace App\Http\Controllers\dashboards;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

class ProductsDashboard extends Controller
{

    /**
     * método usado para capturar los productos favoritos por usuario
     * @param int $user_id
     * @return Collection
     */
    public function getProductsAndFavoritesByUser(int $user_id = null): Collection
    {
        return Product::query()
            ->where('quantity', '>', 0)
            ->with(['favorites' => function ($hasMany) use ($user_id) {
                $hasMany->where('user_id', '=', $user_id);
            }])
            ->orderBy('price')
            ->get();
    }

    /**
     * método usado para capturar los productos favoritos por usuario
     * @param int $user_id
     * @return Collection
     */
    public function getShoppingCartByUser(int $user_id): Collection
    {
        return Product::query()
            ->where('quantity', '>', 0)
            ->with(['favorites' => function ($hasMany) use ($user_id) {
                $hasMany->where('user_id', '=', $user_id);
            }])
            ->orderBy('price')
            ->get();
    }


    /**
     * método usado para capturar los portátiles
     * @param array $filters
     * @return Collection
     */
    public function handleLaptops(array $filters = null): SupportCollection
    {
        $query = DB::table('products');
        if (!is_null($filters['brand_ids'])) {
            $query->whereIn('brand_id', $filters['brand_ids']);
        }
        $query->whereBetween('price', $filters['prices_range']);
        return $query->get();
    }
}
