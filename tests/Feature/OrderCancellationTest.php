<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Faker\Factory as Faker;

class OrderCancellationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_CancelConfirmedOrders()
    {
        $faker = Faker::create();
        $userRole = Role::where(['name' => 'user'])->first();

        // Creating a new user and assigning 'User' role
        $user = User::factory()->create([
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'password' => 'password123',
        ]);
        $user->assignRole($userRole);

        // Creating an order
        $order = Order::create([
            "reference_no" => $this->generateReferenceNumber(),
            "user_id" => $user->id,
            "grand_total" => 2000,
            "order_status" => "order_confirmed",
            "address" => "USA",
            "payment_status" => "succeeded",
        ]);

        // Sending a login request to simulate authentication process
        $response = $this->post('/api/users/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        //Retrieving the token value from the response
        $token = $response["data"]["token"];

        // Adding the token value manually in Authorization header (Authorization: Bearer tokenValue)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/order/cancel-order/' . $order->order_id);

        //Checking the response
        $response->assertStatus(200);

        $order->refresh();

        // Confirming whether the order_status changed or not
        $this->assertEquals("order_cancelled", $response['data']['order']['order_status']);

        // Confirming the response structure
        $response->assertJsonStructure([
            'success',
            'status_code',
            'message',
            'data' => [
                'non_refundable_amount',
                'msg',
                'order' => [
                    "user_id",
                    "order_no",
                    "order_id",
                    "order_status",
                    "grand_total",
                ]
            ]
        ]);


    }

    private function generateReferenceNumber()
    {
        $currentDate = Carbon::now()->format('Ymd');
        $currentTime = Carbon::now()->format('His');

        return $currentDate . 'DIP' . $currentTime;
    }
}
