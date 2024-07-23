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
        Schema::table('products', function (Blueprint $table) {
            $table->string('barcode_symbology')->nullable();
            $table->integer('brand_id')->nullable();
            $table->integer('category_id');
            $table->boolean('is_batch')->nullable();
            $table->string('cost');
            $table->double('qty')->nullable();
            $table->double('alert_quantity')->nullable();
            $table->tinyInteger('promotion')->nullable();
            $table->string('promotion_price')->nullable();
            $table->date('starting_date')->nullable();
            $table->date('last_date')->nullable();
            $table->longText('image')->nullable();
            $table->tinyInteger('featured')->nullable()->default(1);
            $table->text('product_details')->nullable();
            $table->boolean('is_active')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'barcode_symbology',
                'brand_id',
                'category_id',
                'is_batch',
                'cost',
                'qty',
                'alert_quantity',
                'promotion',
                'promotion_price',
                'starting_date',
                'last_date',
                'image',
                'featured',
                'product_details',
                'is_active'
            ]);
        });
    }
};
