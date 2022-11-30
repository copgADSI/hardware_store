<?php

namespace Tests\Feature\review;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class reviewTest extends TestCase
{
    /**
     * test para obtener las reseñas de cada producto
     *
     * @return void
     */
    public function test_get_reviews_by_product(): void
    {
        $response = $this->get(route('reviews.index'));
        $response_data = $response->json();
        dd($response_data);
        $response->assertStatus(200);
    }
}
