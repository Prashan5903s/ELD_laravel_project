<?php

namespace App\Http\Controllers\API\Driver\DriverActivity;

use Carbon\Carbon;
use App\Models\Device;
use App\Models\UserInfo;
use App\Models\ListOption;
use App\Models\RuleAssign;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\VehicleAssign;
use App\Models\DriverShiftLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class DriverActivityInfoAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function paginate(Request $request, $pageNo = 1, $itemNo = 10): JsonResponse
    {
        $user = Auth::user()->id;

        $driverShift = DriverShiftLog::with('user', 'vehicle', 'option')
            ->where('is_add_approved', 1)
            ->where('driver_id', $user)
            ->orderBy('start_log_time', 'DESC')
            ->paginate($itemNo, ['*'], 'page', $pageNo);

        return response()->json([
            'driverShift' => $driverShift->items(),
            'total' => $driverShift->total(),
            'currentPage' => $driverShift->currentPage(),
            'lastPage' => $driverShift->lastPage(),
        ]);
    }

    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userID = Auth::user()->id;

        $data['vehicle'] = VehicleAssign::with('vehicle')
            ->where('driver_id', $userID)
            ->get();

        $data['listOption'] = ListOption::getOptions("driving_status", [], "1");

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

        $auth = Auth::check();

        $cycle_start = 0;
        $shift_start = 0;

        $key = config('app.Map_key');  // Fetch the Google Maps API key

        $locationName = null;

        if ($auth) {

            $driver_id = Auth::user()->id;

            $currentTime = get_current_time_driver($driver_id);
            $currentTime = Carbon::parse($currentTime);

            $rule_ids = RuleAssign::where('user_id', $driver_id)
                ->pluck('rule_id'); // Get an array of rule_ids from RuleAssign

            $latestLog = DriverShiftLog::where('driver_id', $driver_id)
                ->where('is_add_approved', 1)
                ->latest('start_log_time')
                ->first();

            $device = Device::where('vehicle_id', $request->vehicle_id)->first();

            $locationName = get_driver_activity_location($device, $key, $currentTime);

            $odometer = get_driver_activity_odometer($device, $currentTime);

            $engineHour = get_driver_activity_rpm($device, $currentTime);

            $updatedLatestLog = DriverShiftLog::create([
                'created_at' => $currentTime,
                'start_log_time' => $currentTime,
                'start_log_time_unix' => Carbon::parse($currentTime)->timestamp,
                'driver_id' => $driver_id,
                'vehicle_id' => $request->vehicle_id,
                'location_name' => $locationName,
                'odometer' => $odometer,
                'engineHour' => $engineHour,
                'current_shift_status' => $request->driver_status,
                'message_reason' => $request->message_reason,
                'log_type' => 4,
                'accepted' => 1,
                'is_add_approved' => 1,
                'is_edit_approved' => 1,
                'is_edit' => 1,
                'created_by' => Auth::user()->id,
            ]);

            if ($latestLog) {

                $latestLog->update([
                    'end_log_time' => $currentTime,
                    'end_log_time_unix' => Carbon::parse($currentTime)->timestamp,
                    'location_end' => $locationName,
                    'odometer_end' => $odometer,
                ]);
            }

            $startData = shift_cycle_start_check($updatedLatestLog, $currentTime, $locationName, $rule_ids, 0);

            if (count($startData) > 0) {
                $shift_start = $startData[0];
                $cycle_start = $startData[1];
            }

            if ($updatedLatestLog) {

                $updatedLatestLog->update([
                    'shift_start' => $shift_start,
                    'cycle_start' => $cycle_start,
                ]);
            }

            return response()->json([
                'status' => "Success",
                'statusCode' => 200,
                'message' => 'Saved successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => "Failure",
                'statusCode' => 401,
                'message' => 'User not authenticated'
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
        $driverActivity = DriverShiftLog::find($id);

        // Get the current date-time in the specified time zone
        $currentDateTime = get_current_time_driver($driverActivity->driver_id);
        $data['current'] = $currentDateTime;
        $data['log'] = DriverShiftLog::with('user', 'vehicle', 'option')->find($id);
        $currentDate = Carbon::parse($currentDateTime)->format('Y-m-d');
        $endLogDate = Carbon::parse($data['log']->end_log_time)->format('Y-m-d');
        $data['checkCurrentDay'] = ($endLogDate === $currentDate);
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

        $auth = Auth::check();

        $key = config('app.Map_key');  // Fetch the Google Maps API key

        $cycle_start = 0;
        $shift_start = 0;

        if ($auth) {

            $driver_id = Auth::user()->id;

            $rule_ids = RuleAssign::where('user_id', $driver_id)
                ->pluck('rule_id'); // Get an array of rule_ids from RuleAssign

            // Get the current date-time in the specified time zone
            $currentDateTime = get_current_time_driver($driver_id);
            $currentTime = Carbon::parse($currentDateTime);

            $driverActivity = DriverShiftLog::find($id);

            $latestLog = DriverShiftLog::where('driver_id', $driver_id)
                ->where('is_add_approved', 1)
                ->latest('start_log_time')
                ->first();

            $create = $request->start_time;
            $last = $currentTime;

            $lastFinalUnixTime = null;
            $lastTimeFinal = null;

            if ($latestLog) {

                $latestId = $latestLog->id;
                $last = (($latestId == $id ? $currentTime : $request->end_time));

                $lastFinalUnixTime = ($latestId == $id ? null : Carbon::parse($last)->timestamp);
                $lastTimeFinal = (($latestId == $id ? null : $last));
            } else {

                $last = $currentTime;

                $lastFinalUnixTime = null;
                $lastTimeFinal = null;
            }

            $device = Device::where('vehicle_id', $request->vehicle_id)->first();

            $locationName = get_driver_activity_location($device, $key, $create);

            $engineHour = get_driver_activity_rpm($device, $create);

            $create = Carbon::parse($create);

            $last = Carbon::parse($last);

            $odometer = get_driver_activity_odometer($device, $create);
            $odometerEnd = get_driver_activity_odometer($device, $last);

            $exist = check_log_driver_exist(
                $driver_id,
                $create,
                $last,
                $id
            );

            if ($exist['status']) {
                return response()->json([
                    'status' => 'Success',
                    'statusCode' => 403,
                    'message' => 'Driving log exist'
                ], 403);
            }

            $changes = driver_log_time_data_edit(
                $driver_id,
                $create,
                $last,
                $currentTime,
                $id
            );

            if ($changes) {

                if ($driverActivity) {

                    $driverActivity->update([
                        'driver_id' => $driver_id,
                        'vehicle_id' => $request->vehicle_id,
                        'current_shift_status' => $request->driver_status,
                        'start_log_time' => $create,
                        'odometer' => $odometer,
                        'odometer_end' => $odometerEnd,
                        'engineHour' => $engineHour,
                        'location_name' => $request->location_name,
                        'notes' => $request->notes,
                        'start_log_time_unix' => Carbon::parse($create)->timestamp,
                        'end_log_time' => $lastTimeFinal,
                        'end_log_time_unix' => $lastFinalUnixTime,
                        'message_reason' => $request->message_reason,
                        'updated_by' => Auth::user()->id,
                        'log_type' => 5,
                        'accepted' => 1,
                        'is_add_approved' => 1,
                        'is_edit_approved' => 1,
                        'created_at' => $currentTime,
                        'updated_at' => $currentTime,
                    ]);
                } else {
                    return response()->json([
                        'status' => "Failure",
                        "statusCode" => 404,
                        "message" => "Log does not exist"
                    ], status: 404);
                }

                $startData = shift_cycle_start_check($driverActivity, $currentTime, $locationName, $rule_ids, 0);

                if (count($startData) > 0) {
                    $shift_start = $startData[0];
                    $cycle_start = $startData[1];
                }

                $driverActivity->update([
                    'shift_start' => $shift_start,
                    'cycle_start' => $cycle_start
                ]);
            }

            return response()->json([
                'status' => "Success",
                "statusCode" => 200,
                "message" => "Data saved successfully"
            ], status: 200);
        } else {

            return response()->json([
                'status' => "Failure",
                "statusCode" => 401,
                "message" => "User not authenticated"
            ], status: 401);
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

    public function check_time_driver_shift(Request $request, $id, $start, $end)
    {
        $activity = DriverShiftLog::find($id);

        // Check if the activity exists
        if ($activity) {

            $driver_id = $activity->driver_id;

            // Check if the driver_id is valid
            if ($driver_id) {

                $userInfo = UserInfo::where('user_id', $driver_id)->first();

                $timeZone = $userInfo->home_terminal_timezone;

                // Get the current date-time in the specified time zone
                $currentDateTime = Carbon::now($timeZone)->toDateTimeLocalString();

                $currentDateTime = Carbon::parse($currentDateTime);

                // Fetch all driver shift logs excluding the current one
                $driverActivities = DriverShiftLog::where('driver_id', $driver_id)
                    ->where('system_entry', 1)
                    ->where('id', '!=', $id)
                    ->get();

                // Loop through the driver activities to check for overlaps
                foreach ($driverActivities as $driverActivity) {

                    $startLogTime = $driverActivity->start_log_time;
                    $endLogTime = $driverActivity->end_log_time;

                    $endLogTime = $endLogTime ? $endLogTime : $currentDateTime;

                    // Check if the given start and end times overlap with the existing log
                    if (($start > $startLogTime && $end < $endLogTime)) {
                        return response()->json(['status' => 1, 'for' => 'a']);
                    }
                    if (
                        $start > $startLogTime && $start < $endLogTime
                    ) {
                        // If overlap exists, return 1
                        return response()->json(['status' => 1, 'for' => 'b']);
                    }
                    if ($end > $startLogTime && $end < $endLogTime) {
                        return response()->json(['status' => 1, 'for' => 'c']);
                    }
                }
            }
        }

        // If no overlap exists, return 0
        return response()->json(['status' => 0, 'for' => 'd']);
    }

    public function change_driver_duty_status($id)
    {

        $cycle_start = 0;
        $shift_start = 0;

        $key = config('app.Map_key');  // Fetch the Google Maps API key

        // Check if the user is authenticated
        if (Auth::check()) {

            $user = Auth::user();

            $driverId = $user->id;

            // Find the master user
            $master = User::find($user->master_id);

            // Check if the user is of type 'U' and their master is of type 'TR'
            if ($user->user_type == 'U' && $master && $master->user_type == 'TR') {

                $currentTime = get_current_time_driver($driverId);

                $currentTime = Carbon::parse($currentTime);

                $latestLog = DriverShiftLog::where('driver_id', $driverId)
                    ->where('is_add_approved', 1)
                    ->latest('start_log_time')
                    ->first();

                if ($latestLog) {

                    $messageReason = $latestLog->message_reason;

                    $latestEndLogTime = $latestLog->end_log_time;

                    $rule_ids = RuleAssign::where('user_id', $driverId)
                        ->pluck('rule_id'); // Get an array of rule_ids from RuleAssign

                    $locationName = null;

                    $vehicleId = $latestLog->vehicle_id;

                    $device = Device::where('vehicle_id', $vehicleId)->first();

                    $locationName = get_driver_activity_location($device, $key, $currentTime);

                    $engineHour = get_driver_activity_rpm($device, $currentTime);

                    $odometer = get_driver_activity_odometer($device, $currentTime);

                    if (!is_null($latestEndLogTime)) {

                        if (Carbon::parse($latestEndLogTime)->ne($currentTime)) {

                            $updatedBtwLog = DriverShiftLog::create([
                                'created_at' => $currentTime,
                                'start_log_time' => $latestEndLogTime,
                                'start_log_time_unix' => Carbon::parse($latestEndLogTime)->timestamp,
                                "end_log_time" => Carbon::parse($currentTime),
                                'end_log_time_unix' => Carbon::parse($currentTime)->timestamp,
                                'driver_id' => $driverId,
                                'vehicle_id' => $vehicleId,
                                'location_name' => $locationName,
                                'odometer' => $odometer,
                                'engineHour' => $engineHour,
                                'current_shift_status' => 1,
                                'message_reason' => $messageReason,
                                'is_add_approved' => 1,
                                'is_edit_approved' => 1,
                                'is_edit' => 1,
                                'created_by' => Auth::user()->id,
                            ]);

                            $startBtwData = shift_cycle_start_check($updatedBtwLog, $currentTime, $locationName, $rule_ids, 0);

                            $shift_btw_start = 0;
                            $cycle_btw_start = 0;

                            if (count($startBtwData) > 0) {
                                $shift_btw_start = $startBtwData[0];
                                $cycle_btw_start = $startBtwData[1];
                            }

                            $updatedBtwLog->update([
                                'shift_start' => $shift_btw_start,
                                'cycle_start' => $cycle_btw_start,
                            ]);

                        }
                    }

                    $updatedLatestLog = DriverShiftLog::create([
                        'created_at' => $currentTime,
                        'start_log_time' => $currentTime,
                        'start_log_time_unix' => Carbon::parse($currentTime)->timestamp,
                        'driver_id' => $driverId,
                        'vehicle_id' => $vehicleId,
                        'location_name' => $locationName,
                        'odometer' => $odometer,
                        'engineHour' => $engineHour,
                        'current_shift_status' => $id,
                        'message_reason' => $messageReason,
                        'is_add_approved' => 1,
                        'is_edit_approved' => 1,
                        'is_edit' => 1,
                        'created_by' => Auth::user()->id,
                    ]);

                    $latestLogEndTIme = is_null($latestEndLogTime) ? Carbon::parse($currentTime) : Carbon::parse($latestEndLogTime);

                    if (!Carbon::parse($latestLogEndTIme)->ne($currentTime)) {

                        $latestLog->update([
                            'end_log_time' => $currentTime,
                            'end_log_time_unix' => Carbon::parse($currentTime)->timestamp,
                            'location_end' => $locationName,
                            'odometer_end' => $odometer,
                            'engineHour' => $engineHour
                        ]);


                    }

                    $startData = shift_cycle_start_check($updatedLatestLog, $currentTime, $locationName, $rule_ids, 0);

                    if (count($startData) > 0) {
                        $shift_start = $startData[0];
                        $cycle_start = $startData[1];
                    }

                    $updatedLatestLog->update([
                        'shift_start' => $shift_start,
                        'cycle_start' => $cycle_start,
                    ]);

                    return response()->json([
                        'status' => "success",
                        'statusCode' => 200,
                        'message' => 'Saved successfully'
                    ], 200);
                } else {

                    return response()->json([
                        'status' => "Failure",
                        "statusCode" => 404,
                        "message" => "Latest log does not exist"
                    ], 404);
                }
            } else {

                return response()->json([
                    'status' => 'failure',
                    'statusCode' => 401,
                    'message' => "Unauthorized user"
                ], 401);
            }
        } else {

            return response()->json([
                'status' => 'failure',
                'statusCode' => 401,
                'message' => "Not authenticated"
            ], 401);
        }
    }
}
