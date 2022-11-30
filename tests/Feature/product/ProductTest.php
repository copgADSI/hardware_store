<?php

namespace Tests\Feature\product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Favorite;
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
        $user = User::inRandomOrder()->first();
        $brands = Brand::inRandomOrder()->get();
        $categories = Category::inRandomOrder()->get();

        $response = $this->actingAs($user)->get(route('product.list', [
            'email' => $user->email,
            'brands_ids'  => json_encode($brands->pluck('id')),
            'categories_ids' => json_encode($categories->pluck('id')),
        ]));
        $response_data = $response->json();
        if ($response_data['status']) {
            foreach ($response_data['products'] as $key => $product) {
                $this->assertDatabaseHas('products', [
                    'id' => $product['id'],
                    'carousel' => $product['carousel'],
                    'name' => $product['name'],
                    'price' => $product['price']
                ]);

                if (!count($product['favorites'])) continue;
                $this->assertDatabaseHas('favorites',  $product['favorites'][0]);
            }
            $response->assertStatus(200);
            return;
        }
        $response->assertStatus(500);
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
        $user = User::where('role_id', '=', 2)->inRandomOrder()->first(); //admin
        $category = Category::inRandomOrder()->first();
        $brand = Brand::inRandomOrder()->first();
        $response = $this->actingAs($user)->post(route('product.store', [
            'carousel' => 'test',
            'name' => "test_{$this->randomString}23",
            'price' => 2000,
            'quantity' => rand(5, 200),
            'description' => 'test',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'brand_id' => $brand->id
        ]));
        dd($response->json());
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
        $user = User::where('role_id', '=', 2)->inRandomOrder()->first(); //admin
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
        $user = User::where('role_id', '=', 2)->inRandomOrder()->first(); //admin
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
            $this->assertDatabaseHas('products', [
                'price' => $response_data['product'],
            ]);
            $response->assertStatus(200);
        } else {
            $response->assertStatus($response_data['status']);
        }
    }

    /**
     * Test para comprobar que un producto se agrega a favoritos
     *
     * @return void
     */
    public function test_add_product_favorite()
    {
        $product = Product::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();
        $response = $this->actingAs($user)->post(route('product.favorite', [
            'product_id' => $product->id,
            'user_id' => $user->id
        ]));
        $response_data = $response->json();
        if ($response_data['status']) {
            $response->assertStatus(200);
        } else {
            $response->assertStatus($response_data['status']);
        }
    }

    /**
     * Test para obtener todos los favoritos del usuario
     *
     * @return void
     */
    public function test_get_favorites()
    {
        $favorites = Favorite::inRandomOrder()->first();
        $user = User::where('id', '=', $favorites->user_id)->first();
        $response = $this->actingAs($user)->get(route('products.getFavorites', [
            'user_id' => $user->id
        ]));
        $response_data = $response->json();

        foreach ($response_data['favorites']['data'] as  $product) {
            $this->assertDatabaseHas('favorites', [
                'id' => $product['id'],
                'product_id' => $product['product_id'],
            ]);
        }
        if ($response_data['status']) {
            $response->assertStatus(200);
        } else {
            $response->assertStatus(404);
        }
    }

    /**
     * Test para eliminar favoritos del usuario
     *
     * @return void
     */
    public function test_delete_favorite()
    {
        $favorites = Favorite::inRandomOrder()->first();
        $user = User::where('id', '=', $favorites->user_id)->first();
        $response = $this->actingAs($user)->delete(route('favorite.destroy', [
            'user_id' => $favorites->user_id,
            'product_id' => $favorites->product_id
        ]));
        $response_data = $response->json();
        dd($response_data);
        foreach ($response_data['favorites']['data'] as  $product) {
            $this->assertDatabaseHas('favorites', [
                'id' => $product['id'],
                'product_id' => $product['product_id'],
            ]);
        }
        if ($response_data['status']) {
            $response->assertStatus(200);
        } else {
            $response->assertStatus(404);
        }
    }

    /**
     * prueba para filtar laptops
     *
     * @return void
     */
    public function test_get_laptops()
    {
        $brands = Brand::all()->random()->limit(rand(1, 7))->get();
        $response = $this->get(route('product.laptops', [
            'prices_range' => [1000, 2000000],
            'brand_ids' => json_decode($brands->pluck('id'))
        ]));
        $response_data = $response->json();
        if ($response_data['status']) {
            foreach ($response_data['products'] as $key => $product) {
                $this->assertDatabaseHas('products', [
                    'id' => $product['id'],
                    'carousel' => $product['carousel'],
                    'name' => $product['name'],
                    'price' => $product['price']
                ]);

                if (!count($product['favorites'])) continue;
                $this->assertDatabaseHas('favorites',  $product['favorites'][0]);
            }
            $response->assertStatus(200);
            return;
        }
        $response->assertStatus(404);
    }

    /**
     * prueba para filtar laptops
     *
     * @return void
     */
    public function test_search_matches()
    {
        $product = Product::inRandomOrder()->first();
        $term = explode('_', $product->name);
        $response = $this->get(route('product.match', [
            'term' => $term[0],
        ]));
        $response_data = $response->json();

        if ($response_data['status']) {
            foreach ($response_data['matches'] as $product) {
                $this->assertDatabaseHas('products', [
                    'id' => $product['id'],
                    'carousel' => $product['carousel'],
                    'name' => $product['name'],
                    'price' => $product['price']
                ]);
            }
            $response->assertStatus(200);
            return;
        }
        $response->assertStatus(404);
    }
}
