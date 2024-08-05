<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use Exception;

class BrandController extends Controller
{
    public function index()
    {
        try {
            $brands = Brand::latest()->get();
            return successResponse('Brands retrieved successfully.', BrandResource::collection($brands));
        } catch (Exception $e) {
            return errorResponse('Failed to retrieve brands.', 500);
        }
    }

    public function store(StoreBrandRequest $request)
    {
        try {
            $brand = Brand::create($request->validated());
            return successResponse('Brand created successfully.', new BrandResource($brand), false, 201);
        } catch (Exception $e) {
            return errorResponse('Failed to create brand.', 500);
        }
    }

    public function show($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            return successResponse('Brand retrieved successfully.', new BrandResource($brand));
        } catch (Exception $e) {
            return errorResponse('Brand not found.', 404);
        }
    }

    public function update(UpdateBrandRequest $request, $id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->update($request->validated());
            return successResponse('Brand updated successfully.', new BrandResource($brand));
        } catch (Exception $e) {
            return errorResponse('Failed to update brand.', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->delete();
            return successResponse('Brand deleted successfully.', null, false, 204);
        } catch (Exception $e) {
            return errorResponse('Failed to delete brand.', 500);
        }
    }
}
