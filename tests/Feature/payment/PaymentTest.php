<?php

namespace Tests\Feature\payment;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_poorPayment()
    {
        $user = User::inRandomOrder()->first();
        $response = $this->actingAs($user)->post(route('payment.process',[
            'user_id' => $user->id,
            'number' => 4242424242424242,
            'cvc' => 123,
            'expiration_date' => '0223',
            'name_on_card' => $user->name,
            'phone' => 3214888230
        ]));
        $response_data = $response->json();
        dd($response_data);
        $response->assertStatus(200);
    }
}
