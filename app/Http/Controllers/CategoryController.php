<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Exception;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::whereNull('parent_id')->with('subCategories')->latest()->get();
            return successResponse('Categories retrieved successfully.', CategoryResource::collection($categories));
        } catch (Exception $e) {
            return errorResponse('Failed to retrieve categories.', 500);
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = Category::create($request->validated());
            return successResponse('Category created successfully.', new CategoryResource($category), false, 201);
        } catch (Exception $e) {
            return errorResponse('Failed to create category.', 500);
        }
    }

    public function show($id)
    {
        try {
            $category = Category::with('subCategories')->findOrFail($id);
            return successResponse('Category retrieved successfully.', new CategoryResource($category));
        } catch (Exception $e) {
            return errorResponse('Category not found.', 404);
        }
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->update($request->validated());
            return successResponse('Category updated successfully.', new CategoryResource($category));
        } catch (Exception $e) {
            return errorResponse('Failed to update category.', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            return successResponse('Category deleted successfully.', [], false, 204);
        } catch (Exception $e) {
            return errorResponse('Failed to delete category.', 500);
        }
    }
}
