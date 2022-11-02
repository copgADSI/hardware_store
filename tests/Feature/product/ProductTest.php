<?php

namespace Tests\Feature\product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{

    const CHARACTERS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    protected $randomString = '';
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_list_products()
    {

        $response = $this->get(route('product.list'));
        $response_data = $response->json();
        $this->assertDatabaseHas('products', $response_data);
        $response->assertStatus(200);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_product()
    {
        for ($i = 0; $i < 10; $i++) {
            $index = rand(0, strlen(self::CHARACTERS) - 1);
            $this->randomString .= self::CHARACTERS[$index];
        }
        $user = User::where('role_id', '<>', 2)->inRandomOrder()->first(); //admin
        $response = $this->actingAs($user)->post(route('product.store', [
            'images_carousel' => 'test',
            'name' => "test_{$this->randomString}23",
            'price' => 2000,
            'quantity' => 20,
            'description' => 'test',
            'user_id' => $user->id,
        ]));
        $response_data = $response->json();
        if ($response_data['status']) {
            $this->assertDatabaseHas('products', $response_data['product']);
            $response->assertStatus(200);
        } else {
            $response->assertStatus($response_data['status']);
        }
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_a_delete_product()
    {
        $product = Product::inRandomOrder()->first();
        $user = User::where('role_id', '<>', 2)->inRandomOrder()->first(); //admin
        $response = $this->actingAs($user)->delete(route('product.destroy', [
            'id' => $product->id
        ]));
        $response_data = $response->json();
        if ($response_data['status']) {
            $response->assertStatus(200);
        } else {
            $response->assertStatus($response_data['status']);
        }
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_a_update_product()
    {
        for ($i = 0; $i < 10; $i++) {
            $index = rand(0, strlen(self::CHARACTERS) - 1);
            $this->randomString .= self::CHARACTERS[$index];
        }
        $product = Product::inRandomOrder()->first();
        $user = User::where('role_id', '<>', 2)->inRandomOrder()->first(); //admin
        $response = $this->actingAs($user)->put(route('product.update', [
            'id' => $product->id,
            'name' =>  "test_{$this->randomString}",
        ]));
        $response_data = $response->json();
        if ($response_data['status']) {
            $this->assertDatabaseHas('products', $response_data['product']);
            $response->assertStatus(200);
        } else {
            $response->assertStatus($response_data['status']);
        }
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_show_product()
    {
        $product = Product::inRandomOrder()->first();
        $user = User::inRandomOrder()->first(); //any role
        $response = $this->actingAs($user)->get(route('product.show', [
            'id' => $product->id
        ]));
        $response_data = $response->json();
        if ($response_data['status']) {
            $this->assertDatabaseHas('products', $response_data['product']);
            $response->assertStatus(200);
        } else {
            $response->assertStatus($response_data['status']);
        }
    }
}
