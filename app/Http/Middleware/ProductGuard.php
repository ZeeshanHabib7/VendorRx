<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Attempt to authenticate the user
            if (JWTAuth::parseToken()->authenticate()) {
                return $next($request);
            }

        } catch (JWTException $e) {
            // Handle token exceptions

            return errorResponse("Unauthorized User." . $e->getMessage(), 401);
        }


    }
}
