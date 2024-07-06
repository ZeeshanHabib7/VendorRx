<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products_SA;
use App\Helpers\ResponseHelper;
use App\Http\Requests\ProductRequest_SA;
use App\Http\Resources\ProductResource_SA;
use App\Helpers\PaginationHelper;


class ProductsController_SA extends Controller
{

    public function getData(ProductRequest_SA $request)
    {

        if (!$request->query()) {

            return response()->json(ResponseHelper::sendResponse(true, 200, "Data Fetched Successfully", Products_SA::all(), $request->paginate, $request->pageSize, $request->pageNo));

        }

        $query = Products_SA::query();

        // Apply filters
        if ($request->has('name')) {
            $query->where('name', 'LIKE', '%' . $request->input('name') . '%');
        }

        if ($request->has('startDate') && $request->has('endDate')) {
            $query->whereBetween('created_at', [$request->input('startDate'), $request->input('endDate')]);
        } else {

            if ($request->has('startDate')) {
                $query->where('created_at', '>=', $request->input('startDate'));
            } elseif ($request->has('endDate')) {
                $query->where('created_at', '<=', $request->input('endDate'));
            }
        }

        if ($request->has('minPrice')) {
            $query->where('price', '>=', $request->input('minPrice'));
        }

        if ($request->has('maxPrice')) {
            $query->where('price', '<=', $request->input('maxPrice'));
        }

        if ($request->has('brand')) {
            $query->where('brand', $request->input('brand'));
        }

        if ($request->has('keyWord')) {
            $query->where('brand', 'LIKE', '%' . $request->keyWord . '%')->orWhere('name', 'LIKE', '%' . $request->keyWord . '%');
        }


        if ($query->exists()) {

            if ($request->paginate) {
                echo "in paginate";
                $myData = $query->paginate($request->pageSize, ['*'], 'page', $request->pageNo);

                return response()->json(ResponseHelper::sendResponse(true, 200, "Data Fetched Successfully", $myData, $request->paginate, $request->pageSize, $request->pageNo));
            }

            $filteredProducts = $query->get();

            return response()->json(ResponseHelper::sendResponse(true, 200, "Data Fetched Successfully", $filteredProducts));
        }

        return response()->json(ResponseHelper::sendResponse(false, 404, "No Products Found :("));


    }

}


