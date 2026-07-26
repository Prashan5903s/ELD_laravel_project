<?php

namespace App\Http\Controllers\Mobile\API;

use App\Models\CoDriver;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Models\HOSActivityLog;
use App\Models\HOSUnsignedLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ActivityLogMobileAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($count = 10, $page = 1)
    {

        $check = Auth::check();

        if ($check) {

            $user = Auth::user();

            $userId = $user->id;

            // Adjust the query to include pagination
            $activityLog = ActivityLog::where('action_by', $userId)
                ->select('action', 'module', 'message', 'created_at')
                ->paginate($count, ['*'], 'page', $page);

            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Activity log fetched successfully',
                'activity_log_data' => $activityLog,
            ];

        } else {

            $data = [
                'status' => 'failure',
                'code' => 401,
                'message' => 'Not authenticated'
            ];

        }

        return response()->json($data, $data['code']);

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
        try {
            $request->validate([
                'first_name' => 'required|string', // Ensure the file is an image
                'last_name' => 'required|string',
                'date' => 'required',
                'cariier_name' => 'required',
                'main_office_address' => 'required',
                'home_terminal_address' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(), // Include validation error messages
            ], 422);
        }

        $auth = Auth::check();

        if (!$auth) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 403,
                'message' => 'User does not exist',
            ], 403);
        }

        $user = Auth::user();

        $userId = $user->id;

        $coDriver = CoDriver::where('user_id', $userId)->where('codriver_date', $request->date)->first();

        if ($coDriver) {
            if (!empty($request->codriver_id) && $request->codriver_id !== 'null') {
                $coDriver->update([
                    'codriver_id' => $request->codriver_id,
                    'updated_by' => $userId,
                ]);
            }
        } else {
            if (!empty($request->codriver_id) && $request->codriver_id !== 'null') {
                CoDriver::create([
                    'user_id' => $userId,
                    'codriver_id' => $request->codriver_id,
                    'codriver_date' => $request->date,
                    'is_approved' => 0,
                    'created_by' => $userId,
                    'master_id' => $user->master_id,
                    'master_company_id' => $user->master_company_id,
                ]);
            }
        }

        $exist = HOSActivityLog::where('user_id', $userId)->where('timeData', $request->date)->first();

        $unsignedLog = HOSUnsignedLog::where('user_id', $userId)->where('timeData', $request->date)->first();

        if ($unsignedLog) {

            $unsignedLog->update([
                'is_certify' => 0,
                'updated_by' => $userId,
            ]);
        } else {
            HOSUnsignedLog::create([
                'user_id' => $userId,
                'timeData' => $request->date,
                'created_by' => $userId,
                'master_id' => $user->master_id,
                'master_company_id' => $user->master_company_id,
            ]);
        }


        if ($exist) {

            $exist->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'user_id' => $userId,
                'timeData' => $request->date,
                'distance' => $request->distance,
                'odometer' => $request->odometer,
                'cariier_name' => $request->cariier_name,
                'main_office_address' => $request->main_office_address,
                'home_terminal_address' => $request->home_terminal_address,
                'fromLoc' => $request->fromLoc,
                'toLoc' => $request->toLoc,
                'notes' => $request->notes,
                'master_id' => $user->master_id,
                'master_company_id' => $user->master_company_id,
                'created_by' => $userId,
                'is_change_certified' => 0,
            ]);
        } else {
            $hosActivityLog = HOSActivityLog::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'user_id' => $userId,
                'timeData' => $request->date,
                'distance' => $request->distance,
                'odometer' => $request->odometer,
                'cariier_name' => $request->cariier_name,
                'main_office_address' => $request->main_office_address,
                'home_terminal_address' => $request->home_terminal_address,
                'fromLoc' => $request->fromLoc,
                'toLoc' => $request->toLoc,
                'notes' => $request->notes,
                'master_id' => $user->master_id,
                'master_company_id' => $user->master_company_id,
                'created_by' => $userId,
                'is_change_certified' => 0,
            ]);
        }

        if (isset($hosActivityLog) || isset($exist)) {
            return response()->json([
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data updated successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'failure',
            'statusCode' => 500,
            'message' => 'Failed to save data',
        ], 500);
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
