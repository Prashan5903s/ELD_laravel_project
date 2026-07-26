<?php

namespace App\Http\Controllers\API\Transport\Settings\Organization;

use Carbon\Carbon;
use App\Models\Device;
use App\Models\Vehicle;
use App\Models\RuleAssign;
use Illuminate\Http\Request;
use App\Models\DriverShiftLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UnidentifiedDrivingAPIController extends Controller
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

            $vehicleId = Vehicle::where('created_by', $userId)
                ->pluck('id')
                ->toArray();

            $data['driverLog'] = DriverShiftLog::whereIn('vehicle_id', $vehicleId)
                ->where('is_unidentified', 1)
                ->orderBy('start_log_time_unix', 'ASC')
                ->with('option', 'vehicle', 'vehicleAssign', 'vehicleAssign.driver')
                ->select('id', 'vehicle_id', 'current_shift_status', 'start_log_time')
                ->get();

            return response()->json($data, 200);
        } else {
            return response()->json([
                'status' => "Failure",
                'statusCode' => 403,
                'message' => 'Not authenticated'
            ], 403);
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
        //
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

        $auth = Auth::check();

        if ($auth) {

            $userId = $request->driver_id;

            $locationName = null;

            $key = config('app.Map_key');  // Fetch the Google Maps API key

            $latestLog = DriverShiftLog::where('driver_id', $userId)
                ->where('is_add_approved', 1)
                ->latest('start_log_time')
                ->first();

            $rule_ids = RuleAssign::where('user_id', $userId)
                ->pluck('rule_id'); // Get an array of rule_ids from RuleAssign

            $driverLog = DriverShiftLog::where('id', $id)
                ->where('is_unidentified', 1)
                ->first();

            if (!$driverLog) {
                return response()->json([
                    'status' => "Failure",
                    'statusCode' => 404,
                    'message' => 'Driver log does not exist'
                ], 404);
            }

            $driverLogStartTime = $driverLog->start_log_time;

            $driverLogStartTime = Carbon::parse($driverLogStartTime);

            $driverLogEndTime = $driverLog->end_log_time;

            $currentTime = get_current_time_driver($userId);

            $currentTime = Carbon::parse($currentTime);

            $logEndTime = ($driverLogEndTime == null || $driverLogEndTime == 'null') ? $currentTime : Carbon::parse($driverLogEndTime);

            $exist = check_log_driver_exist(
                $userId,
                $driverLogStartTime,
                $logEndTime,
                $id
            );

            if ($exist['status']) {
                return response()->json([
                    'status' => "Failure",
                    'statusCode' => 404,
                    'message' => 'Driving log exist in this time'
                ], 404);
            }

            $device = Device::where('vehicle_id', $request->vehicle_id)->first();

            $locationName = get_driver_activity_location($device, $key, $driverLogStartTime);

            $locationEndName = get_driver_activity_location($device, $key, $driverLogEndTime);

            $odometer = get_driver_activity_odometer($device, $driverLogStartTime);

            $odometerEnd = get_driver_activity_odometer($device, $driverLogEndTime);

            $engineHour = get_driver_activity_rpm($device, $driverLogStartTime);

            $startData = shift_cycle_start_check($latestLog, $currentTime, $locationName, $rule_ids, 0);

            $shift_start = 0;
            $cycle_start = 0;

            if (count($startData) > 0) {
                $shift_start = $startData[0];
                $cycle_start = $startData[1];
            }


            $driverLog->update([
                'driver_id' => $request->driver_id,
                'is_unidentified' => 0,
                'is_add_approved' => 0,
                'log_type' => 6,
                'location_name' => $locationName,
                'location_end' => $locationEndName,
                'odometer' => $odometer,
                'odometer_end' => $odometerEnd,
                'engineHour' => $engineHour,
                'shift_start' => $shift_start,
                'cycle_start' => $cycle_start,
                'is_edit' => 0,
            ]);

            return response()->json([
                'status' => "Success",
                'statusCode' => 200,
                'message' => "Data saved successfully!"
            ], 200);
        } else {
            return response()->json([
                'status' => "Failure",
                'statusCode' => 403,
                'message' => 'User not authenticated'
            ]);
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
