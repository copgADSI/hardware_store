<?php

namespace Tests\Feature\address;

use App\Models\Address;
use App\Models\City;
use App\Models\Departament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    /**
     * Comprobar creación de una nueva dirección para el usuario.
     *
     * @return void
     */
    public function test_a_create_address()
    {
        $user = User::inRandomOrder()->first();
        $departament = Departament::inRandomOrder()->first();
        $city = City::where('departament_id', $departament->id)->first();
        $fields = [
            'user_id' => $user->id,
            'line_one' => 'test dirección' . now()->toTimeString(),
            'city_id' => $city->id,
            'phone' => 3116862859,
            'departament_id' => $departament->id
        ];
        $response = $this->actingAs($user)->post(route('addresses.store', $fields));
        $response_data = $response->json();
        $this->assertDatabaseHas('addresses', $fields);
        $response->assertStatus(200);
    }

    /**
     * Comprobar la actualizarción a otra dirección principal.
     *
     * @return void
     */
    public function test_update_principal_address()
    {

        $address = Address::inRandomOrder()->first();
        $user = User::find(['id' => $address->user_id])->first();
        $fields = [
            'id' => $address->id,
            'user_id' => $user->id,
        ];
        $response = $this->actingAs($user)->put(route('addresses.principal', $fields));
        $response_data = $response->json();
        $this->assertDatabaseHas('addresses', $fields);
        $response->assertStatus(200);
    }
}
