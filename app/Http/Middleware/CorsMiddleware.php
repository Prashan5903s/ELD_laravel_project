<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $origin = $request->header('Origin');

        $allowedOrigins = [
            'http://localhost:3000',
            'http://127.0.0.1:3000',
        ];

        if ($request->isMethod('OPTIONS')) {
            $response = response('', 200);
        } else {
            $response = $next($request);
        }

        if (in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }

        $response->headers->set(
            'Access-Control-Allow-Methods',
            'GET, POST, PUT, PATCH, DELETE, OPTIONS'
        );

        $response->headers->set(
            'Access-Control-Allow-Headers',
            'Origin, Content-Type, Authorization, X-Requested-With, Accept'
        );

        $response->headers->set(
            'Access-Control-Allow-Credentials',
            'true'
        );

        return $response;
    }
}