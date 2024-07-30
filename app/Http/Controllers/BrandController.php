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
            return successResponse("Retrieved Successfully", BrandResource::collection($brands));
        } catch (Exception $e) {
            return errorResponse('Failed to retrieve brands.', 500);
        }
    }

    public function store(StoreBrandRequest $request)
    {
        try {
            $brand = Brand::create($request->validated());
            return successResponse("Stored Successfully", new BrandResource($brand), 201);
        } catch (Exception $e) {
            return errorResponse('Failed to create brand.', 500);
        }
    }

    public function show($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            return successResponse("Fetched Successfully", new BrandResource($brand));
        } catch (Exception $e) {
            return errorResponse('Brand not found.', 404);
        }
    }

    public function update(UpdateBrandRequest $request, $id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->update($request->validated());
            return successResponse("Updated Successfully", new BrandResource($brand));
        } catch (Exception $e) {
            return errorResponse('Failed to update brand.', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->delete();
            return successResponse('Brand deleted successfully.', [], false, 204);
        } catch (Exception $e) {
            return errorResponse('Failed to delete brand.', 500);
        }
    }
}
