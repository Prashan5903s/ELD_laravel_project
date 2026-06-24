<?php

namespace App\Http\Controllers\API\Transport;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class FleetUserAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['user'] = User::where('user_type', 'FU')->where('master_id', Auth::user()->id)->get();
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['role'] = Role::where('master_id', 0)->orWhere('master_id', Auth::user()->id)->get();
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user_type' => 'FU',
            'is_active' => $request->is_active,
            'mobile_no' => $request->phone,
            'email' => $request->email, // Fix: replaced ',' with '=>'
            'password' => Hash::make($request->password),
            'master_id' => Auth::user()->id,
            'master_company_id' => Auth::user()->master_company_id,
        ]);

        UserInfo::create([
            'user_id' => $user->id,
            'fleet_user_id' => $request->user_id,
        ]);

        RoleUser::create([
            'user_id' => $user->id,
            'role_id' => $request->role_id, // Fix: replaced ',' with '=>'
        ]);

        return response()->json('Added successfully');
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
        $data['user'] = User::find($id);
        $data['userInfo'] = UserInfo::where('user_id', $id)->first();
        $data['role'] = RoleUser::where('user_id', $id)->first();

        return response()->json($data);
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
        $user = User::find($id);

        if ($user) {
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'user_type' => 'FU',
                'mobile_no' => $request->phone,
                'email' => $request->email,
                'is_active' => $request->is_active,
                'password' => Hash::make($request->password),
                'master_id' => Auth::user()->id,
                'master_company_id' => Auth::user()->master_company_id,
            ]);

            $userInfo = UserInfo::where('user_id', $user->id)->first();

            if ($userInfo) {
                $userInfo->update([
                    'user_id' => $user->id,
                    'fleet_user_id' => $request->user_id,
                ]);
            } else {
                // Handle case where UserInfo does not exist, optionally create a new UserInfo
                UserInfo::create([
                    'user_id' => $user->id,
                    'fleet_user_id' => $request->user_id,
                    // other fields here...
                ]);
            }

            RoleUser::where('user_id', $user->id)->delete();

            $roleUser = RoleUser::where('user_id', $user->id)->first();

            if ($roleUser) {
                $roleUser->update([
                    'user_id' => $user->id,
                    'role_id' => $request->role_id,
                ]);
            } else {
                // Handle case where RoleUser does not exist, optionally create a new RoleUser
                RoleUser::create([
                    'user_id' => $user->id,
                    'role_id' => $request->role_id,
                ]);
            }

            return response()->json("Update successfully");
        }

        return response()->json("User not found", 404);
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
