<?php
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
            $categories = Category::all();
            return ResponseHelper::success($categories, 'Categories retrieved successfully.');
        } catch (Exception $e) {
            return ResponseHelper::error([], 'Failed to retrieve categories.', 500);
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = Category::create($request->validated());
            return ResponseHelper::success($category, 'Category created successfully.', 201);
        } catch (Exception $e) {
            return ResponseHelper::error([], 'Failed to create category.', 500);
        }
    }

    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return ResponseHelper::success($category, 'Category retrieved successfully.');
        } catch (Exception $e) {
            return ResponseHelper::error([], 'Category not found.', 404);
        }
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->update($request->validated());
            return ResponseHelper::success($category, 'Category updated successfully.');
        } catch (Exception $e) {
            return ResponseHelper::error([], 'Failed to update category.', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            return ResponseHelper::success([], 'Category deleted successfully.', 204);
        } catch (Exception $e) {
            return ResponseHelper::error([], 'Failed to delete category.', 500);
        }
    }
}
