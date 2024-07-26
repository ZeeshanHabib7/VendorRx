<?php
namespace App\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;
use App\Http\Interfaces\PaymentServiceInterface;
use Stripe\Product as StripeProduct;
use Stripe\Price as StripePrice;
use Exception;
use Stripe\Exception\ApiErrorException;

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
        catch (ApiErrorException $e) {
            throw $e;
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
        catch (Exception $e) {
            throw $e;
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
        catch (Exception $e) {
            throw $e;
        }
    }

    public function createProduct(array $payload)
    {
        try {
            // array to be returned
            $result = [];
              // Create Stripe Product
            $stripeProduct = StripeProduct::create([
                'name' => $payload['name']
            ]);
           
            // chech if stripe product is created
            if($stripeProduct){
              // add stripe product id in result array
              $result["stripe_product_id"] = $payload["stripe_product_id"] = $stripeProduct->id;
              // create stripe price for the product created
              $stripeProduct =  $this->createPrice($payload);
              // add stripe price id in result array
              $result['stripe_price_id'] = $stripeProduct->id;
            }
           
            // return the result array
            return $result;
        } 
        catch (Exception $e) {
            throw $e;
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
        catch (Exception $e) {
            throw $e;
        }
    }
}
