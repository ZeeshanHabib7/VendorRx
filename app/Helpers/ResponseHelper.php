<?php

// app/Helpers/DateHelper.php

namespace App\Helpers;


class ResponseHelper
{
    public static function sendResponse($sucess, $code, $msg, $products = [])
    {
        $response = [
            'success' => $sucess,
            'status_code' => $code,
            'message' => [$msg],
            'data' => $products,


        ];

        return $response;
    }
}
