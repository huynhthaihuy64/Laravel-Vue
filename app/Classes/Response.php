<?php

namespace App\Classes;

use App\Enum\Constants;
use App\Enum\HTTPStatus;

class Response
{
    /**
     * @param  array   $data
     * @param  int     $code
     * @param  string  $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private static function makeResponseData($data, $message, $code)
    {
        $status = $code < 300 ? Constants::Success->name : Constants::Error->name;
        return response()->json([
            'status'  => $status,
            'data'    => $data,
            'message' => $message,
        ], (int)$code);
    }

    public static function error($message, $code)
    {
        return self::makeResponseData(null, $message, $code);
    }

    public static function success($data, $message)
    {
        return self::makeResponseData($data, $message, HTTPStatus::OK->value);
    }

    public static function unauthorized()
    {
        return self::makeResponseData(null, __('auth.auth.unauthorized'), HTTPStatus::ACCESS_DENIED->value);
    }

    public static function unauthenticated()
    {
        return self::makeResponseData(null, __('auth.auth.unauthenticated'), HTTPStatus::ACCESS_DENIED->value);
    }

    public static function customMessages($messages, $code = 422)
    {
        $result = [];
        if (count($messages) > 0) {
            foreach ($messages as $key => $message) {
                $result[$key][] = $message[0];
            };
        }
        return response()->json([
            'status'  => $code,
            'data'    => null,
            'message' => $result,
        ], (int)$code);
    }
}
