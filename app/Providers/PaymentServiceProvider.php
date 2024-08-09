<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Interfaces\PaymentServiceInterface;
use App\Services\StripePaymentService;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind the PaymentServiceInterface to StripePaymentService
        $this->app->bind(PaymentServiceInterface::class, StripePaymentService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
