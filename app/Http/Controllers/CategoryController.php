<?php
// app/Http/Controllers/CategoryController_HR.php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryHRRequest;
use App\Http\Requests\UpdateCategoryHRRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::all());
    }

    public function store(StoreCategoryHRRequest $request)
    {
        $category = Category::create($request->validated());
        return response()->json($category, 201);
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }

    public function update(UpdateCategoryHRRequest $request, Category $category)
    {
        $category->update($request->validated());
        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(null, 204);
    }
}
