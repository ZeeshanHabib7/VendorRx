<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Products_SA;
use Faker\Factory as Faker;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $faker = Faker::create();

        for ($i = 1; $i < 50; $i++) {

            $product = new Products_SA;
            $product->name = $faker->word;
            $product->price = $faker->randomFloat(2, 100, 2000);
            $product->brand = $faker->company;
            $product->created_at = $faker->date();
            $product->save();
        }


    }
}
