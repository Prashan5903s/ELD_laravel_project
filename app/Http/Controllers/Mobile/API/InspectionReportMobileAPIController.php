<?php

namespace App\Http\Controllers\Mobile\API;

use Carbon\Carbon;
use App\Models\UserInfo;
use App\Models\Inspection;
use App\Models\ListOption;
use Illuminate\Http\Request;
use App\Models\InspectionLog;
use App\Models\VehicleAssign;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\UploadedFile; // Add this import

class InspectionReportMobileAPIController extends Controller
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

            $data = [
                'status' => 'failure',
                'code' => 401,
                'message' => 'Not authenticated',
            ];

            return response()->json($data, 401);
        }

        $user = Auth::user();

        $userId = $user->id;

        $inspection = Inspection::where("created_by", $userId)->select('id', 'vehicle_id', 'inspection_type', 'inspection_date_time')->with(['typeInspection:option_id,title', 'vehicle:id,name', 'inspectionLog:inspection_id,parts_id,is_ok,defect_type,notes,image_url', 'inspectionLog.defect:option_id,title', 'inspectionLog.parts:option_id,title'])->get();

        $data = [
            'status' => 'success',
            'code' => 200,
            'message' => 'Inspection data fetched successfully',
            'data' => $inspection,
        ];

        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $auth = Auth::check();

        if (!$auth) {

            $data = [
                'status' => 'failure',
                'code' => 401,
                'message' => 'Not authenticated',
            ];

            return response()->json($data, 401);
        }

        $user = Auth::user();

        $userId = $user->id;

        // Fetch user's timezone
        $userInfo = UserInfo::where('user_id', $userId)->first();
        $timezone = $userInfo->home_terminal_timezone ?? $user->timezone;

        // Get the current time in the user's timezone
        $currentTime = Carbon::now()->setTimezone($timezone)->toDateTimeLocalString();
        $currentTime = Carbon::parse($currentTime);

        $logData['start_time'] = $currentTime;

        $logData['vehicle'] = VehicleAssign::where('driver_id', $userId)->select('driver_id', 'vechile_id')->with('vehicle:id,name,make,model,year', 'driver:id,first_name,last_name,email,mobile_no,pin_code,address,timezone', 'userInfo:user_id,driver_id,licenseNumber,username,note,career_name,main_office_address,carrer_us_dot_number')->get();

        $logData['parts_type'] = ListOption::where('list_id', 'parts_type')->select('option_id', 'title')->orderBy('title', 'ASC')->get();

        $logData['defect_type'] = ListOption::where('list_id', 'defect_type')->select('option_id', 'title')->get();

        $logData['inspection_type'] = ListOption::where('list_id', 'inspection_type')->select('option_id', 'title')->get();

        $logData['is_ok'] = [
            'OK' => 1,
            'NOT OK' => 2,
        ];

        $data = [
            'status' => 'success',
            'code' => 200,
            'message' => 'Data fetched successfully!',
            'data' => $logData,
        ];

        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'inspection_type' => 'required',
            'vehicle_id' => 'required|integer',
            'inspection_start_time' => 'required',
            'parts_data' => 'required|array',
            'parts_data.*.parts_id' => 'required|integer',
            'parts_data.*.is_ok' => 'required',
            'parts_data.*.defect_type' => 'nullable',
            'parts_data.*.notes' => 'nullable|string',
            'parts_data.*.image_url' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Return validation errors if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $userId = $user->id;

        // Fetch user's timezone
        $userInfo = UserInfo::where('user_id', $userId)->first();
        $timezone = $userInfo->home_terminal_timezone ?? $user->timezone;

        // Get the current time in the user's timezone
        $currentTime = Carbon::now()->setTimezone($timezone)->toDateTimeLocalString();
        $currentTime = Carbon::parse($currentTime);

        try {
            // Create the inspection
            $inspection = Inspection::create([
                'inspection_type' => $request->inspection_type,
                'vehicle_id' => $request->vehicle_id,
                'inspection_date_time' => $currentTime,
                'inspection_start_time' => Carbon::parse($request->inspection_start_time),
                'inspection_end_time' => $currentTime,
                'master_id' => $user->master_id,
                'master_company_id' => $user->master_company_id,
                'created_by' => $userId,
            ]);

            // Process parts_data
            foreach ($request->parts_data as $data) {
                $inspectionLogData = [
                    'inspection_id' => $inspection->id,
                    'parts_id' => $data['parts_id'],
                    'is_ok' => $data['is_ok'],
                    'master_id' => $user->master_id,
                    'master_company_id' => $user->master_company_id,
                    'created_by' => $userId,
                ];

                // Add defect_type and notes if they exist
                if (!empty($data['defect_type'])) {
                    $inspectionLogData['defect_type'] = $data['defect_type'];
                }

                if (!empty($data['notes'])) {
                    $inspectionLogData['notes'] = $data['notes'];
                }

                // Handle image upload
                if (!empty($data['image_url']) && $data['image_url'] instanceof UploadedFile) {
                    $imageName = time() . '_' . $data['image_url']->getClientOriginalName();
                    $data['image_url']->move(public_path('inspection'), $imageName);
                    $inspectionLogData['image_url'] = $imageName;
                }

                // Create the inspection log
                InspectionLog::create($inspectionLogData);
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Inspection created successfully',
            ], 201);
        } catch (\Exception $e) {
            // Log the error and return a response
            Log::error('Error creating inspection: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while creating the inspection',
                'error' => $e->getMessage(),
            ], 500);
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
        $auth = Auth::check();

        if (!$auth) {

            $data = [
                'status' => 'failure',
                'code' => 401,
                'message' => 'Not authenticated',
            ];

            return response()->json($data, 401);
        }


        $user = Auth::user();

        $userId = $user->id;

        $inspection = Inspection::where('id', $id)->where("created_by", $userId)->select('id', 'inspection_type', 'vehicle_id', 'inspection_date_time')->with(['typeInspection:option_id,title', 'vehicle:id,name', 'inspectionLog:inspection_id,parts_id,is_ok,defect_type,notes,image_url', 'inspectionLog.defect:option_id,title', 'inspectionLog.parts:option_id,title'])->get();

        $data = [
            'status' => 'success',
            'code' => 200,
            'message' => 'Data fetched successfully',
            'data' => $inspection,
        ];

        return response()->json($data, 200);
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
        // Validate the request
        $validator = Validator::make($request->all(), [
            'inspection_type' => 'required',
            'vehicle_id' => 'required|integer',
            'parts_data' => 'required|array',
            'parts_data.*.parts_id' => 'required|integer',
            'parts_data.*.is_ok' => 'required',
            'parts_data.*.defect_type' => 'nullable',
            'parts_data.*.notes' => 'nullable|string',
            'parts_data.*.image_url' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Ensure the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $userId = $user->id;

        // Get the user's timezone
        $userInfo = UserInfo::where('user_id', $userId)->first();
        $timezone = $userInfo->home_terminal_timezone ?? $user->timezone;

        $currentTime = Carbon::now()->setTimezone($timezone)->toDateTimeLocalString();

        // Find the inspection
        $inspection = Inspection::find($id);

        if (!$inspection) {
            return response()->json(['message' => 'Inspection not found'], 404);
        }

        try {
            // Update the inspection
            $inspection->update([
                'inspection_type' => $request->inspection_type,
                'vehicle_id' => $request->vehicle_id,
                'inspection_date_time' => $currentTime,
                'master_id' => $user->master_id,
                'master_company_id' => $user->master_company_id,
                'updated_by' => $userId,
            ]);

            // Clear old inspection logs
            InspectionLog::where('inspection_id', $id)->delete();

            // Process parts_data
            foreach ($request->parts_data as $data) {
                $inspectionLogData = [
                    'inspection_id' => $inspection->id,
                    'parts_id' => $data['parts_id'],
                    'is_ok' => $data['is_ok'],
                    'master_id' => $user->master_id,
                    'master_company_id' => $user->master_company_id,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ];

                // Optional fields
                if (!empty($data['defect_type'])) {
                    $inspectionLogData['defect_type'] = $data['defect_type'];
                }
                if (!empty($data['notes'])) {
                    $inspectionLogData['notes'] = $data['notes'];
                }

                // Handle image upload
                if (!empty($data['image_url']) && $data['image_url'] instanceof UploadedFile) {
                    $imageName = time() . '_' . $data['image_url']->getClientOriginalName();
                    $data['image_url']->move(public_path('inspection'), $imageName);
                    $inspectionLogData['image_url'] = $imageName;
                }

                // Save inspection log
                InspectionLog::create($inspectionLogData);
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Inspection updated successfully',
            ], 200);
        } catch (\Exception $e) {
            // Log error with stack trace
            Log::error('Error updating inspection', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while updating the inspection',
                'error' => $e->getMessage(),
            ], 500);
        }
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
