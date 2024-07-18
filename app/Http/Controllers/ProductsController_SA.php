<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource_SA;
use App\Models\Product;
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

    public function store(ProductRequest_SA $request)
    {
        try {
            $products = Products_SA::create($request->all());
            return successResponse("Product created successfully", ProductResource_SA::make($products));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }

    }

    public function show($productId)
    {
        try {
            $product = $this->findProductById($productId);
            return successResponse("Product fetched successfully", ProductResource_SA::make($product));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }
    }

    public function update(ProductRequest_SA $request, $productId)
    {

        try {
            $product = $this->findProductById($productId);
            $product->update([
                'name' => $request->name,
                'price'=> $request->price,
                'brand'=> $request->brand,
            ]);

            return successResponse("Product updated successfully", ProductResource_SA::make($product));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }

    }

    public function destroy($productId)
    {
        try {
            $product = $this->findProductById($productId);
            $product->delete();

            return successResponse("Product deleted successfully", ProductResource_SA::make($product));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }

    }

    public function restore($productId)
    {
        try {
            $product = Product::withTrashed()->find($productId);

            if ($product && $product->trashed()) {
                $product->restore();
                return successResponse("Product restored successfully", ProductResource_SA::make($product));
            }

        } catch (\Exception $e) {
            return errorResponse($e->getMessage(),404);
        }
       
    }

    public function permanentDelete($productId)
    {
        try {

            $product = Product::withTrashed()->find($productId);

            if ($product) {
                $product->forceDelete();
                return successResponse("Product permanently deleted successfully", ProductResource_SA::make($product));
            }

        } catch (\Exception $e) {
            return errorResponse($e->getMessage(), 404);
        }

    }

    protected function findProductById($productId)
    {
        return Product::find($productId);
    }

}


