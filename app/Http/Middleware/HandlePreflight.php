<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandlePreflight
{
    public function handle(Request $request, Closure $next)
    {
        
        // if ($request->getMethod() === 'OPTIONS') {
        //     return response('', 200)
        //         ->header('Access-Control-Allow-Origin', 'https://uat-eld.vercel.app')
        //         ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        //         ->header('Access-Control-Allow-Headers', 'Authorization, Content-Type, X-Requested-With')
        //         ->header('Access-Control-Allow-Credentials', 'true');
        // }

        return $next($request);
    }
}