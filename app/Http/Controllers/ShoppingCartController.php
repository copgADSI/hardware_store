<?php

namespace App\Http\Controllers;

use App\Http\Controllers\dashboards\CartDashboard;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShoppingCartController extends Controller
{
    protected $cartDashboard;

    public function __construct(CartDashboard $cartDashboard)
    {
        $this->cartDashboard = $cartDashboard;
    }

    /**
     * Método usado para desplegar el carrito de compras
     * del usuario logeado.
     * @param Request $request
     * @param ShoppingCart $shoppingCart
     * @return \Illuminate\Http\Response
     */
    public function getShoppingCart(Request $request, ShoppingCart $shoppingCart)
    {
        $request->validate([
            'user_id' => 'required'
        ]);
        $cart = $this->cartDashboard->getCartData($request->user_id);
        if ($cart->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Carrito de compras vacío'
            ], 401);
        }
        return response()->json([
            'status' => true,
            'shopping_cart' => $cart,
            'total' => $cart->sum('subtotal'),
            'total_products' => $cart->sum('quantity_by_product')
        ], 200);
    }

    /**
     * Método para llenar el carrito de compras del usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param ShoppingCart $shoppingCart
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request, ShoppingCart $shoppingCart)
    {
        $request->validate([
            'user_id' => 'required|numeric',
            'product_id' => 'required|numeric',
            'quantity' => 'required|numeric'
        ]);
        $product = Product::where('id', $request->product_id)->first();
        if ($product->quantity <= 0) {
            return response()
                ->json('Producto sin unidades disponibles', 404);
        }

        if ($request->quantity > $product->quantity) {
            return response()
                ->json('Superas la cantidad de productos disponibles', 404);
        }

        $subtotal = $product->price * $request->quantity;
        $product = $shoppingCart->updateOrCreate([
            'product_id' => $request->product_id,
            'user_id' => $request->user_id,
        ]);


        DB::transaction(function () use ($product, $request, $subtotal) {
            $product->quantity += $request->quantity;
            $product->subtotal += $subtotal;
            $product->save();
        });

        return response()->json([
            'status' => true,
            'message' => '¡Producto agregado con éxito!',
            'product' => $product
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
