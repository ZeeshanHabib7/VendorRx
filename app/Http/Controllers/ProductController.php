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
            });
            
    
            
            if($request->input('paginate') == 'true'){
                $paginatedData = $this->paginate($products->paginate());
                return ResponseHelper::success($paginatedData, 'Data fetched successfully!');
            } else {
                return ResponseHelper::success(new ProductCollection($products->get()), 'Data fetched successfully!');
            }

        }
        catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), $e->getCode());
        }
      
    }

    public function paginate($data = []) {
        $paginationArray = null;

        if ($data != null) {
            // Initialize pagination array with list of items
            $paginationArray = [
                'list' => $data->items(),
                'pagination' => []
            ];

            // Add pagination details
            $paginationArray['pagination'] = [
                'total' => $data->total(),
                'current' => $data->currentPage(),
                'first' => 1,
                'last' => $data->lastPage(),
                'previous
                ' => $data->currentPage() > 1 ? $data->currentPage() - 1 : 0,
                'next' => $data->hasMorePages() ? $data->currentPage() + 1 : $data->lastPage(),
                'pages' => $data->lastPage() > 1 ? range(1, $data->lastPage()) : [1],
                'from' => $data->firstItem(),
                'to' => $data->lastItem()
            ];

            return $this->result = $paginationArray;
        }

        return $paginationArray;
    }

}
