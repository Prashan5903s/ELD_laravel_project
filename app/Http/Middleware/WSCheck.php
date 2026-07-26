<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserInfo;
use App\Models\Language;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WSCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if the user is logged in
        if (Auth::check()) {
            // Get the user instance
            $user = Auth::user();

            $master_id = $user->master_id;

            // Check if the user_type is "WC"
            if ($user->user_type === "WC") {
                return $next($request);
            }

            // Check if the user_type is "SA" and redirect accordingly
            if ($user->user_type === "SA") {
                return redirect()->to(route('admin.dashboard'));
            }
            if ($user->user_type === "TR") {
                $user = Auth::user();
                $lang = Language::where('id', $user->language_id)->first();
                if (!$lang) {
                    return redirect()->to(route('transport.dashboard', ["en"]));
                } else {
                    $short = $lang->Short_name;
                    return redirect()->to(route('transport.dashboard', [$short]));
                }
            }
            if ($user->user_type == "RS") {
                return redirect()->to(route('reseller.dashboard'));
            }

            if ($user->user_type == "EC") {
                return redirect()->to(route('company.dashboard'));
            }
            if ($user->user_type = "U") {
                $tr = User::find($master_id);

                if ($tr->user_type == 'TR') {
                    return redirect()->route('driver.dashboard');
                } else {
                    return redirect()->route('logout');
                }

            }

        }

        // If user is not logged in or user_type doesn't match, you can handle it as per your application logic.
        // For example, you might want to redirect to login or show an error page.
        // Here, I'm just returning a response indicating unauthorized access.
        // return response('Unauthorized', 401);
    }
}
