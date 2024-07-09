<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController_HR extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('startDate') && $request->has('endDate')) {
            $query->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        if ($request->has('brand')) {
            $query->where('brand', $request->brand);
        }

        if ($request->has('minPrice') && $request->has('maxPrice')) {
            $query->whereBetween('price', [$request->minPrice, $request->maxPrice]);
        }

        if ($request->has('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $products = $query->paginate(10); // Paginate results, 10 products per page


        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => ['Data fetched successfully!'],
            'data' => ['list' => $products]
        ], 200);
    }
}
