<?php

namespace App\Http\Helpers;

use Illuminate\Database\Eloquent\Model;
class ResponseHelper {

    public static function success($data=[], $message = 'success!', $statusCode=200 )
    {
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

}

?>
