<?php

namespace App\Http\Controllers;


use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use Exception;

class ProductController extends Controller
{
    private $isPaginate = false;
    private $defaultPageSize = 10;
    private $defaultPageNum = 1;

    public function index(ProductRequest $request)
    {
        try{
            $query_res = Product::when($request->filled('startDate') && $request->filled('endDate'), function ($query) use ($request) {
                return $query->whereBetween('date', [$request->startDate, $request->endDate]);
            })
            ->when($request->start_date, function ($query, $startDate) {
                return $query->where('date', '>=', $startDate);
            })
            ->when($request->end_date, function ($query, $endDate) {
                return $query->where('date', '<=', $endDate);
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
                $pageSize = $request->input('pageSize', $this->defaultPageSize);
                $pageNum = $request->input('pageNum', $this->defaultPageNum);
                $this->isPaginate = true;
                $products = $query_res->paginate($pageSize,$pageNum);
            } else{
                $products = $query_res->get();
            }

            return successResponse( 'Data fetched successfully!',ProductCollection::collection($products),$this->isPaginate, 200);

        }
        catch (Exception $e) {
            return handleException($e);
        }
      
    }


}
