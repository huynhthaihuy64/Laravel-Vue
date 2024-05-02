<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class RoleMiddleware
{
    //Example check role middleware
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure                  $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        try {
            if (!Auth::check()) {
                throw new UnauthorizedException(__('auth.auth.unauthorized'));
            }

            $userRole = Auth::user()->role;

            if ($userRole === null) {
                throw new UnauthorizedException(__('auth.auth.unauthorized'));
            }

            if (in_array($userRole, $roles)) {
                return $next($request);
            }

            throw new UnauthorizedException(__('auth.auth.unauthorized'));
        } catch (Exception $e) {
            logger($e->getMessage(), $e->getTrace());
            throw $e;
        }

        return $next($request);
    }
}
