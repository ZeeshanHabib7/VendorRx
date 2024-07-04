<?php

namespace App\Helpers;

use App\Http\Resources\ProductResource_SA;

class PaginationHelper
{
    public static function paginateMyData($data = [], $currentPage = 1, $perPage = 5)
    {
        $paginationArray = null;

        if ($data != null) {
            // Initialize pagination array with list of items
            $paginationArray = [
                'list' => ProductResource_SA::collection($data->items()),
                'pagination' => []
            ];

            // Add pagination details
            $paginationArray['pagination'] = [
                'total' => $data->total(),
                'current' => $currentPage,
                'first' => 1,
                'last' => $data->lastPage(),
                'previous' => $data->currentPage() > 1 ? $data->currentPage() - 1 : 0,
                'next' => $data->hasMorePages() ? $data->currentPage() + 1 : $data->lastPage(),
                'pages' => $data->lastPage() > 1 ? range(1, $data->lastPage()) : [1],
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ];

            return $paginationArray;
        }

        return $paginationArray;
    }
}