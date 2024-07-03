<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use Carbon\Carbon;
use App\Helpers\ResponseHelper;


class ProductsController extends Controller
{
    public function getData(Request $req)
    {

        $products = Products::all();

        if (!$req->query()) {

            return response()->json(ResponseHelper::sendResponse(true, 200, "Data Fetched Successfully", $products));
        }

        //initializing default values
        $startDate = Carbon::createFromFormat('Y-m-d', '1999-07-01')->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', '2050-12-31')->endOfDay();
        $minPrice = 0;
        $maxPrice = PHP_INT_MAX;
        $brand = "";
        $keyWord = "";
        $filteredProducts = $products;


        if ($req->query('startDate'))
            $startDate = Carbon::createFromFormat('Y-m-d', $req->query('startDate'))->startOfDay();

        if ($req->query('endDate'))
            $endDate = Carbon::createFromFormat('Y-m-d', $req->query('endDate'))->endOfDay();

        if ($req->query('minPrice'))
            $minPrice = $req->query('minPrice');

        if ($req->query('maxPrice'))
            $maxPrice = $req->query('maxPrice');

        if ($req->query('brand'))
            $brand = $req->query('brand');

        if ($req->query('keyWord'))
            $keyWord = $req->query('keyWord');



        $filteredProducts = $products->filter(function ($product) use ($startDate, $endDate, $minPrice, $maxPrice, $brand, $keyWord) {
            return $product->date >= $startDate &&
                $product->date <= $endDate &&
                $product->price >= $minPrice &&
                $product->price <= $maxPrice &&
                ($brand == "" || $product->brand == $brand) &&
                ($keyWord == "" || (stripos($product->name, $keyWord) !== false || stripos($product->brand, $keyWord) !== false));
        });


        if (!$filteredProducts->isEmpty()) {

            //For making it a json response i converted it into an associative array
            $productList = $filteredProducts->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'date' => $product->date,
                    'brand' => $product->brand
                ];
            })->values()->all();

            return response()->json(ResponseHelper::sendResponse(true, 200, "Data Fetched Successfully", $productList));

        } else {

            return response()->json(ResponseHelper::sendResponse(false, 404, "No Products Found :(", $products));
        }
    }
}
