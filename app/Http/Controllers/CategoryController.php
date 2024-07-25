<?php
// app/Http/Controllers/CategoryController_HR.php
// app/Http/Controllers/CategoryController_HR.php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Helpers\ResponseHelper;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Exception;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::latest()->get();
            return ResponseHelper::successResponse(CategoryResource::collection($categories), 'Categories retrieved successfully.');
        } catch (Exception $e) {
            return ResponseHelper::errorResponse([], 'Failed to retrieve categories.', 500);
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = Category::create($request->validated());
            return ResponseHelper::successResponse(new CategoryResource($category), 'Category created successfully.', 201);
        } catch (Exception $e) {
            return ResponseHelper::errorResponse([], 'Failed to create category.', 500);
        }
    }

    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return ResponseHelper::successResponse(new CategoryResource($category), 'Category retrieved successfully.');
        } catch (Exception $e) {
            return ResponseHelper::errorResponse([], 'Category not found.', 404);
        }
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->update($request->validated());
            return ResponseHelper::successResponse(new CategoryResource($category), 'Category updated successfully.');
        } catch (Exception $e) {
            return ResponseHelper::errorResponse([], 'Failed to update category.', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            return ResponseHelper::successResponse([], 'Category deleted successfully.', 204);
        } catch (Exception $e) {
            return ResponseHelper::errorResponse([], 'Failed to delete category.', 500);
        }
    }
}
