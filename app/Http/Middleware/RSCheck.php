<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserInfo;
use App\Models\Language;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RSCheck
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
        if (Auth::check()) {
            // Get the user instance
            $user = Auth::user();

            $master_id = $user->master_id;

            // Check if the user_type is "SA"
            if ($user->user_type === "RS") {
                return $next($request);
            }

            // Check if the user_type is "WC" and redirect accordingly
            if ($user->user_type === "WC") {
                return redirect()->to(route('white-label.dashboard'));
            }
            if ($user->user_type === "TR") {
                $user = Auth::user();
                $lang = Language::where('id', $user->language_id)->first();
                if(!$lang){
                    return redirect()->to(route('transport.dashboard', ["en"]));
                }else{
                    $short = $lang->Short_name;
                    return redirect()->to(route('transport.dashboard', [$short]));
                }
            }
            if ($user->user_type == "SA") {
                return redirect()->to(route('admin.dashboard'));
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
    }
}
