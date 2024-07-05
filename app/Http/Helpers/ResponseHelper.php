<?php

namespace App\Http\Helpers;

class ResponseHelper {

    public static function success($data=[], $message = 'success!', $statusCode=200, $isPaginate=false )
    {
       if($isPaginate) {
           $data = SELF::Paginate($data);
       }

        return response()->json([
            'success' => true,
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
        ],$statusCode);
    }

    public static function error($data=[], $message = 'Failed!', $statusCode = 400)
    {
        return response()->json([
            'success' => false,
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public static function paginate($data = []) {
        $paginationArray = null;

        if ($data != null) {
            // Initialize pagination array with list of items
            $paginationArray = [
                'list' => $data->items(),
                'pagination' => []
            ];

            // Add pagination details
            $paginationArray['pagination'] = [
                'total' => $data->total(),
                'current' => $data->currentPage(),
                'first' => 1,
                'last' => $data->lastPage(),
                'previous' => $data->currentPage() > 1 ? $data->currentPage() - 1 : 0,
                'next' => $data->hasMorePages() ? $data->currentPage() + 1 : $data->lastPage(),
                'pages' => $data->lastPage() > 1 ? range(1, $data->lastPage()) : [1],
                'from' => $data->firstItem(),
                'to' => $data->lastItem()
            ];

            return  $paginationArray;
        }

        return $paginationArray;
    }

}

?>
