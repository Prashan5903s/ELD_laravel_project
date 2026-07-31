<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MobileAPIMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user('mobileAPI');

        if (!$user) {
            return response()->json([
                'success' => false,
                'statusCode' => 401,
                'message' => 'Unauthenticated. Please provide a valid access token.',
            ], 401);
        }

        if ($user->user_type !== 'u') {
            return response()->json([
                'success' => false,
                'statusCode' => 403,
                'message' => 'Access denied.',
            ], 403);
        }

        return $next($request);
    }
}