<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TRChecKAPI
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
        $user = Auth::user();

        if($user->user_type == "TR"){

            return $next($request);

        } elseif ($user->user_type == "EC"){

            return response()->json(['error' => 'Wrong user type.'], 403);

        } else {

            return response()->json(['error' => 'Invalid user type.'], 401);

        }
    }
}
