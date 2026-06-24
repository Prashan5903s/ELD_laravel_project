<?php

namespace App\Http\Controllers\Mobile\API;

use App\Models\ListOption;
use App\Models\User;
use App\Models\Rules;
use App\Models\UserInfo;
use App\Models\RuleAssign;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SettingMobileAPIController extends Controller
{
    public function account_data()
    {

        $auth = Auth::check();

        if ($auth) {

            $user = Auth::user();

            $userId = $user->id;

            $user = Auth::user();

            $selectedUser = $user ? $user->load([
                'language' => function ($query) {
                    $query->select('id', 'short_name', 'logo', 'language_name'); // Select specific fields
                }
            ])->only([
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'mobile_no',
                        'address',
                        'pin_code',
                        'timezone',
                        'avatar_image'
                    ]) : null;

            // Add language details if available
            if ($selectedUser && $user->language) {
                $selectedUser['language'] = $user->language; // This will add the full language data
            }

            $userInfo = UserInfo::where('user_id', $userId)->select('driver_id', 'licenseNumber', 'username')->first();

            $data = [
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data fetched successfully',
                'user' => $selectedUser,
                'user_info' => $userInfo,

            ];


        } else {

            $data = [
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'Not authenticated',
            ];

        }

        return response()->json($data, $data['statusCode']);

    }

    public function account_data_edit(Request $request)
    {

        $auth = Auth::check();

        if ($auth) {

            $user = Auth::user();

            $userId = $user->id;

            $userInfo = UserInfo::where('user_id', $userId)->first();

            try {
                $request->validate([
                    'first_name' => 'required|alpha|max:100', // Max length of 100 characters
                    'last_name' => 'required|alpha|max:100', // Max length of 100 characters
                    'driver_id' => 'required|numeric|digits_between:1,10', // Max 10 digits for driver_id
                    'email' => 'required|email|unique:users,email,' . ($userId ?? 'NULL'),
                    'phone' => 'required|numeric|digits_between:10,15', // Phone number must be between 10 and 15 digits long
                    'language_id' => 'required|exists:language,id',
                    'pincode' => 'required|numeric|digits_between:4,10', // Pincode length between 4 and 10 digits
                    'address' => 'required|max:200', // Max length of 200 characters for address
                    'timezone' => 'required|max:100', // Max length of 100 characters for timezone
                    'username' => [
                        'required',
                        'alpha_num',
                        'min:8',
                        'max:20',
                        Rule::unique('user_info')->ignore($userId, 'user_id'), // Ignore the username of the current user
                    ],
                    'licenseNumber' => 'required',
                ]);
            } catch (ValidationException $e) {
                return response()->json([
                    'status' => 'failure',
                    'statusCode' => 422,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(), // Include validation error messages
                ], 422);
            }




            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile_no' => $request->phone,
                'language_id' => $request->language_id,
                'address' => $request->address,
                'pin_code' => $request->pin_code,
                'timezone' => $request->timezone,
            ]);

            $userInfo->update([
                'driver_id' => $request->driver_id,
                'licenseNumber' => $request->licenseNumber,
                'username' => $request->username,
            ]);

            $data = [
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data saved successfully',
            ];

        } else {

            $data = [
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'Not authenticated',
            ];

        }

        return response()->json($data, $data['statusCode']);

    }

    public function setting_change_password(Request $request)
    {
        $auth = Auth::check();

        if ($auth) {

            $user = Auth::user();
            $userId = $user->id;

            try {
                // Validate input
                $request->validate([
                    'current_password' => 'required',
                    'password' => [
                        'required',
                        'min:8',
                        'regex:/[A-Za-z]/',        // At least one letter
                        'regex:/[0-9]/',            // At least one number
                        'regex:/[A-Za-z0-9@$.!%*?&#]/', // Allows letters, numbers, and special characters including @ and .
                    ],
                    'confirm_password' => [
                        'required',
                        'same:password',            // Must match password
                    ],
                ]);

            } catch (ValidationException $e) {
                return response()->json([
                    'status' => 'failure',
                    'statusCode' => 422,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(), // Include validation error messages
                ], 422);
            }

            // Check if the current password matches the stored password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => 'failure',
                    'statusCode' => 403,
                    'message' => 'Current password does not match the existing user password',
                ]);
            }

            // Update the password
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Password changed successfully',
            ]);
        } else {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'Not authenticated',
            ]);
        }
    }

    public function settings_account_general_data()
    {

        $auth = Auth::check();

        if ($auth) {

            $user = Auth::user();

            $userId = $user->id;

            $userInfo = UserInfo::where('user_id', $userId)->first();



        } else {

            $data = [
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'Not authenticated'
            ];

        }

        return response()->json($data, $data['statusCode']);

    }

    public function setting_carrier_data()
    {

        $auth = Auth::check();

        if ($auth) {

            $user = Auth::user();

            $userId = $user->id;

            $userInfo = UserInfo::where('user_id', $userId)->select('main_office_address', 'home_terminal_address', 'home_terminal_name', 'career_name')->first();

            $selectedUser = $user ? $user->only([
                'id',
                'address',
                'pin_code',
                'timezone',
            ]) : null;

            $data = [
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data fetched successfully',
                'user' => $selectedUser,
                'user_info' => $userInfo,
            ];


        } else {

            $data = [
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'Not authenticated'
            ];

        }

        return response()->json($data, $data['statusCode']);

    }

    public function setting_carrier_update(Request $request)
    {

        $auth = Auth::check();

        if ($auth) {

            $user = Auth::user();

            $userId = $user->id;

            try {
                // Validate input
                $request->validate([
                    'career_name' => [
                        'required',
                        'max:30',
                        'regex:/^[A-Za-z\s]+$/i',
                    ],
                    'main_office_address' => [
                        'required',
                        'max:100',
                    ],
                    'home_terminal_address' => 'required',
                    'address' => [
                        'required',
                    ],
                    'pincode' => 'required|numeric|digits_between:4,10', // Pincode length between 4 and 10 digits
                ]);


            } catch (ValidationException $e) {

                return response()->json([
                    'status' => 'failure',
                    'statusCode' => 422,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(), // Include validation error messages
                ], 422);

            }

            $userInfo = UserInfo::where('user_id', $userId)->first();

            $userInfo->update([
                'main_office_address' => $request->main_office_address,
                'career_name' => $request->career_name,
                'home_terminal_address' => $request->home_terminal_address,
            ]);

            $user->update([
                'address' => $request->address,
                'pin_code' => $request->pincode,
            ]);

            $data = [
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data updated successfully',
            ];

        } else {

            $data = [
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'Not authenticated'
            ];

        }

        return response()->json($data, $data['statusCode']);

    }

    public function setting_cycle_rule_data()
    {

        $auth = Auth::check();

        if ($auth) {

            $user = Auth::user();

            $userId = $user->id;

            $rule_assign = RuleAssign::where('user_id', $userId)
                ->select('id', 'rule_id')
                ->with([
                    'rule' => function ($query) {
                        $query->select('id', 'name');
                    }
                ])
                ->get();

            $userInfo = UserInfo::where('user_id', $userId)->first();

            $cargo_type_id = $userInfo->cargo_type_id;

            $cargo_type = ListOption::where('list_id', 'cargo_type')->select('option_id', 'title')->get();

            $cycle_rule = Rules::where('reason', 2)->orWhere('reason', 5)->select('id', 'name', 'title', 'description', 'max_hour_limit')->get();

            $shift_rule = Rules::where('reason', 1)->select('id', 'name', 'title', 'description', 'max_hour_limit')->get();

            $break_rule = Rules::where('reason', 4)->select('id', 'name', 'title', 'description', 'max_hour_limit')->get();

            $drive_rule = Rules::where('reason', 3)->select('id', 'name', 'title', 'description', 'max_hour_limit')->get();

            $shift_restart_rule = Rules::where('reason', 7)->select('id', 'name', 'title', 'description', 'max_hour_limit')->get();

            $cycle_restart_rule = Rules::where('reason', 8)->select('id', 'name', 'title', 'description', 'max_hour_limit')->get();

            $adverse_driving_rule = Rules::where('reason', 8)->select('id', 'name', 'title', 'description', 'max_hour_limit')->get();

            $data = [
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data updated successfully',
                'cycle_rule' => $cycle_rule,
                'shift_rule' => $shift_rule,
                'break_rule' => $break_rule,
                'drive_rule' => $drive_rule,
                'shift_restart_rule' => $shift_restart_rule,
                'cycle_restart_rule' => $cycle_restart_rule,
                'adverse_driving_rule' => $adverse_driving_rule,
                'cargo_type' => $cargo_type,
                'selected_cargo_type_id' => $cargo_type_id,
                'selected_rule_assign' => $rule_assign,
            ];

        } else {

            $data = [
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'Not authenticated'
            ];

        }

        return response()->json($data, $data['statusCode']);

    }

    public function setting_cycle_rule_update(Request $request)
    {

        $auth = Auth::check();

        if ($auth) {

            $user = Auth::user();

            $userId = $user->id;

            $userInfo = UserInfo::where('user_id', $userId)->first();

            try {
                // Validate input
                $request->validate([
                    'cycle_rule_id' => 'required',
                    'shift_rule_id' => 'required',
                    'break_rule_id' => 'required',
                    'drive_rule_id' => 'required',
                    'restart_rule_id' => 'required',
                    'adverse_driving_rule_id' => 'required',
                    'cargo_type_id' => 'required',
                ]);


            } catch (ValidationException $e) {

                return response()->json([
                    'status' => 'failure',
                    'statusCode' => 422,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(), // Include validation error messages
                ], 422);

            }

            RuleAssign::where('user_id', $userId)->delete();

            // Loop through all the rule IDs and create entries for each
            $rules = [
                $request->cycle_rule_id,
                $request->shift_rule_id,
                $request->break_rule_id,
                $request->drive_rule_id,
                $request->restart_rule_id,
                $request->adverse_driving_rule_id,
                9,
                7,
            ];

            foreach ($rules as $ruleId) {
                RuleAssign::create([
                    'user_id' => $userId,
                    'rule_id' => $ruleId,
                    'master_id' => $user->master_id,
                    'master_company_id' => $user->master_company_id,
                ]);
            }

            // Update user info with cargo_type_id
            $userInfo->update([
                'cargo_type_id' => $request->cargo_type_id,
            ]);

            $data = [
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data updated successfully',
            ];

        } else {

            $data = [
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'Not authenticated'
            ];

        }

        return response()->json($data, $data['statusCode']);

    }

}
