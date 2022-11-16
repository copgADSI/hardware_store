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
     * @param array $filters
     * @return Collection
     */
    public function getProductsAndFavoritesByUser(int $user_id = null, array $filters): Collection
    {
        $query = Product::query()
            ->where('quantity', '>', 0);
        if (count($filters['brands'])) {
            $query->whereIn('brand_id', $filters['brands']);
        }
        if (count($filters['categories'])) {
            $query->whereIn('category_id', $filters['categories']);
        }
        $query->with(['favorites' => function ($hasMany) use ($user_id) {
            $hasMany->where('user_id', '=', $user_id);
        }])
            ->orderBy('price');

        return $query->get();
    }


    /**
     * método usado para obtener detalles del producto.
     * @param int $product_id
     * @return SupportCollection
     */
    public function getSingleProduct(int $product_id): SupportCollection
    {
        return DB::table('products')
            ->selectRaw('products.*, categories.category, brands.brand')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->where('products.id', '=', $product_id)
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

    /**
     * método usado para buscar coicidencias
     * @param string $term
     * @return Collection
     */
    public function searchProducts(string $term): SupportCollection
    {
        return DB::table('products')
            ->selectRaw('products.*, brands.brand, categories.category')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->where('products.name', 'LIKE', '%' . $term . '%')
            ->orWhere('brands.brand', 'LIKE', '%' . $term . '%')
            ->orWhere('categories.category', 'LIKE', '%' . $term . '%')
            ->get();
    }
}
