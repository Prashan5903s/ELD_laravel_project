<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {

        $request->authenticate();

        $request->session()->regenerate();

        // Log the IP address of the device from which the user is trying to login
        $ipAddress = $request->ip();

        // Update the user's IP address in the database
        $user = $request->user();
        $user->ip = $ipAddress;
        $user->save();

        $val_user = $request->user();
        if ($val_user->user_type == "EC") {
            $tr_user = User::where('user_type', 'TR')->where('master_id', $val_user->id)->get();
            if(count($tr_user)>=2){
                $request->session()->put('comp_user_change', true);
            }
        }

        // Get the authenticated user instance
        $ids = $request->user()->id;
        $first = $request->user()->first_name;
        $last = $request->user()->last_name;
        $email = $request->user()->email;
        $avatar = $request->user()->avatar_image;
        $master_company_id = $request->user()->master_company_id;
        $master_id = $request->user()->master_id;
        $is_master = $request->user()->is_master;

        // Store the user instance data in the session
        $request->session()->put('ids', $ids);
        $request->session()->put('first', $first);
        $request->session()->put('last', $last);
        $request->session()->put('email', $email);
        $request->session()->put('avatar', $avatar);
        $request->session()->put('master_company_id', $master_company_id);
        $request->session()->put('master_id', $master_id);
        $request->session()->put('is_master', $is_master);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {


        $user = $request->user();
        $user->logout_at = Carbon::now();
        $user->save();

        Auth::guard('web')->logout();

        // Remove or forget master_company_id, master_id, and is_master from the session
        $request->session()->forget(['master_company_id', 'master_id', 'is_master', 'first', 'last', 'email', 'avatar', 'ids', 'ut', 'comp_user_change', 'chUsers', 'after_change']);
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

}
