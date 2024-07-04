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
            $table->float('price')->change();
            $table->string('brand', 50)->nullable()->change();
            if (Schema::hasColumn('products', 'date')) {
                $table->dropColumn('date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('price')->change();
            $table->string('brand', 50)->nullable(false)->change();
            $table->timestamp('date')->nullable();
        });
    }
};
