<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileAPIMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        
        $check = Auth::guard('mobileAPI')->check();

        if (!$check) {

            return response()->json([
                "status" => "failure",
                "statusCode" => 401,
                "message" => "User not loggedIn"
            ]);
        }
        
        // Authenticate user via 'mobileAPI' guard
        $user = Auth::guard('mobileAPI')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Perform additional checks if needed, for example, user type
        if ($user->user_type !== 'mobile') {
            return response()->json(['error' => 'Access denied'], 403);
        }

        return $next($request);
    }
}
