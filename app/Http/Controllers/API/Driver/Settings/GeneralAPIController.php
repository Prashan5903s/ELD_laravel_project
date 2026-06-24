<?php

namespace App\Http\Controllers\API\Driver\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Timezone;
use App\Models\UserInfo;
use App\Models\ListOption;

class GeneralAPIController extends Controller
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

            $data['timezone'] = Timezone::select('timezone_key', 'timezone_value')->get();

            $data['home_terminal_timezone'] = UserInfo::where('user_id', $userId)->select('home_terminal_timezone', 'odometer')->first();

            $data['odometer'] = ListOption::where('list_id', 'odometer')->select('option_id', 'title')->get();

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
            $userId = $user->id;

            $timezone = $request->timezone;
            $odometer = $request->odometer;

            $userInfo = UserInfo::where('user_id', $userId)->first();

            $userInfo->update([
                'odometer' => $odometer,
                'home_terminal_timezone' => $timezone
            ]);

            return response()->json([
                'status' => "Success",
                'statusCode' => 200,
                'message' => 'Data updated successfully!',
            ], 200);
        } else {
            return response()->json([
                'status' => "Failure",
                'statusCode' => 401,
                'message' => "User not authenticated!",
            ], 401);
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
