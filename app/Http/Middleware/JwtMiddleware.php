<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Illuminate\Support\Facades\Config;

class JwtMiddleware extends BaseMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return  response()->json([
                    'status' => false,
                    'error_code' => Config::get('errors.ERR_TOKEN.INVALID.CODE'),
                    'result' => null,
                    'message' => Config::get('errors.ERR_TOKEN.INVALID.MESSAGE'),
                    'error' => [
                    ]
                ]);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return  response()->json([
                    'status' => false,
                    'error_code' => Config::get('errors.ERR_TOKEN.EXPIRED.CODE'),
                    'result' => null,
                    'message' => Config::get('errors.ERR_TOKEN.EXPIRED.MESSAGE'),
                    'error' => [
                    ]
                ]);
            }else{
                return  response()->json([
                    'status' => false,
                    'error_code' => Config::get('errors.ERR_TOKEN.NOT_FOUND.CODE'),
                    'result' => null,
                    'message' => Config::get('errors.ERR_TOKEN.NOT_FOUND.MESSAGE'),
                    'error' => [
                    ]
                ]);
            }
        }
        return $next($request);
    }
}