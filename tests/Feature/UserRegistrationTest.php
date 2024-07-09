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
        $response = $this->postJson('/api/users/register', [
            'name' => 'Syed Samroze Ali',
            'email' => 'samroze@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'sucess',
                'status_code',
                'message',
                'data' => [
                    'token' => [
                        'acess_token',
                        'bearer',
                        'expires_in'
                    ],
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'samroze@gmail.com',
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
            'name' => 'Samroze Ali',
            // Missing email and password fields
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
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
