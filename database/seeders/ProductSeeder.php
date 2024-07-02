<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Product 01',
                'price' => 500,
                'date' => '2024-07-01',
                'brand' => 'Brand A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Product 02',
                'price' => 200,
                'date' => '2024-07-02',
                'brand' => 'Brand B',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Product 03',
                'price' => 350,
                'date' => '2024-07-03',
                'brand' => 'Brand B',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
