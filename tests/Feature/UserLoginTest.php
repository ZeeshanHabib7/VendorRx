<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */


    public function testUserCanLogin()
    {
        User::factory()->create([
            'name' => 'Anas Ahmed',
            'email' => 'anas@gmail.com',
            'password' => 'password123',
        ]);


        $response = $this->post('/api/users/login', [
            'email' => 'anas@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'status_code',
                'message',
                'data' => [
                    'token' => [
                        'original' => [
                            'access_token',
                            'token_type',
                        ],
                    ],
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                ]
            ]);

    }

    public function testUserRegistrationFailsWithMissingFields()
    {
        $response = $this->postJson('/api/users/login', [
            'name' => 'Anas Ahmed',
            // Missing email and password fields
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message.email', ['Email is required.'])
            ->assertJsonPath('message.password', ['Password field is required for the registration.']);

    }

    /**
     * Test user registration with invalid email.
     *
     * @return void
     */
    public function testUserRegistrationFailsWithInvalidEmail()
    {
        $response = $this->postJson('/api/users/login', [
            'email' => 'anas',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message.email', ['Enter a valid email.']);
    }

    public function testUserLoginFailsWithInvalidCredentials()
    {
        $response = $this->postJson('/api/users/login', [

            'email' => 'haris@gmail.com',
            'password' => 'hello123',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message.email', ['The user does not exist. Please Register yourself.']);

    }



}
