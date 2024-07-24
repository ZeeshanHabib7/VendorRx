<?php

namespace App\Http\Interfaces;

interface PaymentServiceInterface
{
    // Create Payment Method Card 
    public function createCard(array $payload);

    // Function to Charge the payment
    public function createPaymentIntent(array $payload);
}