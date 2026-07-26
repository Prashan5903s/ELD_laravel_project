<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CompController extends Controller
{
    public function index(Request $request)
    {
        if ($request->session()->get('comp_user_change') != false) {
            return redirect()->route('company.user.change');
        }
        return view('company.dashboard');
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
            $request->session()->remove('chUsers');
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

    public function change_user(Request $request)
    {
        if (!$request->session()->has('comp_user_change')) {
            return redirect()->route('company.dashboard');
        }

        $id = Auth::user()->id;
        $user = User::where('user_type', 'TR')->where('master_id', $id)->get();
        return view('company.userChange.view', compact('user'));

    }

    public function change_dashboard(Request $request, $id)
    {
        $user = User::find($id);

        if ($user->user_type == "EC") {
            if (Auth::user()->id != $id) {
                $request->session()->flash('error', "Invalid user.");
                return redirect()->route('company.user.change');
            } else {
                if ($request->session()->has('comp_user_change') || $request->session()->has('chUsers') || $request->session()->has('after_change')) {
                    Auth::login($user);
                    $request->session()->put('ids', $user->id);
                    $request->session()->put('first', $user->first_name);
                    $request->session()->put('last', $user->last_name);
                    $request->session()->put('email', $user->email);
                    $request->session()->put('avatar', $user->avatar_image);
                    $request->session()->put('master_company_id', $user->master_company_id);
                    $request->session()->put('master_id', $user->master_id);
                    $request->session()->put('is_master', $user->is_master);
                    // Do something with the user, like echo its details
                    $request->session()->remove('comp_user_change');
                    $request->session()->put('after_change', true);
                    return redirect()->route('transport.dashboard');
                } else {
                    // Handle if user not found or user type is not "TR"
                    $request->session()->flash('error', "Invalid user or user type.");
                    return redirect()->route('company.dashboard');
                }
            }
        } elseif ($user->user_type == "TR") {
            if (Auth::user()->id != $user->master_id) {
                $request->session()->flash('error', "Invalid user.");
                return redirect()->route('company.user.change');
            } else {
                if ($request->session()->has('comp_user_change') || $request->session()->has('chUsers') || $request->session()->has('after_change')) {
                    Auth::login($user);
                    $request->session()->put('ids', $user->id);
                    $request->session()->put('first', $user->first_name);
                    $request->session()->put('last', $user->last_name);
                    $request->session()->put('email', $user->email);
                    $request->session()->put('avatar', $user->avatar_image);
                    $request->session()->put('master_company_id', $user->master_company_id);
                    $request->session()->put('master_id', $user->master_id);
                    $request->session()->put('is_master', $user->is_master);
                    // Do something with the user, like echo its details
                    $request->session()->put('comp_user_change', true);
                    return redirect()->route('transport.dashboard');
                } else {
                    // Handle if user not found or user type is not "TR"
                    $request->session()->flash('error', "Invalid user or user type.");
                    return redirect()->route('company.dashboard');
                }
            }
        } else {
            $request->session()->flash('error', "Invalid user type.");
            return redirect()->route('company.user.change');
        }
    }
    public function chUsers(Request $request, $id)
    {
        if(!$request->session()->has('after_change')){
            $request->session()->flash('error', 'Wrong user 1');
            return redirect()->route('company.dashboard');
        }
        if(Auth::user()->id == $id){
            $request->session()->put('comp_user_change', true);
            return redirect()->route('company.user.change');
        } else {
            $request->session()->flash('error', 'Wrong user 2');
            return redirect()->route('company.dashboard');
        }
    }
}
