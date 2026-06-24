<?php

namespace App\Http\Controllers\Mobile\API;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\DriverShiftLog;
use App\Models\HOSUnsignedLog;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class HOSUnsignedLogMobileAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $auth = Auth::check();

        if (!$auth) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'Not authenticated',
            ], 401);
        }

        $user = Auth::user();

        if ($user) {
            $userId = $user->id;
            $unsignedLog = HOSUnsignedLog::where('user_id', $userId)->where('is_certify', 0)->select('id', 'user_id', 'timeData', 'signature', 'is_certify')->get();
            $data = [
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data fetched successfully',
                'data' => $unsignedLog,
            ];
        } else {
            $data = [
                'status' => 'failure',
                'statusCode' => 403,
                'message' => 'User does not exist',
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
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'Not authenticated',
            ], 401);
        }

        try {
            // Validate incoming request
            $request->validate([
                'id' => 'required|integer',
                'signature' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
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

        if ($user) {
            $userId = $user->id;

            // Fetch the unsigned log record
            $unsignedLog = HOSUnsignedLog::where('id', $request->id)
                ->where('user_id', $userId)
                ->where('is_certify', 0)
                ->first();

            if (!$unsignedLog) {
                return response()->json([
                    'status' => 'failure',
                    'statusCode' => 403,
                    'message' => 'Unassigned log does not exist',
                ], 403);
            }

            $imageName = time() . '_' . $request->signature->getClientOriginalName();
            $request->signature->move(public_path('signature'), $imageName);

            // Update the unsigned log record
            $unsignedLog->update([
                'signature' => $imageName,
                'is_certify' => 1,
                'updated_by' => $userId,
            ]);

            // Refresh the model to get the latest data
            $unsignedLog->refresh();

            return response()->json([
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data updated successfully',
                'data' => $unsignedLog,
            ], 200);
        }

        return response()->json([
            'status' => 'failure',
            'statusCode' => 403,
            'message' => 'User does not exist',
        ], 403);
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
    public function update(Request $request, $id) {}

    public function hos_edit_certify(Request $request, $date)
    {
        if (! Auth::check()) {
            return response()->json([
                'status'     => 'failure',
                'statusCode' => 401,
                'message'    => 'Not authenticated',
            ], 401);
        }

        try {
            // Validate incoming request
            $request->validate([
                'signature' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status'     => 'failure',
                'statusCode' => 422,
                'message'    => 'Validation failed',
                'errors'     => $e->errors(),
            ], 422);
        }

        $user = Auth::user();

        $userId = $user->id;

        $unsignedLog = HOSUnsignedLog::where('timeData', $request->id)
            ->where('user_id', $userId)
            ->first();

        $imageName = time() . "_" . $request->file('signature')->getClientOriginalName();
        $request->file('signature')->move(public_path('signature'), $imageName);

        if (! $unsignedLog) {
            HOSUnsignedLog::create([
                'created_by' => $userId,
                'user_id'    => $userId,
                'timeData'   => $date,
                'signature'  => $imageName,
                'master_id'  => $user->master_id,
                'master_company_id'  => $user->master_company_id,
                'is_certify' => 1,
            ]);
        } else {
            $unsignedLog->update([
                'signature'  => $imageName,
                'updated_by' => $userId,
                'is_certify' => 1,
            ]);
        }

        return response()->json([
            'status'     => 'success',
            'statusCode' => 200,
            'message'    => 'Log shift edit approved',
        ], 200);
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
