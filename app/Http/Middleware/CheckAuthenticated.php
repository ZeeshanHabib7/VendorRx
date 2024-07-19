<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Check if the token is valid
           JWTAuth::parseToken()->authenticate();
        }   catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }
        
        return $next($request);
    }
}
