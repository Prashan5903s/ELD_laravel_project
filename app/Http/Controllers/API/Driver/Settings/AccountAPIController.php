<?php

namespace App\Http\Controllers\API\Driver\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\Language;
use App\Models\State;
use Illuminate\Support\Facades\Hash;

class AccountAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $auth = Auth::check();

        if ($auth) {

            $userId = Auth::user()->id;

            $data['user'] = User::where('id', $userId)->select('language_id', 'first_name', 'last_name')->first();

            $data['userInfo'] = UserInfo::where('user_id', $userId)->select('licenseNumber', 'userName', 'driver_license_state', 'note', 'home_terminal_timezone')->first();

            $data['language'] = Language::select('id', 'language_name', 'logo')->get();

            $data['state'] = State::select('state_id', 'state_name')->get();

            return response()->json([
                'status' => "Success",
                'statusCode' => 200,
                'message' => "Data fetched successfully!",
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => "Failure",
                'statusCode' => 401,
                'message' => "User not authenticated!",
            ], 401);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $auth = Auth::check();

        if ($auth) {

            $user = Auth::user();

            $email = $request->email;

            $password = $request->password;

            $first_name = $request->first_name;

            $last_name = $request->last_name;

            $driverId = $request->driverId;

            $licenseNumber = $request->licenseNumber;

            $mobileNo = $request->mobile_no;

            $stateId = $request->state_id;

            $languageId = $request->language_id;

            $exist = User::where('id', '!=', $user->id)->where('email', $email)->exists();

            if ($exist) {
                return response()->json(
                    [
                        'status' => 'failure',
                        'statusCode' => 422,
                        'message' => "Email already exists",
                    ],
                    422
                );
            }

            $driver = User::where('id', $user->id)->first();

            $userInfo = UserInfo::where('user_id', $user->id)->first();

            $driver->update([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'password' => Hash::make($password),
                'mobile_no' => $mobileNo,
                'language_id' => $languageId
            ]);

            $userInfo->update([
                'licenseNumber' => $licenseNumber,
                'driver_id' => $driverId,
                'driver_license_state' => $stateId,
            ]);


            return response()->json([
                'status' => "Success",
                'statusCode' => 200,
                'message' => 'Data updated successfully!',
            ], 200);
        } else {
            return response()->json(
                [
                    'status' => 'failure',
                    'statusCode' => 401,
                    'message' => "User not authenticated",
                ],
                401
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
