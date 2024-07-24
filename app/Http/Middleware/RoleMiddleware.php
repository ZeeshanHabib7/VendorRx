<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        // dd($roles);
        $rolesArray = explode(',', str_replace(['[', ']', '"'], '', $roles));

        foreach ($rolesArray as $role) {

            if ($request->user()->hasRole(trim($role))) {
                return $next($request);
            }
        }


        return errorResponse("Access denied for unauthorized user", 403);
    }
}
