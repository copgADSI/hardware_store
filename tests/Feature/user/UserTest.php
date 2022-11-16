<?php

namespace Tests\Feature\user;

use App\Models\Address;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    const CHARACTERS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    protected $randomString = '';

    /**
     * test a inicio de sesión desde la api
     *
     * @return void
     */
    public function test_a_user_login()
    {
        //$user = User::inRandomOrder()->first();
        $response = $this->post(route('user.login', [
            'email' => 'cristiangualteros23@gmail.com',
            'password' => '123456123aza',
        ]));
        $response->assertStatus(200);
    }

    /**
     * test para registro de usuarios
     *
     * @return void
     */
    public function test_a_user_sign_up()
    {
        for ($i = 0; $i < 10; $i++) {
            $index = rand(0, strlen(self::CHARACTERS) - 1);
            $this->randomString .= self::CHARACTERS[$index];
        }
        $response = $this->post(route('user.register', [
            'name' => "test_{$this->randomString}",
            'email' => "test_{$this->randomString}@mail.com",
            'password' => $this->randomString,
            'password_confirmation' => $this->randomString
        ]));

        $response_data = $response->json();
        $this->assertDatabaseHas('users', $response_data);
        $response->assertStatus(200);
    }


    /**+
     * obtener carrito de compras
     * @return void
     */
    public function test_get_shopping_cart(): void
    {
        $user = User::inRandomOrder()->first();
        $response =  $this->actingAs($user)->get(route('user.shopping_cart', [
            'user_id' => $user->id
        ]));

        $response_data = $response->json();
        if ($response_data['status']) {
            foreach ($response_data['shopping_cart'] as $product) {
                $this->assertDatabaseHas('shopping_carts', [
                    'quantity' => $product['quantity_by_product'],
                    'product_id' => $product['product_id']
                ]);
            }
            $this->assertArrayHasKey('total', $response_data);
            $response->assertStatus(200);
            return;
        }
        $response->assertStatus(401);
    }

    /**
     * test para llenar el carrito de compras
     * @return void
     */
    public function test_filling_cart(): void
    {
        $user = User::inRandomOrder()->first();
        $product = Product::inRandomOrder()->first();
        $response =  $this->actingAs($user)->post(route('user.addToCart', [
            'user_id' => $user->id,
            'quantity' => $product->quantity - rand(0, 5),
            'product_id' => $product->id
        ]));

        $response_data = $response->json();
        $this->assertDatabaseHas('shopping_carts', $response_data['product']);
    }

    /**
     * test para obtener las direcciones ingresadas por el usuario
     * @return void
     */
    public function test_get_addresses_by_user(): void
    {
        $user = User::inRandomOrder()->first();
        $response =  $this->actingAs($user)->get(route('addresses.index', [
            'user_id' => $user->id,
        ]));

        $response_data = $response->json();
        if ($response_data['status']) {
            foreach ($response_data['data'] as $address) {
                $this->assertDatabaseHas('addresses', $address);
            }
            $response->assertStatus(200);
            return;
        }
        $response->assertStatus(404);
    }

    /**
     * test para obtener las direcciones ingresadas por el usuario
     * @return void
     */
    public function test_add_address_by_user(): void
    {
        $user = User::inRandomOrder()->first();
        $response =  $this->actingAs($user)->post(route('addresses.store', [
            'user_id' => $user->id,
            'line_one' => 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Libero, harum ea. Tenetur aliquam distinctio vel cupiditate vitae pariatur culpa excepturi obcaecati asperiores labore. Ipsam nobis dolorum asperiores! Quod, iste dolor!',
            'line_two' => 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Libero, harum ea. Tenetur aliquam distinctio vel cupiditate vitae pariatur culpa excepturi obcaecati asperiores labore. Ipsam nobis dolorum asperiores! Quod, iste dolor!',
            'phone' => '12312313'
        ]));

        $response_data = $response->json();
        if ($response_data['status']) {
            $this->assertDatabaseHas('addresses', $response_data['address']);
            $response->assertStatus(200);
            return;
        }
        $response->assertStatus(404);
    }

    /**
     * test para obtener las direcciones ingresadas por el usuario
     * @return void
     */
    public function test_update_address_by_user(): void
    {
        $address = Address::inRandomOrder()->first();
        $user = User::where('id', '=', $address->user_id)->first();
        $response =  $this->actingAs($user)->put(route('addresses.update', [
            'id' => $address->id,
            'user_id' => $user->id,
            'phone' => '12312313'
        ]));

        $response_data = $response->json();
        if ($response_data['status']) {
            $this->assertDatabaseHas('addresses', $response_data['address']);
            $response->assertStatus(200);
            return;
        }
        $response->assertStatus(404);
    }
}
