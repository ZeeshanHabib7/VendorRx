<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,$permission): Response
    
    {

        if ($request->user()->can($permission)) {
            return $next($request);
        }

        return errorResponse("Access denied for unauthorized user. User don't have the permission for ".$permission, 403);
    }
}
