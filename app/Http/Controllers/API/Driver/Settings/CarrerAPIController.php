<?php

namespace App\Http\Controllers\API\Driver\Settings;

use App\Http\Controllers\Controller;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Location;

class CarrerAPIController extends Controller
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

            $user = Auth::user();

            $userId = $user->id;

            $masterId = $user->master_id;

            $data['userInfo'] = UserInfo::where('user_id', $userId)->with('homeTerminal')->select('career_name', 'main_office_address', 'home_terminal_address')->first();

            $data['location'] = Location::where('created_by', $masterId)->select('id', 'name', 'address')->get();

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

            $carrerName = $request->carrer_name;
            $homeTerminal = $request->home_terminal_address;
            $mainOffice = $request->main_office_address;

            $userInfo = UserInfo::where('user_id', $userId)->first();

            $userInfo->update([
                'career_name' => $carrerName,
                'home_terminal_address' => $homeTerminal,
                'main_office_address' => $mainOffice
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
