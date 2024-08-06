<?php

namespace App\Http\Interfaces;

interface PaymentServiceInterface
{
    // Create Payment Method Card 
    public function createCard(array $payload);

    // Function to Charge the payment
    public function createPaymentIntent(array $payload);

    // Create User/Customer 
    public function createCustomer(array $payload);

    // Create product 
    public function createProduct(array $payload);

    // Create Price 
    public function createPrice(array $payload);

    // Get Price By Product Id
    public function getPriceByProductId($productId);
}