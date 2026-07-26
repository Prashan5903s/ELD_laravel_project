<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class DrCheckAPI
{
    public function handle(Request $request, Closure $next)
    {
        // Allow CORS preflight to pass immediately
        if ($request->getMethod() === 'OPTIONS') {
            return response()->json('OK', 200);
        }

        // FORCE API GUARD (IMPORTANT FIX)
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'error' => 'User not authenticated.'
            ], 401);
        }

        // Safely fetch master user
        $master = User::find($user->master_id);

        if (!$master) {
            return response()->json([
                'error' => 'Master not found.'
            ], 403);
        }

        //  Business rule check
        if ($user->user_type === 'U' && $master->user_type === 'TR') {
            return $next($request);
        }

        //  Log unauthorized access attempt
        Log::warning('DrCheckAPI access denied', [
            'user_id' => $user->id,
            'master_id' => $user->master_id,
            'user_type' => $user->user_type ?? null,
            'master_type' => $master->user_type ?? null,
        ]);

        return response()->json([
            'error' => 'Access denied.'
        ], 403);
    }
}