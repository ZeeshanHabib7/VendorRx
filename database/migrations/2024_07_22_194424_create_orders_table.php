<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->string('reference_no');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('item')->nullable()->default(0);
            $table->integer('total_quantity')->default(0);
            $table->double('total_tax', 10, 2)->default(0);
            $table->double('total_price', 10, 2)->default(0);
            $table->double('total_discount', 10, 2)->default(0);
            $table->double('grand_total', 10, 2)->default(0);
            $table->double('order_discount', 10, 2)->default(0);
            $table->double('shipping_cost', 10, 2)->default(200);
            $table->enum('sale_status', ['order_placed', 'order_dispatched', 'order_completed', 'order_returned', 'order_rejected'])->default('order_placed');
            $table->double('paid_ammount', 10, 2, true)->nullable();
            $table->enum('payment_status', ["paid", "unpaid"])->default('unpaid');
            $table->text('address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

