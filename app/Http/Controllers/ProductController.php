<?php

namespace App\Http\Controllers;


use App\Http\Requests\ProductRequest;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use Exception;

class ProductController extends Controller
{

    public function index(ProductRequest $request)
    {
        $isPaginate = false;

        try{
            $query_res = Product::when($request->filled('startDate') && $request->filled('endDate'), function ($query) use ($request) {
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
            });        
            
            if($request->input('paginate') == 'true'){
                $isPaginate = true;
                $products = $query_res->paginate();
            } else{
                $products = $query_res->get();
            }

            return ResponseHelper::success(ProductCollection::collection($products) , 'Data fetched successfully!',200,$isPaginate);

        }
        catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), $e->getCode());
        }
      
    }


}
