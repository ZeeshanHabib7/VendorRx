<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyForeignKeyOnOrderDetailsTable extends Migration
{

    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['order_id']);

            // Add the new foreign key constraint with cascading delete
            $table->foreign('order_id')
                ->references('order_id')
                ->on('orders')
                ->onDelete('cascade');
        });
    }


    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            // Drop the foreign key with cascading delete
            $table->dropForeign(['order_id']);

            // Add the original foreign key constraint without cascading deletion
            $table->foreign('order_id')
                ->references('order_id')
                ->on('orders');
        });
    }
}
