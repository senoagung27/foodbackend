<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
    // public function sendResponse($result, $message)
    // {
    // 	$response = [
    //         'code' => 200,
    //         'success' => true,
    //         'data'    => $result,
    //         'message' => $message,
    //     ];


    //     return response()->json($response, 200);

    // }
    protected static $response = [
        'meta' => [
            'code' => 200,
            'status' => 'success',
            'message' => null,
        ],
        'data' => null,
    ];

    public static function success($data = null, $message = null)
    {
        self::$response['meta']['message'] = $message;
        self::$response['data'] = $data;

        return response()->json(self::$response, self::$response['meta']['code']);
    }
    public static function error($data = null, $message = null, $code = 400)
    {
        self::$response['meta']['status'] = 'error';
        self::$response['meta']['code'] = $code;
        self::$response['meta']['message'] = $message;
        self::$response['data'] = $data;

        return response()->json(self::$response, self::$response['meta']['code']);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */

    // public function sendError($error, $errorMessages = [], $code = 404)
    // {
    // 	$response = [
    //         'success' => false,
    //         'message' => $error,
    //     ];


    //     if(!empty($errorMessages)){
    //         $response['data'] = $errorMessages;
    //     }


    //     return response()->json($response, $code);
    // }
}
