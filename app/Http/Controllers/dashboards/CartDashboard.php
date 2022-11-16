<?php

namespace App\Http\Controllers\dashboards;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CartDashboard extends Controller
{
    /**
     * @param int $user_id
     * @return Collection
     */
    public function getCartData(int $user_id): Collection
    {
        return DB::table('shopping_carts')
            ->selectRaw('
                    shopping_carts.quantity as quantity_by_product,

                    shopping_carts.*,
                    products.*
                ')
            ->join('products', 'shopping_carts.product_id', '=', 'products.id')
            ->join('users', 'shopping_carts.user_id', '=', 'users.id')
            ->where('shopping_carts.user_id', '=', $user_id)
            ->get();
    }
}
