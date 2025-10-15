<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isMethod('OPTIONS')) {
            if (null == $request->headers->has('X-api-key') || $request->header('x-api-key') !== env('API_KEY')) {
                return response()->json(['status' => 'error', 'message' => 'Forbidden.'], 403);
            }
        }
        return $next($request);
    }
}
