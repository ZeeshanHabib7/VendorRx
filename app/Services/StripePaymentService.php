<?php
namespace App\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;
use App\Http\Interfaces\PaymentServiceInterface;

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
}
