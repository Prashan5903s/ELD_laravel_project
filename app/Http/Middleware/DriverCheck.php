<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DriverCheck
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

        $master_id = $user->master_id;

        if ($user->user_type === "U") {

            $tr = User::find($master_id);

            if ($tr->user_type == 'TR') {
                return $next($request);
            } else {
                return redirect()->route('logout');
            }
        }

        // Check if the user_type is "WC" and redirect accordingly
        if ($user->user_type === "WC") {
            return redirect()->to(route('white-label.dashboard'));
        }
        if ($user->user_type === "SA") {
            return redirect()->to(route('admin.dashboard'));
        }
        if ($user->user_type == "RS") {
            return redirect()->to(route('reseller.dashboard'));
        }

        if ($user->user_type == "EC") {
            return redirect()->to(route('company.dashboard'));
        }
        if ($user->user_type = "TR") {
            return redirect()->to(route('transport.dashboard'));
        }

    }
}
