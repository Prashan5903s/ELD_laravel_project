<?php

namespace App\Http\Controllers\WhiteLabel;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Add this line to import the Auth facade
class WhiteProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $countries = Country::with(['states' => function ($query) {
            $query->where('is_active', 1)->with(['cities' => function ($query) {
                $query->where('is_active', 1);
            }]);
        }])
            ->where('is_active', 1)
            ->get();
        $timezones = [];
        $rolesCount = "";
        $usersCount = "";
        $resellerCount = User::where('user_type', 'RS')->where('master_company_id', $request->session()->get('ids'))->count();
        $companyCount = User::where('user_type', 'EC')->where('master_company_id', $request->session()->get('ids'))->count();
        $transportCount = User::where('user_type', 'TR')->where('master_company_id', $request->session()->get('ids'))->count();
        foreach (timezone_identifiers_list() as $timezone) {
            $dt = new \DateTime('now', new \DateTimeZone($timezone)); // Use \DateTime and \DateTimeZone without namespace
            $offset = $dt->getOffset() / 3600;
            $offsetString = ($offset >= 0 ? '+' : '-') . sprintf('%02d', abs($offset)) . ':00';
            $timezones[$timezone] = "(GMT$offsetString) " . str_replace('_', ' ', $timezone);
        }
        return view('white-label.profile.index', compact('user', 'countries', 'timezones', 'rolesCount', 'usersCount', 'resellerCount', 'transportCount', 'companyCount'));
    }

    public function edit(Request $request)
    {
        $user = Auth::user();
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
        return view('white-label.profile.edit', compact('user', 'countries', 'timezones'));
    }
    public function post(Request $request, $id)
    {
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

        // Validate request
        $request->validate($rules);

        // Find the user
        $user = User::find($id);

        // Handle avatar image upload
        if ($request->hasFile('file')) {
            $imageName = time() . '.' . $request->file->extension();
            $request->file->move(public_path('white'), $imageName);
        } else {
            $imageName = $user->avatar_image; // Retain the previous image if no new image is provided
        }

        // Update user information
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user_type' => 'WC',
            'comp_name' => $request->comp_name,
            'avatar_image' => $imageName,
            'email' => $request->email,
            'mobile_no' => $request->mobile_no,
            'landline_no' => $request->landline_no,
            'country_id' => $request->country_id,
            'timezone' => $request->timezone,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'pin_code' => $request->pincode,
            'is_active' => $request->is_active,
            'address' => $request->address,
            'is_master' => 1,
        ]);

        // Update session values
        session()->put('ids', $user->id);
        session()->put('first', $user->first_name);
        session()->put('last', $user->last_name);
        session()->put('email', $user->email);
        session()->put('avatar', $user->avatar_image);
        session()->put('is_master', $user->is_master);

        return redirect()->route('white-label.profile.index');
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
