<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('Coupon name for easier management');
            $table->enum('status', ['active', 'deactive'])->default('deactive');
            $table->string('stripe_price_id')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->enum('discount_type', ['percent', 'flat'])->default('percent');
            $table->decimal('discount', 10, 2);
            $table->date('expiry');
            $table->softDeletes();
            $table->timestamps();
        
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
