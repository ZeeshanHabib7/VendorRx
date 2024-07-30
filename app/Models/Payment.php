<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_reference',
        'amount',
        'paying_method',
        'payment_note',
        'response'
    ];


    // relationship with the Order model
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
