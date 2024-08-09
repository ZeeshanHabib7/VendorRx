<?php

// app/Helpers/DateHelper.php

namespace App\Helpers;

use App\Http\Resources\ProductResource;

class ResponseHelper
{

    public static function sendResponse($sucess, $code, $msg, $data = [], $paginate = false, $pageSize = 5, $pageNo = 1)
    {

        $response = [
            'success' => $sucess,
            'status_code' => $code,
            'message' => [$msg],
            'data' => $data,


        ];

        if ($paginate) {
            $data = self::paginate($data, $pageNo, $pageSize);
            $response['data'] = $data;
        }

        return $response;
    }

    public static function paginate($data = [], $currentPage = 1, $perPage = 5)
    {
        $paginationArray = null;

        if ($data != null) {
            // Initialize pagination array with list of items
            //dd($data);
            $paginationArray = [
                'list' => $data,
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
