<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TRChange
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
        if ($request->session()->has('comp_user_change')) {
            return redirect()->route('company.user.change'); // corrected line
        } else {
            return $next($request);
        }
    }

}
