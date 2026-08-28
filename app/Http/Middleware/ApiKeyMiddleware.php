<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = config('app.api_key', env('API_KEY', 'silapcat-secret-key-2026'));

        $apiKey = $request->header('X-API-KEY');

        if (! $apiKey && $request->hasHeader('Authorization')) {
            $authHeader = $request->header('Authorization');
            if (str_starts_with($authHeader, 'Bearer ')) {
                $apiKey = substr($authHeader, 7);
            }
        }

        if (! $apiKey) {
            $apiKey = $request->query('api_key');
        }

        if (! $apiKey || ! hash_equals((string) $expectedKey, (string) $apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: API Key tidak valid atau tidak ditemukan.',
            ], 401);
        }

        return $next($request);
    }
}
