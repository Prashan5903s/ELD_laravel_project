<?php

namespace App\Http\Controllers\WhiteLabel\Company;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Country;
use App\Models\Hardware;
use App\Models\Timezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NotifyNotification;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('user_type', 'EC')->where('master_id', $request->session()->get('ids'))->get();
        return view('white-label.company.index', compact('users'));
    }
    public function add(Request $request)
    {

        $countries = Country::with([
            'states' => function ($query) {
                $query->where('is_active', 1)->with([
                    'cities' => function ($query) {
                        $query->where('is_active', 1);
                    }
                ]);
            }
        ])
            ->where('is_active', 1)
            ->get();

        $hardware = Hardware::where('is_active', 1)->get();

        $reseller = User::where('is_active', 1)
            ->where('user_type', 'RS')->where('master_company_id', $request->session()->get('ids'))
            ->get();

        $timezones = Timezone::where('status', 1)->get();

        return view('white-label.company.add', compact('countries', 'timezones', 'reseller', 'hardware'));

    }

    public function addForm(Request $request)
    {



        $is_master = $request->session()->get('is_master');


        $rules = [
            'comp_name' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:password',
            'mobile_no' => 'required|string', // Adding the size rule to enforce 10 digits
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'hardware_id' => 'required|integer',
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
            $request->file->move(public_path('companyss'), $imageName);
        } else {
            $imageName = null;
        }

        // // Create new user instance

        $user = new User;
        $user->hardware_id = $request->hardware_id;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->user_type = 'EC';
        $user->comp_name = $request->comp_name;
        $user->avatar_image = $imageName;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->mobile_no = $request->mobile_no;
        $user->landline_no = $request->landline_no;
        $user->lead_by = $request->lead_by;
        $user->country_id = $request->country_id;
        $user->country_code = $request->country_code;
        $user->state_id = $request->state_id;
        $user->is_active = $request->is_active;
        $user->city_id = $request->city_id;
        $user->pin_code = $request->pincode;
        $user->address = $request->address;
        $user->timezone = $request->timezone;

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

        $user = User::where('user_type', 'SA')->first();
        $message = "New company has been added " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('white-label.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        $request->session()->flash('success', 'Company updated successfully.');

        return redirect()->route('company.index');

    }

    public function edit(Request $request, $id)
    {
        $countries = Country::with([
            'states' => function ($query) {
                $query->where('is_active', 1)->with([
                    'cities' => function ($query) {
                        $query->where('is_active', 1);
                    }
                ]);
            }
        ])
            ->where('is_active', 1)
            ->get();

        $timezones = Timezone::where('status', 1)->get();

        $reseller = User::where('is_active', 1)
            ->where('user_type', 'RS')
            ->get();

        $user = User::find($id);
        if (!$user) {
            $request->session()->flash('error', 'This id does not exist.');
            return redirect()->route('company.index');
        } else {
            if ($user->user_type != "EC") {
                $request->session()->flash('error', 'This is wrong user type.');
                return redirect()->route('company.index');
            }
        }
        return view('white-label.company.edit', compact('user', 'timezones', 'countries', 'reseller'));
    }

    public function post(Request $request, $id)
    {

        $is_master = $request->session()->get('is_master');

        $rules = [
            'comp_name' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'mobile_no' => 'required|string', // Adding the size rule to enforce 10 digits
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'city_id' => 'required|integer',
            'timezone' => 'required|string',
            'address' => 'required|string',
        ];

        $request->validate($rules);

        $user = User::find($id);
        if (!$user) {
            $request->session()->flash('error', 'This id does not exist.');
            return redirect()->route('company.index');
        } else {
            if ($user->user_type != "EC") {
                $request->session()->flash('error', 'This is wrong user type.');
                return redirect()->route('company.index');
            }
        }

        if ($request->hasFile('file')) {
            $imageName = time() . '.' . $request->file->extension();
            $request->file->move(public_path('companyss'), $imageName);
        } else {
            $imageName = $user->avatar_image; // Retain the previous image if no new image is provided
        }

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->user_type = 'EC';
        $user->comp_name = $request->comp_name;
        $user->avatar_image = $imageName;
        $user->email = $request->email;
        $user->password = $request->password ? bcrypt($request->password) : $user->password; // Check if password provided, if not, retain the previous one
        $user->mobile_no = $request->mobile_no;
        $user->landline_no = $request->landline_no;
        $user->lead_by = $request->lead_by;
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

        $user = User::where('user_type', 'SA')->first();
        $message = "New company has been edited " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('white-label.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        $request->session()->flash('success', 'Company updated successfully.');

        return redirect()->route('company.index');

    }

}
