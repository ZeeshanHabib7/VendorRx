<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Http\Requests\UserSearchRequest;

class UserControllerTest extends TestCase
{
    // use RefreshDatabase;

    /**
     * Test retrieving all users with pagination.
     *
     * @return void
     */
    public function testGetAllUsers()
    {
        // Create some dummy users for testing
        User::factory()->count(5)->create();

        // Simulate a request with necessary parameters
        $request = new UserSearchRequest([
            'pagination' => true,
            'perPage' => 3,
        ]);

        // Call the controller method
        $response = $this->get('/user/all', $request->all());
        $response->assertStatus(200)
        ->assertJsonStructure([
            "success", "status_code", "message", "data"
        ]);
    }

    /**
     * Test retrieving a specific user by ID.
     *
     * @return void
     */
    public function testGetUserById()
    {
        // Create a dummy user
        $user = User::factory()->create();

        // Simulate a request with ID parameter
        $request = new UserSearchRequest([
            'id' => $user->id,
        ]);

        // Call the controller method
        $response = $this->get('/user/all', $request->all());
        // Assert response status and structure
        $response->assertStatus(200)
        ->assertJsonStructure([
            "success", "status_code", "message", "data"
        ]);
    }
}
