<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeTransaction {

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authUrl = env('AUTH_URL');
        $authResponse = Http::get($authUrl);

        if($authResponse['status'] == 'success' && $authResponse['data']['authorization'] == true) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Unauthorized',
        ], 401);
    }
}
