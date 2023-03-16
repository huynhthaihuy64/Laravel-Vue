<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class ResponseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public static function successResponse($data)
    {
        return [
            'status' => true,
            'error_code' => null,
            'result' => $data,
            'message' => null,
            'error' => [
            ]
        ];
    }

    public function validationError($validation)
    {
        return [
            'status' => false,
            'error_code' => null,
            'result' => null,
            'message' => $validation->errors()->first(),
            'error' => [
               
            ]
        ];
    }

    public static function errorResponse($message)
    {
        if (is_array($message)) $message = self::getErrorMessageFromResponse($message);

        return [
            'status' => false,
            'error_code' => null,
            'result' => null,
            'message' => $message,
            'error' => [
            ]
        ];
    }

    public static function authFailed()
    {
        return [
            'status' => false,
            'error_code' => null,
            'result' => null,
            'message' => "ユーザ名またはパスワードが間違っています!",
            'error' => [
                
            ]
        ];
    }

    public function errorMethodUndefined($method = '')
    {
        return [
            'status' => false,
            'error_code' => null,
            'result' => null,
            'message' => 'Метод '.$method.' не найден',
            'error' => [
            ]
        ];
    }

    /**
     * @param array $response
     * @return string $message of error
     */
    public static function getErrorMessageFromResponse(array $response):string
    {
        # initial set default info message
        $message = "Undefined error: ".json_encode($response);

        # find error message
        if (isset($response['error']))
        {
            if (isset($response['error']['message']))
            {
                if (is_array($response['error']['message'])) $message = array_shift($response['error']['message']);
                else $message = $response['error']['message'];
            }
        }
        return $message;
    }


    public function validate(array $params, array $rules)
    {
        $validate = Validator::make($params,$rules);

        if ($validate->fails())
        {
            return self::validationError($validate);
        }

        return true;
    }
}
