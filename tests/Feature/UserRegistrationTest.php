<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration.
     *
     * @return void
     */
    public function testUserCanRegister()
    {
        $response = $this->post('/api/users/register', [
            'name' => 'Anas Ahmed',
            'email' => 'anas@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
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

        $this->assertDatabaseHas('users', [
            'email' => 'anas@gmail.com',
            'name' => 'Anas Ahmed'
        ]);
    }

    /**
     * Test user registration with missing fields.
     *
     * @return void
     */
    public function testUserRegistrationFailsWithMissingFields()
    {
        $response = $this->postJson('/api/users/register', [
            'name' => 'Nasir Ali',
            // Missing email and password fields
        ]);

        print_r($response);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message.email', 'message.password']);


    }

    /**
     * Test user registration with invalid email.
     *
     * @return void
     */
    public function testUserRegistrationFailsWithInvalidEmail()
    {
        $response = $this->postJson('/api/users/register', [
            'name' => 'Mohammad Hassan Khan',
            'email' => 'hassan',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test user registration with password mismatch.
     *
     * @return void
     */
    public function testUserRegistrationFailsWithPasswordMismatch()
    {
        $response = $this->postJson('/api/users/register', [
            'name' => 'Shaheer Beig',
            'email' => 'Shaheer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password321',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function testUserRegistrationFailsWithPasswordLengthLessThan8()
    {
        $response = $this->postJson('/api/users/register', [
            'name' => 'Ali Ahmed',
            'email' => 'ali@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
