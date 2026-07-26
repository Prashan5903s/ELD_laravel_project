<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PackageAssign;
use App\Models\Package;
use App\Models\PackageModule;
use Carbon\Carbon;

class CheckCustomPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permissionId)
    {

        if (Auth::check()) {


            // Get the authenticated user
            $user = Auth::user();

            $currTime = Carbon::now();

            if ($user) {

                $packAssgn = PackageAssign::where('user_id', $user->id)
                    ->where('end_date', '>', $currTime)
                    ->latest()
                    ->first();

                if ($packAssgn) {

                    $package = Package::where('id', $packAssgn->package_id)->first();

                    if ($package) {

                        $packMod = PackageModule::where('package_id', $package->id)->where('permission_id', $permissionId)->get();

                        if (count($packMod) > 0) {

                            return $next($request);
                        } else {

                            return redirect()->route('admin.dashboard')->with('error', 'Unauthorised');
                        }
                    }
                } else {
                    return redirect()->route('transport.dashboard');
                }
            }
        }
    }
}
