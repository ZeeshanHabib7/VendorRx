<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\ProductCollection;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(ProductRequest $request)
    {
    
        try{
            $products = Product::when($request->filled('startDate') && $request->filled('endDate'), function ($query) use ($request) {
                return $query->whereBetween('date', [$request->startDate, $request->endDate]);
            })
            ->when($request->filled('brand'), function ($query) use ($request) {
                return $query->where('brand', $request->brand);
            })
            ->when($request->filled('minPrice') && $request->filled('maxPrice'), function ($query) use ($request) {
                return $query->whereBetween('price', [$request->minPrice, $request->maxPrice]);
            })
            ->when($request->filled('keyword'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->keyword . '%');
            })
            ->get();
    
            return ResponseHelper::success(new ProductCollection($products), 'Data fetched successfully!');
        }
        catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), $e->getCode());
        }
      
    }
}
