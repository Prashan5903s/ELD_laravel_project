<?php
namespace App\Http\Controllers\API\Driver\Inspection;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Models\InspectionLog;
use App\Models\ListOption;
use App\Models\UserInfo;
use App\Models\VehicleAssign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

// Add this import

class InspectionReportDriverAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $auth = Auth::check();

        if (! $auth) {

            $data = [
                'status'  => 'failure',
                'code'    => 401,
                'message' => 'Not authenticated',
            ];

            return response()->json($data, 401);
        }

        $user = Auth::user();

        $userId = $user->id;

        $inspection = Inspection::where("created_by", $userId)->select('id', 'vehicle_id', 'inspection_type', 'inspection_date_time')->with(['typeInspection:option_id,title', 'vehicle:id,name', 'inspectionLog:inspection_id,parts_id,is_ok,defect_type,notes,image_url', 'inspectionLog.defect:option_id,title', 'inspectionLog.parts:option_id,title'])->get();

        $data = [
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Inspection data fetched successfully',
            'data'    => $inspection,
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

        if (! $auth) {

            $data = [
                'status'  => 'failure',
                'code'    => 401,
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
            'OK'     => 1,
            'NOT OK' => 2,
        ];

        $data = [
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data fetched successfully!',
            'data'    => $logData,
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
            'inspection_type'          => 'required|integer',
            'vehicle'                  => 'required|integer',
            'start_time'               => 'required|date',
            'parts_type'               => 'required|array',
            'parts_type.*.value'       => 'required|integer', // value is is_ok
            'parts_type.*.defect_type' => 'nullable|integer',
            'parts_type.*.notes'       => 'nullable|string',
            'parts_type.*.image'       => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Return validation errors if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Check if the user is authenticated
        if (! Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user   = Auth::user();
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
                'inspection_type'       => $request->inspection_type,
                'vehicle_id'            => $request->vehicle,
                'inspection_date_time'  => $currentTime,
                'inspection_start_time' => Carbon::parse($request->start_time),
                'inspection_end_time'   => $currentTime,
                'master_id'             => $user->master_id,
                'master_company_id'     => $user->master_company_id,
                'created_by'            => $userId,
            ]);

            $keys = array_keys($request->parts_type);

            foreach ($keys as $key) {

                $data = $request->parts_type[$key];

                $isOk = $data['value'];

                $inspectionLogData = [
                    'inspection_id'     => $inspection->id,
                    'parts_id'          => $key,
                    'is_ok'             => $isOk,
                    'master_id'         => $user->master_id,
                    'master_company_id' => $user->master_company_id,
                    'created_by'        => $userId,
                ];

                if ($isOk == 2) {

                    // Add defect_type and notes if they exist
                    if (! empty($data['defect_type'])) {
                        $inspectionLogData['defect_type'] = $data['defect_type'];
                    }

                    if (! empty($data['notes'])) {
                        $inspectionLogData['notes'] = $data['notes'];
                    }

                    // Handle image upload
                    if (! empty($data['image']) && $data['image'] instanceof UploadedFile) {
                        $imageName = time() . '_' . $data['image']->getClientOriginalName();
                        $data['image']->move(public_path('inspection'), $imageName);
                        $inspectionLogData['image_url'] = $imageName;
                    }

                }

                // Create the inspection log
                InspectionLog::create($inspectionLogData);
            }

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Inspection created successfully',
            ], 200);
        } catch (\Exception $e) {
            // Log the error and return a response
            Log::error('Error creating inspection: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while creating the inspection',
                'error'   => $e->getMessage(),
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

        if (! $auth) {

            $data = [
                'status'  => 'failure',
                'code'    => 401,
                'message' => 'Not authenticated',
            ];

            return response()->json($data, 401);
        }

        $user = Auth::user();

        $userId = $user->id;

        $inspection = Inspection::where('id', $id)
            ->where('created_by', $userId)
            ->select('id', 'inspection_type', 'vehicle_id', 'inspection_date_time')
            ->with([
                'typeInspection:option_id,title',
                'vehicle:id,name',
                'inspectionLog:id,inspection_id,parts_id,is_ok,defect_type,notes,image_url',
                'inspectionLog.defect:option_id,title',
                'inspectionLog.parts:option_id,title',
            ])
            ->first();

        $data = [
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Data fetched successfully',
            'data'    => $inspection,
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

    public function update_data(Request $request, $id)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'inspection_type'          => 'required|integer',
            'vehicle'                  => 'required|integer',
            'start_time'               => 'required|date',
            'parts_type'               => 'required|array',
            'parts_type.*.value'       => 'required|integer', // value is is_ok
            'parts_type.*.defect_type' => 'nullable|integer',
            'parts_type.*.notes'       => 'nullable|string',
            // 'parts_type.*.image'       => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        if (! Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user   = Auth::user();
        $userId = $user->id;

        $userInfo = UserInfo::where('user_id', $userId)->first();
        $timezone = $userInfo->home_terminal_timezone ?? $user->timezone;

        $currentTime = Carbon::now()->setTimezone($timezone)->toDateTimeLocalString();

        $inspection = Inspection::find($id);

        if (! $inspection) {
            return response()->json(['message' => 'Inspection not found'], 404);
        }

        try {
            $inspection->update([
                'inspection_type'      => $request->inspection_type,
                'vehicle_id'           => $request->vehicle,
                'inspection_date_time' => $currentTime,
                'master_id'            => $user->master_id,
                'master_company_id'    => $user->master_company_id,
                'updated_by'           => $userId,
            ]);

            $keys = array_keys($request->parts_type);

            // Delete logs that are no longer present
            InspectionLog::where("inspection_id", $id)->whereNotIn('parts_id', $keys)->delete();

            foreach ($keys as $key) {
                $data = $request->parts_type[$key];
                $isOk = $data['value'];

                // Common data
                $inspectionLogData = [
                    'inspection_id'     => $inspection->id,
                    'parts_id'          => $key,
                    'is_ok'             => $isOk,
                    'master_id'         => $user->master_id,
                    'master_company_id' => $user->master_company_id,
                    'created_by'        => $userId,
                ];

                // Additional data if is_ok == 2 (Not OK)
                if ($isOk == 2) {
                    if (! empty($data['defect_type'])) {
                        $inspectionLogData['defect_type'] = $data['defect_type'];
                    }

                    if (! empty($data['notes'])) {
                        $inspectionLogData['notes'] = $data['notes'];
                    }

                    if (! empty($data['image']) && $data['image'] instanceof UploadedFile) {
                        $imageName = time() . '_' . $data['image']->getClientOriginalName();
                        $data['image']->move(public_path('inspection'), $imageName);
                        $inspectionLogData['image_url'] = $imageName;
                    }
                }

                $presentLog = InspectionLog::where('parts_id', $key)->where('inspection_id', $id)->first();

                if ($presentLog) {
                    $presentLog->update($inspectionLogData);
                } else {
                    InspectionLog::create($inspectionLogData);
                }
            }

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Inspection updated successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error updating inspection', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while updating the inspection',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

}
