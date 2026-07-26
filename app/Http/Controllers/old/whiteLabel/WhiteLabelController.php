<?php

namespace App\Http\Controllers\whiteLabel;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class WhiteLabelController extends Controller
{
    public function index()
    {
        $users = User::where('user_type', 'WC')->get();
        return view('white-label.index', compact('users'));
    }

    public function add()
    {

        $countries = Country::with(['states' => function ($query) {
            $query->where('is_active', 1)->with(['cities' => function ($query) {
                $query->where('is_active', 1);
            }]);
        }])
            ->where('is_active', 1)
            ->get();
        $timezones = [];
        foreach (timezone_identifiers_list() as $timezone) {
            $dt = new \DateTime('now', new \DateTimeZone($timezone)); // Use \DateTime and \DateTimeZone without namespace
            $offset = $dt->getOffset() / 3600;
            $offsetString = ($offset >= 0 ? '+' : '-') . sprintf('%02d', abs($offset)) . ':00';
            $timezones[$timezone] = "(GMT$offsetString) " . str_replace('_', ' ', $timezone);
        }
        return view('white-label.add', compact('timezones', 'countries'));
    }

    public function postEdit(Request $request, $id)
    {
        $rules = [
            'comp_name' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            // 'email' => 'required|email|unique:users',
            // 'password' => 'required|string|min:6',
            // 'confirm_password' => 'required|string|min:6|same:password',
            'mobile_no' => 'required|string', // Adding the size rule to enforce 10 digits
            // 'landline_no' => 'string|size:20',
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'city_id' => 'required|integer',
            'timezone' => 'required|string',
            // 'pin_code' => 'required|string',
            'address' => 'required|string',
            // 'avatar_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Assuming avatar_image is the name of your file input
        ];
        // $messages = [
        //     'confirm_password.same' => 'The password and confirm password must match.',
        // ];
        $request->validate($rules);
        $user = User::find($id);
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
        $user->timezone = $request->timezone;
        $user->state_id = $request->state_id;
        $user->city_id = $request->city_id;
        $user->pin_code = $request->pincode;
        $user->is_active = $request->is_active;
        $user->address = $request->address;
        $user->is_master = 1;

        $user->master_company_id = $id;
        $user->master_id = $id;
        $user->save();

        return redirect('white-label');
    }

    public function edit(Request $request, $id)
    {
        $countries = Country::with(['states' => function ($query) {
            $query->where('is_active', 1)->with(['cities' => function ($query) {
                $query->where('is_active', 1);
            }]);
        }])
            ->where('is_active', 1)
            ->get();
        $timezones = [];
        foreach (timezone_identifiers_list() as $timezone) {
            $dt = new \DateTime('now', new \DateTimeZone($timezone)); // Use \DateTime and \DateTimeZone without namespace
            $offset = $dt->getOffset() / 3600;
            $offsetString = ($offset >= 0 ? '+' : '-') . sprintf('%02d', abs($offset)) . ':00';
            $timezones[$timezone] = "(GMT$offsetString) " . str_replace('_', ' ', $timezone);
        }
        $user = User::find($id);
        return view('white-label.edit', compact('user', 'timezones', 'countries'));
    }
    public function addForm(Request $request)
    {
        // Validation rules
        // echo ($request->has('file'));
        // exit();
        $rules = [
            'comp_name' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:password',
            'mobile_no' => 'required|string', // Adding the size rule to enforce 10 digits
            // 'landline_no' => 'string',
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'city_id' => 'required|integer',
            // 'pin_code' => 'required|string',
            'address' => 'required|string',
            'timezone' => 'required|string',
            // 'avatar_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Assuming avatar_image is the name of your file input
        ];

        $messages = [
            'confirm_password.same' => 'The password and confirm password must match.',
        ];
        // Custom error messages
        // $messages = [
        //     'avatar_image.max' => 'The avatar image may not be greater than 2MB in size.',
        // ];

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
        $user->landline_no = $request->landline_no;
        $user->country_id = $request->country_id;
        $user->state_id = $request->state_id;
        $user->is_active = $request->is_active;
        $user->city_id = $request->city_id;
        $user->pin_code = $request->pincode;
        $user->address = $request->address;
        $user->timezone = $request->timezone;
        $user->is_master = 1;

        // Save the user
        $user->save();

        // Set master_company_id and master_id to the user's ID
        $user->master_company_id = $user->id;
        $user->master_id = $user->id;

        // Save the user again to update master_company_id and master_id
        $user->save();

        // Return success response
        return redirect('white-label');
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

        // echo $searchTerm;
        // exit();


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
