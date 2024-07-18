<?php

namespace App\Models;

use App\Http\Requests\ProductRequest_SA;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products_SA extends Model
{
    use HasFactory;
    protected $table = "products";
    protected $primaryKey = "id";
    protected $fillable = ['name', 'price', 'date', 'brand'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
        'deleted_at' => 'datetime:Y-m-d H:m:s',
    ];

    static public function getFilteredProducts(ProductRequest_SA $request)
    {
        $query = Products_SA::query();

        // Apply filters
        if ($request->has('name')) {
            $query->where('name', 'LIKE', '%' . $request->input('name') . '%');
        }

        if ($request->has('startDate') && $request->has('endDate')) {

            $query->whereBetween('created_at', [$request->input('startDate'), $request->input('endDate')]);

        } else {

            if ($request->has('startDate')) {

                $query->where('created_at', '>=', $request->input('startDate'));

            } elseif ($request->has('endDate')) {

                $query->where('created_at', '<=', $request->input('endDate'));
            }
        }

        if ($request->has('minPrice')) {
            $query->where('price', '>=', $request->input('minPrice'));
        }

        if ($request->has('maxPrice')) {
            $query->where('price', '<=', $request->input('maxPrice'));
        }

        if ($request->has('brand')) {
            $query->where('brand', $request->input('brand'));
        }

        if ($request->has('keyWord')) {
            $query->where('brand', 'LIKE', '%' . $request->keyWord . '%')->orWhere('name', 'LIKE', '%' . $request->keyWord . '%');
        }

        return $query;
    }
}
