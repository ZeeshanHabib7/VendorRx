<?php
// app/Http/Controllers/BrandController_HR.php
// app/Http/Controllers/BrandController_HR.php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\BrandResource;
use Illuminate\Http\Request;
use Exception;

class BrandController extends Controller
{
    public function index()
    {
        try {
            $brands = Brand::latest()->get();
            return ResponseHelper::successResponse(BrandResource::collection($brands), 'Brands retrieved successfully.');
        } catch (Exception $e) {
            return ResponseHelper::errorResponse([], 'Failed to retrieve brands.', 500);
        }
    }

    public function store(StoreBrandRequest $request)
    {
        try {
            $brand = Brand::create($request->validated());
            return ResponseHelper::successResponse(new BrandResource($brand), 'Brand created successfully.', 201);
        } catch (Exception $e) {
            return ResponseHelper::errorResponse([], 'Failed to create brand.', 500);
        }
    }

    public function show($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            return ResponseHelper::successResponse(new BrandResource($brand), 'Brand retrieved successfully.');
        } catch (Exception $e) {
            return ResponseHelper::errorResponse([], 'Brand not found.', 404);
        }
    }

    public function update(UpdateBrandRequest $request, $id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->update($request->validated());
            return ResponseHelper::successResponse(new BrandResource($brand), 'Brand updated successfully.');
        } catch (Exception $e) {
            return ResponseHelper::errorResponse([], 'Failed to update brand.', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->delete();
            return ResponseHelper::successResponse([], 'Brand deleted successfully.', 204);
        } catch (Exception $e) {
            return ResponseHelper::errorResponse([], 'Failed to delete brand.', 500);
        }
    }
}
