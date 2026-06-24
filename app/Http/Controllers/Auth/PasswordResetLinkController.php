<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

     public function store(Request $request): RedirectResponse
     {
         $request->validate([
             'email' => ['required', 'email'],
         ]);

         $status = Password::sendResetLink(
             $request->only('email')
         );
         $email = $request->only('email')['email'];

         // Store the current time when setting the 'fiveMin' session
         session(['fiveMin' => now()->timestamp]);

         session(['emailConf' => $email]);
         if ($status === Password::RESET_LINK_SENT) {
             // If the reset link is sent successfully, notify the user
             $request->session()->flash('status', ($status)); // Assuming 'en' is the language code
             return back()->with('status', __($status));
         }

         $request->session()->flash('email', __($status)); // Assuming you have this translation key defined
         return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
     }


}
