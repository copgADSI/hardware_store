<?php

namespace Tests\Feature\Category;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_categories()
    {
        $response = $this->get(route('categories.index'));
        $response_data = $response->json();
        foreach ($response_data['categories'] as $item) {
            $this->assertDatabaseHas('categories', [
                'category' => $item['category']
            ]);
        }
        $response->assertStatus(200);
    }
}
