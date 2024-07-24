<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Products;
use App\Helpers\ResponseHelper;
use App\Http\Requests\ProductRequest;



class ProductsController_SA extends Controller
{

    public function getData(ProductRequest $request)
    {

        if (!$request->query()) {

            $data = Products::all();

        } else {

            $query = Products::getFilteredProducts($request);

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
        return response()->json(ResponseHelper::sendResponse(true, 200, "Data Fetched Successfully", ProductResource::collection($data), $request->paginate, $request->pageSize, $request->pageNo));

    }

    public function store(ProductRequest $request)
    {
        try {
            $products = Products::create($request->all());
            return successResponse("Product created successfully", ProductResource::make($products));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }

    }

    public function show($productId)
    {
        try {
            $product = $this->findProductById($productId);
            return successResponse("Product fetched successfully", ProductResource::make($product));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }
    }

    public function update(ProductRequest $request, $productId)
    {

        try {
            $product = $this->findProductById($productId);
            $product->update([
                'name' => $request->name,
                'price' => $request->price,
                'brand' => $request->brand,
            ]);

            return successResponse("Product updated successfully", ProductResource::make($product));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }

    }

    public function destroy($productId)
    {
        try {
            $product = $this->findProductById($productId);
            $product->delete();

            return successResponse("Product deleted successfully", ProductResource::make($product));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }

    }

    public function restore($productId)
    {
        try {
            $product = Products::withTrashed()->find($productId);

            if ($product && $product->trashed()) {
                $product->restore();
                return successResponse("Product restored successfully", ProductResource::make($product));
            }

        } catch (\Exception $e) {
            return errorResponse($e->getMessage(), 404);
        }

    }

    public function permanentDelete($productId)
    {
        try {

            $product = Products::withTrashed()->find($productId);

            if ($product) {
                $product->forceDelete();
                return successResponse("Product permanently deleted successfully", ProductResource::make($product));
            }

        } catch (\Exception $e) {
            return errorResponse($e->getMessage(), 404);
        }

    }

    protected function findProductById($productId)
    {
        return Products::find($productId);
    }

}


