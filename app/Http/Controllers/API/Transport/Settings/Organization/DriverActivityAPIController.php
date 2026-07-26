<?php

namespace App\Http\Controllers\API\Transport\Settings\Organization;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Device;
use App\Models\Vehicle;
use App\Models\UserInfo;
use App\Models\ListOption;
use App\Models\RuleAssign;
use Illuminate\Http\Request;
use App\Models\DriverShiftLog;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class DriverActivityAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $auth  = Auth::check();

        if ($auth) {

            $userId = Auth::user()->id;

            $vehicleId = Vehicle::where('created_by', $userId)
                ->pluck('id')
                ->toArray();

            $data['driverShift'] = DriverShiftLog::whereIn('vehicle_id', $vehicleId)
                ->where('is_unidentified', 0)
                ->with('user', 'vehicle', 'option', 'logOption', 'vehicleChange')
                ->orderBy('start_log_time', 'DESC')
                ->get();

            // Return response with status 400 and a message
            return response()->json($data, 200);
        } else {
            return response()->json([], 200);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $data['driver'] = User::where('user_type', 'U')->where('master_id', Auth::user()->id)->get();
        $data['vechile'] = Vehicle::where('created_by', Auth::user()->id)->get();
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

        $latestLog = DriverShiftLog::where('driver_id', $request->driver_id)
            ->where('is_add_approved', 1)
            ->latest('start_log_time')
            ->first();

        $rule_ids = RuleAssign::where('user_id', $request->driver_id)
            ->pluck('rule_id'); // Get an array of rule_ids from RuleAssign

        $userInfo = UserInfo::where('user_id', $request->driver_id)->first();

        $timeZone = $userInfo->home_terminal_timezone;

        $currentTime = Carbon::now()->setTimezone($timeZone)->toDateTimeLocalString();

        $currentTime = Carbon::parse($currentTime);

        $currentUnixTime = $currentTime->copy()->timestamp;

        $locationName = null;

        $key = config('app.Map_key');  // Fetch the Google Maps API key

        $device = Device::where('vehicle_id', $request->vehicle_id)->first();

        $locationName = get_driver_activity_location($device, $key, $currentTime);

        $odometer = get_driver_activity_odometer($device, $currentTime);

        $engineHour = get_driver_activity_rpm($device, $currentTime);

        $startData = shift_cycle_start_check($latestLog, $currentTime, $locationName, $rule_ids, 0);

        $shift_start = 0;
        $cycle_start = 0;

        if (count($startData) > 0) {
            $shift_start = $startData[0];
            $cycle_start = $startData[1];
        }

        DriverShiftLog::create([
            'shift_start' => $shift_start,
            'cycle_start' => $cycle_start,
            'created_by' => Auth::user()->id,
            'created_at' => $currentTime,
            'driver_id' => $request->driver_id,
            'vehicle_id' => $request->vehicle_id,
            'is_add_approved' => 0,
            'log_type' => 4,
            'accepted' => 3,
            'location_name' => $locationName,
            'odometer' => $odometer,
            'engineHour' => $engineHour,
            'message_reason' => $request->message_reason,
            'current_shift_status' => $request->driver_status,
            'start_log_time' => Carbon::parse($currentTime),
            'start_log_time_unix' => $currentUnixTime
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

        $driverActivity = DriverShiftLog::find($id);

        $userInfo = UserInfo::where('user_id', $driverActivity->driver_id)->first();

        if ($userInfo) {
            $timeZone = $userInfo->home_terminal_timezone;

            if (($timeZone)) {
                $currentTime = Carbon::now()->setTimezone($timeZone)->toDateTimeLocalString();
            }
        } else {
            $user = User::find($driverActivity->driver_id);
            $timeZone = $user->timezone;
            // Fallback time zone or handle error
            $currentTime = Carbon::now()->setTimezone($timeZone)->toDateTimeLocalString();
        }

        $latestLog = DriverShiftLog::where('driver_id', $driverActivity->driver_id)->latest('start_log_time')->first();

        $latestId = $latestLog->id;

        // Get the current date-time in the specified time zone
        $currentDateTime = Carbon::now($timeZone)->toDateTimeLocalString();
        $data['current'] = $currentDateTime;
        $data['log'] = DriverShiftLog::with('user', 'vehicle', 'option', 'logOption', 'vehicleChange')->find($id);
        $data['logData'] = check_log_driver_exist($driverActivity->driver_id, $driverActivity->start_log_time, (($latestId == $id ? $currentTime : $driverActivity->end_log_time)),  $id);
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

        $driverActivity = DriverShiftLog::where('id', $id)
            ->with('device')
            ->first();

        $userInfo = UserInfo::where('user_id', $driverActivity->driver_id)->first();

        if ($userInfo) {
            $timeZone = $userInfo->home_terminal_timezone;

            if (($timeZone)) {
                $currentTime = Carbon::now()->setTimezone($timeZone)->toDateTimeLocalString();
            }
        } else {
            $user = User::find($driverActivity->driver_id);
            $timeZone = $user->timezone;
            // Fallback time zone or handle error
            $currentTime = Carbon::now()->setTimezone($timeZone)->toDateTimeLocalString();
        }

        $currentTime = Carbon::parse($currentTime);

        $latestLog = DriverShiftLog::where('driver_id', $request->driver_id)
            ->where('is_add_approved', 1)
            ->latest('start_log_time')
            ->first();

        $latestId = $latestLog->id;

        $exist = check_log_driver_exist($request->driver_id, $request->start_time, (($latestId == $id ? $currentTime : $request->end_time)), $id);

        if ($exist['status']) {
            return response()->json("User is violating driving time", 404);
        }

        // Get the current date-time in the specified time zone
        $currentDateTime = $currentTime;

        if ($driverActivity) {

            $currentVehicleId = $driverActivity->vehicle_id;

            $device = $driverActivity->device;

            $currentDriverStatus = $driverActivity->current_shift_status;

            $currentMessageReason = $driverActivity->message_reason;

            $currentId = $driverActivity->id;

            $currentStartTime = Carbon::parse($driverActivity->start_log_time);

            $currentEndTime = $latestId == $currentId ? null : Carbon::parse($driverActivity->end_log_time);

            $currentNotes = $driverActivity->notes;

            $currentLocation = $driverActivity->location_name;

            $odometer = $driverActivity->odometer;
            $odometerEnd = $driverActivity->odometer_end;

            $vehicleIdChange = $request->vehicle_id;

            $driverStatusChange = $request->driver_status;

            $messageReasonChange = $request->message_reason;

            $startTimeChange = Carbon::parse($request->start_time);

            $endTimeChange = (($latestId == $id ? null : Carbon::parse($request->end_time)));

            $odometerChange = get_driver_activity_odometer($device, $startTimeChange);

            $odometerEndChange = get_driver_activity_odometer($device, $endTimeChange);

            $engineHour = get_driver_activity_rpm($device, $endTimeChange);

            $noteChange = $request->notes;

            $locationNameChange = $request->location_name;

            if (
                ($currentVehicleId != $vehicleIdChange)
                ||
                ($currentDriverStatus != $driverStatusChange)
                ||
                ($currentMessageReason != $messageReasonChange)
                ||
                ($currentNotes != $noteChange)
                ||
                ($currentLocation != $locationNameChange)
                ||
                ($startTimeChange != $currentStartTime)
                ||
                ($currentEndTime != $endTimeChange)
                ||
                ($odometer != $odometerChange)
                ||
                ($odometerEnd != $odometerEndChange)
            ) {

                $driverActivity->update([
                    'driver_id' => $request->driver_id,
                    'vehicle_id_change' => $request->vehicle_id,
                    'current_shift_status_change' => $request->driver_status,
                    'message_reason_change' => $request->message_reason,
                    'start_log_time_change' => $request->start_time,
                    'end_log_time_change' => (($latestId == $id ? null : $request->end_time)),
                    'notes_change' => $request->notes,
                    'location_name_change' => $request->location_name,
                    'odometer_change' => $odometerChange,
                    'odometer_end_change'  => $odometerEndChange,
                    'engineHour' => $engineHour,
                    'is_edit_approved' => 0,
                    'log_type' => 5,
                    'accepted' => 3,
                    'updated_by' => Auth::user()->id,
                    'created_at' => Carbon::parse($currentDateTime),
                    'updated_at' => Carbon::parse($currentDateTime),
                ]);
            } else {
                $driverActivity->update([
                    'updated_by' => Auth::user()->id,
                    'created_at' => Carbon::parse($currentDateTime),
                    'updated_at' => Carbon::parse($currentDateTime),
                ]);
            }

            return response()->json("Update driver activity");
        } else {
            return response()->json("Driver log does not exist", 404);
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
                $currentDateTime = Carbon::parse()->setTimezone($timeZone)->toDateTimeLocalString();

                $currentDateTime = Carbon::parse($currentDateTime);

                // Fetch all driver shift logs excluding the current one
                $driverActivities = DriverShiftLog::where('driver_id', $driver_id)
                    ->where('current_shift_status', 3)
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

    public function transport_latest_log_change($userId, $id)
    {

        $latestLog = DriverShiftLog::where('driver_id', $userId)->latest('start_log_time')->first();

        if (!$latestLog) {
            return response()->json(['message' => 'Latest log does not exist'], 302);
        }

        $latestLog->update(['current_shift_status' => $id]);

        return response()->json(['message' => 'Saved successfully'], 200);
    }

    public function Check_edit_driver_activity($id)
    {

        $driverActivity = DriverShiftLog::find($id);

        if (!$driverActivity) {
            return response()->json(false);
        }

        $edit = $driverActivity->is_edit;

        return response()->json($edit == 1);
    }


    public function log_data($logType, $type, $pageNo, $itemNo): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([], 200);
        }

        $userId = Auth::id();

        $vehicleIds = Vehicle::where('created_by', $userId)
            ->pluck('id')
            ->toArray();

        $logDataQuery = DriverShiftLog::whereIn('vehicle_id', $vehicleIds)
            ->where('is_unidentified', 0)
            ->with(['user', 'vehicle', 'option', 'logOption', 'vehicleChange']);

        if (!is_null($logType) && $logType !== 'null') {
            if ((int)$logType === 1) {
                $logDataQuery->where('system_entry', 1);
            } else {
                $logDataQuery->where('log_type', $logType);
            }
        }

        if (!is_null($type) && $type !== 'null') {
            $logDataQuery->where('accepted', $type);
        }

        $logData = $logDataQuery
            ->orderBy('start_log_time', 'DESC')
            ->paginate($itemNo, ['*'], 'page', $pageNo);

        return response()->json([
            'driverShift' => $logData,
        ], 200);
    }
}
