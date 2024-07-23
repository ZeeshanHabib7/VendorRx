<?php
// app/Http/Controllers/BrandController_HR.php
namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Requests\StoreBrandHRRequest;
use App\Http\Requests\UpdateBrandHRRequest;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        return response()->json(Brand::all());
    }

    public function store(StoreBrandRequest $request)
    {
        $brand = Brand::create($request->validated());
        return response()->json($brand, 201);
    }

    public function show(Brand $brand)
    {
        return response()->json($brand);
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        $brand->update($request->validated());
        return response()->json($brand);
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return response()->json(null, 204);
    }
}
