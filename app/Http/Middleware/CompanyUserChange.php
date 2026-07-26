<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CompanyUserChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next)
    {

        if ($request->session()->has('comp_user_change') || $request->session()->has('chUsers') || $request->session()->has('after_change')) {
            return $next($request);
        } else {
            return redirect()->to(route('company.dashboard'));
        }
    }
}
