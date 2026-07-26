<?php

namespace App\Http\Controllers\Mobile\API;

use App\Models\Group;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
class GroupMobileApiController extends Controller
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

            $group = Group::where('created_by', $userId)->get();

            $data = [
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data fetched successfully',
                'data' => $group
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

            try {
                $request->validate([
                    'group_name' => 'required|string|max:50|unique:groups,group_name', // Required, string, maximum length of 50, must be unique
                    'group_title' => 'required|string|max:50', // Required, string, maximum length of 50
                    'group_desc' => 'required|string|max:100', // Required, string, maximum length of 100
                    'status' => 'required'
                ]);

            } catch (ValidationException $e) {
                return response()->json([
                    'status' => 'failure',
                    'statusCode' => 422,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            $user = Auth::user();

            $userId = $user->id;

            Group::create([

                'group_name' => $request->group_name,
                'group_title' => $request->group_title,
                'group_description' => $request->group_desc,
                'is_active' => $request->status,
                'master_id' => $user->master_id,
                'master_company_id' => $user->master_company_id,
                'created_by' => $userId,

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

            $user = Auth::user();

            $userId = $user->id;

            $group = Group::where('group_id', $id)->where('created_by', $userId)->first();

            if (!$group) {

                return response()->json([
                    'status' => 'failure',
                    'statusCode' => 404,
                    'message' => 'Data does not exist'
                ]);

            }

            $data = [

                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data fetched successfully',
                'data' => $group,

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function update(Request $request, $id)
    {

    }
    
    public function post_update(Request $request, $id)
    {

        $auth = AUth::check();

        if ($auth) {

            try {
                $request->validate([
                    'group_name' => 'required|string|max:50|unique:groups,group_name,' . $id . ',group_id', // Exclude current record by ID
                    'group_title' => 'required|string|max:50',
                    'group_desc' => 'required|string|max:100',
                    'status' => 'required'
                ]);
            } catch (ValidationException $e) {
                return response()->json([
                    'status' => 'failure',
                    'statusCode' => 422,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            $user = Auth::user();

            $userId = $user->id;

            $group = Group::where('group_id', $id)->where('created_by', $userId)->first();

            if (!$group) {

                return response()->json([
                    'status' => 'failure',
                    'statusCode' => 404,
                    'message' => 'Data does not exist'
                ]);

            }

            $group->update([
                'group_name' => $request->group_name,
                'group_title' => $request->group_title,
                'group_description' => $request->group_desc,
                'is_active' => $request->status,
                'master_id' => $user->master_id,
                'master_company_id' => $user->master_company_id,
                'created_by' => $userId,
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
