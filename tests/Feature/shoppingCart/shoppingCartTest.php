<?php

namespace Tests\Feature\shoppingCart;

use App\Models\ShoppingCart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class shoppingCartTest extends TestCase
{
    /**
     * TEST PARA ACTUALIZAR PRODUCTO DE CARRITO
     *
     * @return void
     */
    public function test_updateCart()
    {
        $cart = ShoppingCart::inRandomOrder()->first();
        $user = User::find($cart->user_id);
        $response = $this->actingAs($user)->put(route('cart.update'), [
            'product_id' => $cart->product_id,
            'user_id' => $cart->user_id,
            'quantity' => rand(1, $cart->quantity),
        ]);

        $response_data = $response->json();
        $this->assertArrayHasKey('message', $response_data);
        $response->assertStatus(200);
    }


    /**
     * TEST PARA ACTUALIZAR PRODUCTO DE CARRITO
     *
     * @return void
     */
    public function test_remove_product_from_cart()
    {
        $cart = ShoppingCart::inRandomOrder()->first();
        $user = User::find($cart->user_id);
        $response = $this->actingAs($user)->delete(route('cart.remove'), [
            'product_id' => $cart->product_id,
            'user_id' => $cart->user_id,
        ]);

        $response_data = $response->json();
        $this->assertArrayHasKey('message', $response_data);
        $response->assertStatus(200);
    }
}
