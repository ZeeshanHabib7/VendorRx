<?php

namespace App\Http\Controllers;


use App\Http\Requests\ProductRequest;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use Exception;

class ProductController extends Controller
{
    private $isPaginate = false;

    public function index(ProductRequest $request)
    {
        try{
            $query_res = Product::when($request->filled('startDate') && $request->filled('endDate'), function ($query) use ($request) {
                return $query->whereBetween('date', [$request->startDate, $request->endDate]);
            })
            ->when($request->start_date, function ($query, $start_date) {
                return $query->where('date', '>=', $start_date);
            })
            ->when($request->end_date, function ($query, $end_date) {
                return $query->where('date', '<=', $end_date);
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
                $pageSize = $request->input('pageSize', 10);
                $pageNum = $request->input('pageNum', 1);
                $this->isPaginate = true;
                $products = $query_res->paginate($pageSize, ['*'], 'page', $pageNum);
            } else{
                $products = $query_res->get();
            }

            return ResponseHelper::success(ProductCollection::collection($products) , 'Data fetched successfully!',200,$this->isPaginate);

        }
        catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), $e->getCode());
        }
      
    }


}
