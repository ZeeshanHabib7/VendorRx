<?php

namespace App\Jobs;

use App\Mail\OrderPlaced;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Collection;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderDetails; // This should be a collection
    protected $user;

    public function __construct(Collection $orderDetails, User $user)
    {
        $this->orderDetails = $orderDetails;
        $this->user = $user;
    }

    public function handle()
    {
        try {
            Mail::to($this->user->email)->send(new OrderPlaced($this->orderDetails, $this->user));
        } catch (\Exception $e) {
            // Log or handle exception as needed
            \Log::error('Failed to send email: ' . $e->getMessage());
            throw $e;
        }
    }
}
