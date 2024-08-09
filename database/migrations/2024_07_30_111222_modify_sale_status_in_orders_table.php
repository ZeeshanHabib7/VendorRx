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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('sale_status');
            $table->enum('order_status', ['order_placed', 'order_dispatched', 'order_completed', 'order_returned', 'order_rejected', 'order_confirmed', 'order_cancelled'])->default('order_placed')->after('shipping_cost');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_status');
            $table->enum('sale_status', ['order_placed', 'order_dispatched', 'order_completed', 'order_returned', 'order_rejected'])->default('order_placed')->after('shipping_cost');

        });
    }
};
