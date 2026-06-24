<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Language; // Import the Request class
use App\Models\State;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index($lang = null)
    {
        $languag = Language::where('Short_name', $lang)->first();
        if(!$languag){
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['en']);
        }else{
            App::setLocale($lang);
        }
        $userIds = Auth::user()->master_id;
        $trans = User::where('master_id', $userIds)->get();
        return view('transport.dashboard', compact('trans'));
    }


    public function default(){
        $user = Auth::user();
        $lang = Language::where('id', $user->language_id)->first();
        if(!$lang){
            return redirect()->to(route('transport.dashboard', ["en"]));
        }else{
            $short = $lang->Short_name;
            return redirect()->to(route('transport.dashboard', [$short]));
        }
    }


     public function changeId(Request $request, $lang, $id)
    {

        $languag = Language::where('Short_name', $lang)->first();
        if(!$languag){
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['en']);
        }else{
            App::setLocale($lang);
        }
        $request->session()->put('change_id', $id);
        $user = User::find($id);
        $ids = $user->id;
        $first = $user->first_name;
        $last = $user->last_name;
        $email = $user->email;
        $avatar = $user->avatar_image;
        $master_company_id = $user->master_company_id;
        $master_id = $user->master_id;
        $is_master = $user->is_master;


        $request->session()->put('ids', $ids);
        $request->session()->put('first', $first);
        $request->session()->put('last', $last);
        $request->session()->put('email', $email);
        $request->session()->put('avatar', $avatar);
        $request->session()->put('master_company_id', $master_company_id);
        $request->session()->put('master_id', $master_id);
        $request->session()->put('is_master', $is_master);

        return redirect()->back();

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

    public function checkMail(Request $request)
    {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            $available = true; // Email is found
        } else {
            $available = false; // Email is not found
        }

        return response()->json(['available' => $available]);
    }

        public function chUsers(Request $request, $id)
    {
        if (!$request->session()->has('comp_user_change')) {
            return redirect()->route('transport.dashboard');
        }
        $user = Auth::user();
        $ecUser = User::find($user->master_id);
        if ($ecUser->id == $id) {
            if ($ecUser->user_type == "EC") {
                Auth::login($ecUser); // Pass the $ecUser object here
                return redirect()->route('company.user.change');
            } else {
                $request->session()->flash('Error', 'Wrong user');
                return redirect()->route('transport.dashboard');
            }
        } else {
            $request->session()->flash('Error', 'Wrong user');
            return redirect()->route('transport.dashboard');
        }
    }

}
