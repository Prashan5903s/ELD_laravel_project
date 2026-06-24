<?php

namespace App\Http\Controllers\Mobile\API;

use App\Models\User;
use App\Models\Group;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class GroupAssignMobileApiController extends Controller
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

            $groups = Group::where('created_by', $userId)
                ->select('group_id', 'group_title', 'group_name', 'group_description')
                ->with(['userGroups:user_id,group_id', 'userGroups.user:id,first_name,last_name'])
                ->get();


            $data = [

                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data fetched successfully',
                'data' => $groups,

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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $auth = Auth::check();

        if ($auth) {

            $user = Auth::user();

            $userId = $user->id;

            $masterId = $user->master_id;

            $user = User::where('master_id', $masterId)
                ->select('id', 'first_name', 'last_name')
                ->where('id', '!=', $userId)
                ->get();

            $groups = Group::where('created_by', $userId)
                ->select('group_id', 'group_title', 'group_name', 'group_description')
                ->with(['userGroups', 'userGroups.user'])
                ->get();

            $datas = [
                'group_data' => $groups,
                'user_data' => $user,
            ];

            $data = [

                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data fetched successfully',
                'data' => $datas,

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

            try {
                $request->validate([
                    'user_id' => [
                        'required',
                        Rule::unique('user_group')
                            ->where('group_id', $request->group_id), // Exclude current record by ID (replace $userGroup->id with the correct record ID)
                    ],
                    'group_id' => 'required',
                ]);
            } catch (ValidationException $e) {
                return response()->json([
                    'status' => 'failure',
                    'statusCode' => 422,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            $user_id = $request->user_id;

            if (is_string($user_id) && preg_match('/^\[.*\]$/', $user_id)) {
                $user_id = json_decode($user_id, true); // Convert to array
            }

            $userId = $user->id;

            UserGroup::create([

                'user_id' => $userId,
                'group_id' => $request->group_id,
                'is_active' => 1,

            ]);

            foreach ($user_id as $id) {

                UserGroup::create([

                    'user_id' => $id,
                    'group_id' => $request->group_id,
                    'is_active' => 1,

                ]);

            }

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

        $auth = Auth::check();

        if ($auth) {

            $userGroup = UserGroup::find($id);

            if (!$userGroup) {

                return response()->json([
                    'status' => 'success',
                    'statusCode' => 404,
                    'message' => "Data does not exist"
                ]);

            }

            $group_id = $userGroup->group_id;

            $group = Group::where('group_id', $group_id)->select('group_id', 'group_title', 'group_name', 'group_description')
                ->with(['userGroups:user_id,group_id', 'userGroups.user:id,first_name,last_name'])->first();

            $data = [

                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data saved successfully',
                'data' => $group,

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

    public function post_update(Request $request, $id)
    {

        $auth = Auth::check();

        if ($auth) {

            $user = Auth::user();

            $userGroup = UserGroup::find($id);

            try {
                $request->validate([
                    'user_id' => [
                        'required',
                        Rule::unique('user_group')
                            ->where('group_id', $request->group_id)
                            ->ignore($userGroup->id), // Exclude current record by ID (replace $userGroup->id with the correct record ID)
                    ],
                    'group_id' => 'required',
                ]);
            } catch (ValidationException $e) {
                return response()->json([
                    'status' => 'failure',
                    'statusCode' => 422,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            if (!$userGroup) {

                return response()->json([
                    'status' => 'success',
                    'statusCode' => 404,
                    'message' => "Data does not exist"
                ]);

            }

            $group_id = $userGroup->group_id;

            UserGroup::where('group_id', $group_id)->delete();

            $user_id = $request->user_id;

            if (is_string($user_id) && preg_match('/^\[.*\]$/', $user_id)) {
                $user_id = json_decode($user_id, true); // Convert to array
            }

            $userId = $user->id;

            UserGroup::create([

                'user_id' => $userId,
                'group_id' => $group_id,
                'is_active' => 1,

            ]);

            foreach ($user_id as $id) {

                UserGroup::create([

                    'user_id' => $id,
                    'group_id' => $group_id,
                    'is_active' => 1,

                ]);

            }

            $data = [

                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data updated successfully',

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
