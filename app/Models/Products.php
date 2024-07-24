<?php

namespace App\Models;

use App\Http\Requests\ProductRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "products";
    protected $primaryKey = "id";
    protected $fillable = [
        'name',
        'price',
        'date',
        'brand_id',
        'category_id',
        'barcode_symbology',
        'is_batch',
        'cost',
        'qty',
        'alert_quantity',
        'promotion',
        'promotion_price',
        'starting_date',
        'last_date',
        'image',
        'featured',
        'product_details',
        'is_active',
        'discount',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
        'deleted_at' => 'datetime:Y-m-d H:m:s',
    ];

    static public function getFilteredProducts(ProductRequest $request)
    {
        $query = Products::query();

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
