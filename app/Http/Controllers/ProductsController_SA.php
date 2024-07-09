<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products_SA;
use App\Helpers\ResponseHelper;
use App\Http\Requests\ProductRequest_SA;



class ProductsController_SA extends Controller
{

    public function getData(ProductRequest_SA $request)
    {

        if (!$request->query()) {

            $data = Products_SA::all();

        } else {

            $query = Products_SA::getFilteredProducts($request);

            //if data is retrieved from query and pagination also true
            if ($query->exists() && $request->paginate) {

                $data = $query->paginate($request->pageSize, ['*'], 'page', $request->pageNo);

                // if only data is retrieved and no pagination
            } else if ($query->exists()) {

                $data = $query->get();

            } else {
                // sending error response means we didn't find data for given filters 
                return response()->json(ResponseHelper::sendResponse(false, 404, "No Products Found :("));
            }


        }

        // sending sucess response
        return response()->json(ResponseHelper::sendResponse(true, 200, "Data Fetched Successfully", $data, $request->paginate, $request->pageSize, $request->pageNo));



    }

}


