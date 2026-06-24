<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DrCheckMobileAPI
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
        // Ensure the request is authenticated using the 'api' guard
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        // Ensure the user has a valid master_id
        if (!$user->master_id) {
            return response()->json(['error' => 'User does not have a master ID.'], 401);
        }

        // Retrieve the master user
        $master = User::find($user->master_id);

        if (!$master) {
            return response()->json(['error' => 'Master user not found.'], 401);
        }

        // Ensure the master has a valid user type
        if (!$master->user_type) {
            return response()->json(['error' => 'Master user type is missing.'], 401);
        }

        // Check user type and access permissions
        if ($user->user_type === 'U' && $master->user_type === 'TR') {
            return $next($request); // Allow access for 'U' users with 'TR' master
        }

        if (in_array($user->user_type, ['TR', 'EC'])) {
            return response()->json(['error' => 'Transporter or External Client cannot access this API.'], 403);
        }

        // Default case for invalid or unexpected user type
        return response()->json(['error' => 'Invalid or unsupported user type: ' . $user->user_type], 401);
    }
}
