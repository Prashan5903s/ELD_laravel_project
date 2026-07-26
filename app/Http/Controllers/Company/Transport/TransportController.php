<?php

namespace App\Http\Controllers\Company\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserInfo;
use App\Models\Language;
use App\Models\User;
use App\Models\Country;
use App\Models\Timezone;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NotifyNotification;

class TransportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->session()->has('comp_user_change')) {
            return redirect()->route('company.user.change');
        }
        $users = User::where('user_type', 'TR')->where('master_id', $request->session()->get('ids'))->get();
        return view('company.transport.index', compact('users'));
    }
    public function add(Request $request)
    {
        if ($request->session()->has('comp_user_change')) {
            return redirect()->route('company.user.change');
        }
        $countries = Country::with(['states' => function ($query) {
            $query->where('is_active', 1)->with(['cities' => function ($query) {
                $query->where('is_active', 1);
            }]);
        }])
            ->where('is_active', 1)
            ->get();

        $lang = Language::where('is_active', 1)->get();

        $timezones = Timezone::where('status', 1)->get();

        return view('company.transport.add', compact('countries', 'timezones', 'lang'));
    }
    public function addForm(Request $request)
    {
        if ($request->session()->has('comp_user_change')) {
            return redirect()->route('company.user.change');
        }

        $is_master = $request->session()->get('is_master');


        $rules = [
            'comp_name' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:password',
            'mobile_no' => 'required|string', // Adding the size rule to enforce 10 digits
            'language_id' => 'required|integer',
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'city_id' => 'required|integer',
            'address' => 'required|string',
            'timezone' => 'required|string',
        ];

        $messages = [
            'confirm_password.same' => 'The password and confirm password must match.',
        ];

        // // Validate the request

        $request->validate($rules, $messages);

        // // Generate unique name for the image

        if ($request->hasFile('file')) {
            $imageName = time() . '.' . $request->file->extension();
            $request->file->move(public_path('transportFolder'), $imageName);
        } else {
            $imageName = null;
        }

        // // Create new user instance

        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->user_type = 'TR';
        $user->language_id = $request->language_id;
        $user->comp_name = $request->comp_name;
        $user->avatar_image = $imageName;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->mobile_no = $request->mobile_no;
        $user->landline_no = $request->landline_no;
        $user->country_id = $request->country_id;
        $user->country_code = $request->country_code;
        $user->state_id = $request->state_id;
        $user->is_active = $request->is_active;
        $user->city_id = $request->city_id;
        $user->pin_code = $request->pincode;
        $user->address = $request->address;
        $user->timezone = $request->timezone;

        $user->is_master = 1;
              $user->is_master = 1;
        if ($is_master == 1) {
            $user->master_id = $request->session()->get('ids');
            $user->master_company_id = $request->session()->get('master_company_id');
        } else {
            $user->save();
            $user->master_company_id = $user->id;
            $user->master_id = $user->id;
        }

        // Save the user
        $user->save();

        $userInfos = new UserInfo;
        $userInfos->user_id = $user->id;
        $userInfos->save();

        $user = User::where('user_type', 'SA')->first();
        $message = "New transport has been added " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('company.dashboard');

        // Create an instance of the notification with dynamic data
        $notification = new NotifyNotification($message, $url);

        // Send the notification
        $user->notify($notification);

        $request->session()->flash('success', 'Transport updated successfukky!');

        return redirect()->route('transport.index');

    }

    public function edit(Request $request, $id){
        if ($request->session()->has('comp_user_change')) {
            return redirect()->route('company.user.change');
        }
        $countries = Country::with(['states' => function ($query) {
            $query->where('is_active', 1)->with(['cities' => function ($query) {
                $query->where('is_active', 1);
            }]);
        }])
            ->where('is_active', 1)
            ->get();

        $lang = Language::where('is_active', 1)->get();

        $timezones = Timezone::where('status', 1)->get();

        $user = User::find($id);
        if (!$user) {
           $request->session()->flash('error', 'This id does not exist.');
           return redirect()->route('transport.index');
        } else {
           if ($user->user_type != "TR") {
              $request->session()->flash('error', 'This is wrong user type.');
              return redirect()->route('transport.index');
            }
        }
        $userInfo = UserInfo::where('user_id', $id)->first();
        return view('company.transport.edit', compact('user', 'userInfo',    'timezones', 'countries', 'lang'));
    }

    public function post(Request $request, $id){
        if ($request->session()->has('comp_user_change')) {
            return redirect()->route('company.user.change');
        }

        $is_master = $request->session()->get('is_master');

        $rules = [
            'comp_name' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'mobile_no' => 'required|string', // Adding the size rule to enforce 10 digits
            'country_id' => 'required|integer',
            'language_id' => 'required|integer',
            'state_id' => 'required|integer',
            'city_id' => 'required|integer',
            'timezone' => 'required|string',
            'address' => 'required|string',
        ];

        $request->validate($rules);

        $user = User::find($id);
        if (!$user) {
           $request->session()->flash('error', 'This id does not exist.');
           return redirect()->route('transport.index');
        } else {
           if ($user->user_type != "TR") {
              $request->session()->flash('error', 'This is wrong user type.');
              return redirect()->route('transport.index');
            }
        }

        if ($request->hasFile('file')) {
            $imageName = time() . '.' . $request->file->extension();
            $request->file->move(public_path('transportFolder'), $imageName);
        } else {
            $imageName = $user->avatar_image; // Retain the previous image if no new image is provided
        }

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->user_type = 'TR';
        $user->language_id = $request->language_id;
        $user->comp_name = $request->comp_name;
        $user->avatar_image = $imageName;
        $user->email = $request->email;
        $user->password = $request->password ? bcrypt($request->password) : $user->password; // Check if password provided, if not, retain the previous one
        $user->mobile_no = $request->mobile_no;
        $user->landline_no = $request->landline_no;
        $user->country_id = $request->country_id;
        $user->country_code = $request->country_code;
        $user->timezone = $request->timezone;
        $user->state_id = $request->state_id;
        $user->city_id = $request->city_id;
        $user->pin_code = $request->pincode;
        $user->is_active = $request->is_active;
        $user->address = $request->address;
        $user->is_master = 1;

       if ($is_master == 1) {
            $user->master_id = $request->session()->get('ids');
            $user->master_company_id = $request->session()->get('master_company_id');
        } else {
            $user->master_company_id = $id;
            $user->master_id = $id;
        }

        $user->save();

        $userInfos = UserInfo::where('user_id', $id)->first();
        $userInfos->user_id = $user->id;
        $userInfos->save();

        $user = User::where('user_type', 'SA')->first();
        $message = "New transport has been edited " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('company.dashboard');

        // Create an instance of the notification with dynamic data
        $notification = new NotifyNotification($message, $url);

        // Send the notification
        $user->notify($notification);

        $request->session()->flash('success', 'Transport updated successfukky!');

        return redirect()->route('transport.index');

    }

}
