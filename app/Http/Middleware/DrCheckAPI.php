<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DrCheckAPI
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        $master = User::find($user->master_id);

        if (!$master) {
            return response()->json(['error' => 'Master not found.'], 403);
        }

        // Only allow user types 'U' and master type 'TR'
        if ($user->user_type === 'U' && $master->user_type === 'TR') {
            return $next($request);
        }

        return response()->json(['error' => 'Access denied.'], 403);
    }
}
