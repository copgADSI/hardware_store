<?php

namespace Tests\Feature\user;

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
}
