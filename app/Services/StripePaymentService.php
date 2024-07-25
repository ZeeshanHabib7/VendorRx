<?php
namespace App\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;
use App\Http\Interfaces\PaymentServiceInterface;
use Stripe\Product as StripeProduct;
use Stripe\Price as StripePrice;

class StripePaymentService implements PaymentServiceInterface
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    }

    public function createCard(array $payload)
    {
        try {
            return Customer::createSource(
                $payload['customer_id'],
                ['source' => $payload['token']]
            );
        } 
        catch (\Exception $e) {
            return handleException($e);
        }
    }

    public function createPaymentIntent(array $payload)
    {
        try {
            return PaymentIntent::create([
                'amount' => $payload['amount'] * 100, 
                'currency' => 'usd',
                'customer' => $payload['customer_id'],
                'payment_method' => $payload['card_id'],
                'off_session' => true,
                'confirm' => true,
            ]);
        } 
        catch (\Exception $e) {
            return handleException($e);
        }
    }

    public function createCustomer(array $payload)
    {
        try {
            return Customer::create([
                'name' => $payload['name'],
                'email' => $payload['email'],
            ]);
        } 
        catch (\Exception $e) {
            return handleException($e);
        }
    }

    public function createProduct(array $payload)
    {
        try {
              // Create Stripe Product
            return StripeProduct::create([
                'name' => $payload['name']
            ]);
        } 
        catch (\Exception $e) {
            return handleException($e);
        }
    }

    public function createPrice(array $payload)
    {
        try {
            return StripePrice::create([
                'unit_amount' => $payload['price'] * 100,
                'currency' => 'usd',
                'product' => $payload['stripe_product_id'],
            ]);
        } 
        catch (\Exception $e) {
            return handleException($e);
        }
    }
}
