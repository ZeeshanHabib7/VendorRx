<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class CheckEncrypt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->header('isEncrypted') == "true") {

            try {
                $decryptedPayload = Crypt::decrypt($request->input('encryptedData'));
                $decodedPayload = json_decode($decryptedPayload, true);
                $request->merge($decodedPayload);
                return $next($request);

            } catch (DecryptException $e) {
                return errorResponse($e->getMessage());
            }
        }


        return $next($request);
    }
}
