<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RSController extends Controller
{
    public function index(){
        return view('reseller.dashboard');
    }

    public function changeUser(Request $request, $ut, $id, User $user)
    {
        $user = User::findOrFail($id);
        $ut = $user->user_type;

        $request->session()->put('original_user_id', auth()->id());
        $request->session()->put('ids', $user->id);
        $request->session()->put('ut', 1);
        $request->session()->put('first', $user->first_name);
        $request->session()->put('last', $user->last_name);
        $request->session()->put('email', $user->email);
        $request->session()->put('avatar', $user->avatar_image);
        $request->session()->put('master_company_id', $user->master_company_id);
        $request->session()->put('master_id', $user->master_id);
        $request->session()->put('is_master', $user->is_master);

        Auth::login($user);

        // Log in as the specified user

        if ($ut == "SA") {

            return redirect()->route('admin.dashboard');

        } elseif ($ut == "WC") {

            return redirect()->route('white-label.dashboard');

        } elseif ($ut == "RS") {

            return redirect()->route('reseller.dashboard');

        } elseif ($ut == "EC") {

            return redirect()->route('company.dashboard');

        } elseif ($ut == "TR") {

            return redirect()->route('transport.dashboard');

        } elseif ($ut == "U") {

            return redirect()->back();
        }
    }

}
