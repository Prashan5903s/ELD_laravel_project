<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PackageAssign;
use App\Models\Package;
use App\Models\PackageModule;
use Carbon\Carbon;

class CheckPermission
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
        // If the user is not authenticated, just continue the request
        if (!Auth::check()) {
            return $next($request);
        }

        // Get the authenticated user
        $user = Auth::user();
        $currTime = Carbon::now();

        // If the user is of type "TR"
        if ($user->user_type === "TR") {
            // Retrieve the most recent active package assignment for the user
            $packAssgn = PackageAssign::where('user_id', $user->id)
                ->where('end_date', '>', $currTime)
                ->latest()
                ->first();

            if (!$packAssgn) {
                return $next($request);
            }

            // Retrieve the package associated with the package assignment
            $package = $packAssgn->package;

            if (!$package) {
                return redirect()->route('admin.dashboard')->with('error', 'Package not found.');
            }

            // Retrieve the permissions for the package
            $permissions = $package->modules()->pluck('permission_id');

            if ($permissions->isEmpty()) {
                return redirect()->route('logout');
            }

            // Share the permissions with all views
            view()->share('permissions', $permissions);
        }

        return $next($request);
    }
}
