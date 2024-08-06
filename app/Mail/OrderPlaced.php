<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\OrderDetail_SA;
use App\Models\User;

class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public $orderDetails;
    public $user;

    public function __construct($orderDetails, User $user)
    {
        $this->orderDetails = $orderDetails;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Order Confirmation')
                    ->view('emails.order_confirmation')
                    ->with([
                        'orderDetails' => $this->orderDetails,
                        'user_name' => $this->user->name,
                    ]);
    }
}
