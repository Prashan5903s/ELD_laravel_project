<?php

namespace App\Http\Controllers\Reseller\LeadBy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Country;

class LeadByController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('user_type', 'TR')->where('master_id', $request->session()->get('ids'))->get();
        return view('reseller.leadby.index', compact('users'));
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
        return view('reseller.leadby.add', compact('countries', 'timezones'));
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

        // // Validate the request

        $request->validate($rules, $messages);

        // // Generate unique name for the image

        if ($request->hasFile('file')) {
            $imageName = time() . '.' . $request->file->extension();
            $request->file->move(public_path('leadby'), $imageName);
        } else {
            $imageName = null;
        }

        // // Create new user instance

        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->user_type = 'TR';
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

        return redirect()->route('leadby.index')->with('message', 'Successfully saved');

    }

    public function edit(Request $request, $id){
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
        return view('reseller.leadby.edit', compact('user', 'timezones', 'countries'));
    }

    public function post(Request $request, $id){

        $is_master = $request->session()->get('is_master');

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
            $request->file->move(public_path('leadby'), $imageName);
        } else {
            $imageName = $user->avatar_image; // Retain the previous image if no new image is provided
        }

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->user_type = 'TR';
        $user->comp_name = $request->comp_name;
        $user->avatar_image = $imageName;
        $user->email = $request->email;
        $user->password = $request->password ? bcrypt($request->password) : $user->password; // Check if password provided, if not, retain the previous one
        $user->mobile_no = $request->mobile_no;
        $user->landline_no = $request->landline_no;
        $user->country_id = $request->country_id;
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

        return redirect()->route('leadby.index')->with('message', 'Successfully saved');
    }
}
