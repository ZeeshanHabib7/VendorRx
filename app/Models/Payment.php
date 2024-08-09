<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

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
