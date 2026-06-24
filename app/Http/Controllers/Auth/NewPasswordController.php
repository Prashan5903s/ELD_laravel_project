<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): RedirectResponse|View
    {

        $valToken = session('tokens');
        $reqToken = $request->token;

        if ($reqToken != $valToken){
            session()->flash('error', 'You are using a wrong reset link, create a new reset link!');
            return redirect()->route('password.request');
        }

        if (!session()->has('fiveMin') || Carbon::createFromTimestamp(session('fiveMin'))->diffInMinutes(now()) >= 5) {
            session()->flash('error', 'Reset link has expired, please generate a new link!');
            return redirect()->route('password.request');
        }

        return view('auth.reset-password', ['request' => $request]);
        
    }


    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {

        if (!session()->has('fiveMin') && Carbon::createFromTimestamp(session('fiveMin'))->diffInMinutes(now()) >= 5) {
            $request->session()->flash('error', 'Reset link has expired, please generate a new link!');
            return redirect()->route('password.request');
        }

        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        $request->session()->flash('status', ($status)); // Assuming 'en' is the language code

        Session::forget('emailConf');
        Session::forget('fiveMin');
        Session::forget('tokens');

        return $status == Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);

    }
    
}
