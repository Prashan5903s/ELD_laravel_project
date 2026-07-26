<?php

namespace App\Http\Controllers\SuperAdmin\WhiteLabel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\Timezone;
use App\Models\City;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NotifyNotification;

class WhiteLabelController extends Controller
{
    public function index(Request $request)
    {
        $user = User::where('user_type', "SA")->first();
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
        $users = User::where('user_type', 'WC')->get();
        return view('super-admin.white-label.index', compact('users'));
    }

    public function add()
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

        return view('super-admin.white-label.add', compact('timezones', 'countries'));
    }

    public function postEdit(Request $request, $id)
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
            return redirect()->route('white-label.index');
        } else {
            if ($user->user_type != "WC") {
                $request->session()->flash('error', 'This is wrong user type.');
                return redirect()->route('white-label.index');
            }
        }
        if ($request->hasFile('file')) {
            $imageName = time() . '.' . $request->file->extension();
            $request->file->move(public_path('whiteLabel'), $imageName);
        } else {
            $imageName = $user->avatar_image; // Retain the previous image if no new image is provided
        }

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->user_type = 'WC';
        $user->comp_name = $request->comp_name;
        $user->avatar_image = $imageName;
        $user->email = $request->email;
        $user->password = $request->password ? bcrypt($request->password) : $user->password; // Check if password provided, if not, retain the previous one
        $user->mobile_no = $request->mobile_no;
        // $user->landline_no = $request->landline_no;
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
            $user->master_company_id = $id;
        } else {
            // If is_master is not 1, set master_id and master_company_id to the values from session
            $user->master_company_id = $id;
            $user->master_id = $id;
        }

        $user->save();

        $user = User::where('user_type', 'SA')->first();
        $message = "New white label has been edited " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('white-label.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        $request->session()->flash('success', 'White label updated successfully.');

        return redirect('white-label');
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

        $user = User::find($id);

        if (!$user) {
            $request->session()->flash('error', 'This id does not exist.');
            return redirect()->route('white-label.index');
        } else {
            if ($user->user_type != "WC") {
                $request->session()->flash('error', 'This is wrong user type.');
                return redirect()->route('white-label.index');
            }
        }
        return view('super-admin.white-label.edit', compact('user', 'timezones', 'countries'));
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
            'city_id' => 'required|integer',
            'address' => 'required|string',
            'timezone' => 'required|string',
        ];

        $messages = [
            'confirm_password.same' => 'The password and confirm password must match.',
        ];

        // Validate the request
        $request->validate($rules, $messages);

        // Generate unique name for the image
        if ($request->hasFile('file')) {
            $imageName = time() . '.' . $request->file->extension();
            $request->file->move(public_path('whiteLabel'), $imageName);
        } else {
            $imageName = null;
        }

        // Create new user instance
        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->user_type = 'WC';
        $user->comp_name = $request->comp_name;
        $user->avatar_image = $imageName;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->mobile_no = $request->mobile_no;
        $user->country_code = $request->country_code;
        $user->landline_no = $request->landline_no;
        $user->country_id = $request->country_id;
        $user->state_id = $request->state_id;
        $user->is_active = $request->is_active;
        $user->city_id = $request->city_id;
        $user->pin_code = $request->pincode;
        $user->address = $request->address;
        $user->timezone = $request->timezone;
        $user->is_master = 1;

        if ($is_master == 1) {
            $user->save();
            $user->master_company_id = $user->id;
            $user->master_id = $request->session()->get('ids');
        } else {
            $user->save();
            $user->master_company_id = $user->id;
            $user->master_id = $user->id;
        }

        // Save the user
        $user->save();

        $user = User::where('user_type', 'SA')->first();
        $message = "New white label has been added " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('white-label.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        $request->session()->flash('success', 'White label updated successfully.');

        return redirect('white-label')->with('message', 'Successfully added');

    }

    public function getStates(Request $request)
    {

        $states = State::where('country_id', $request->country_id)->get();
        return response()->json($states);
    }

    public function getCities(Request $request)
    {
        $cities = City::where('state_id', $request->state_id)->get();
        return response()->json($cities);
    }
    public function login()
    {
        return view('white-label.login');
    }
    public function searchUsers(Request $request)
    {

        $searchTerm = $request->input('searchTerm');

        // Perform your search query based on $searchTerm
        // For example:
        $filteredData = User::where('first_name', 'like', '%' . $searchTerm . '%')
            ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
            ->orWhere('email', 'like', '%' . $searchTerm . '%')
            ->orWhere('mobile_no', 'like', '%' . $searchTerm . '%')
            ->get();

        // Return the filtered data as JSON
        return response()->json($filteredData);
    }
}
