<?php
namespace App\Http\Controllers\Mobile\API;

use App\Http\Controllers\Controller;
use App\Models\BluetoothLogData;
use App\Models\CoDriver;
use App\Models\Device;
use App\Models\DriverShiftLog;
use App\Models\RuleAssign;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\Vehicle;
use App\Models\VehicleAssign;
use App\Models\VehicleLogHistory;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class HOSMobileAPIController extends Controller
{

    public function change_mobile_duty_status($id, $lat, $long, $text)
    {

        $cycle_start = 0;
        $shift_start = 0;

        $key = config('app.Map_key'); // Fetch the Google Maps API key

        // Check if the user is authenticated
        if (Auth::check()) {

            $user = Auth::user();

            $driverId = $user->id;

            // Find the master user
            $master = User::find($user->master_id);

            // Check if the user is of type 'U' and their master is of type 'TR'
            if ($user->user_type == 'U' && $master && $master->user_type == 'TR') {

                // Validate the text input
                $validator = Validator::make(
                    ['message_reason' => $text],
                    ['message_reason' => 'required|string|max:255']
                );

                if ($validator->fails()) {

                    return response()->json([
                        'status' => 'failure',
                        'statusCode' => 422,
                        'message' => $validator->errors()->first('message_reason'),
                    ], 422);
                }

                $currentTime = get_current_time_driver($driverId);

                $currentTime = Carbon::parse($currentTime);

                $latestLog = DriverShiftLog::where('driver_id', $driverId)
                    ->where('is_add_approved', 1)
                    ->latest('start_log_time')
                    ->first();

                if ($latestLog) {

                    $latestEndLogTime = $latestLog->end_log_time;

                    $rule_ids = RuleAssign::where('user_id', $driverId)
                        ->pluck('rule_id'); // Get an array of rule_ids from RuleAssign

                    $locationName = fetchFullAddressName($lat, $long);

                    $vehicleId = $latestLog->vehicle_id;

                    $device = Device::where('vehicle_id', $vehicleId)->first();

                    $engineHour = get_driver_activity_rpm($device, $currentTime);

                    $odometer = get_driver_activity_odometer($device, $currentTime);

                    if (!is_null($latestEndLogTime)) {

                        if (Carbon::parse($latestEndLogTime)->ne($currentTime)) {

                            $updatedBtwLog = DriverShiftLog::create([
                                'created_at' => $currentTime,
                                'start_log_time' => $latestEndLogTime,
                                "end_log_time" => Carbon::parse($currentTime),
                                'start_log_time_unix' => Carbon::parse($latestEndLogTime)->timestamp,
                                'end_log_time_unix' => Carbon::parse($currentTime)->timestamp,
                                'driver_id' => $driverId,
                                'vehicle_id' => $vehicleId,
                                'location_name' => $locationName,
                                'odometer' => $odometer,
                                'engineHour' => $engineHour,
                                'current_shift_status' => 1,
                                'message_reason' => $text,
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
                        'message_reason' => $text,
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
                            'engineHour' => $engineHour,
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
                        'message' => 'Saved successfully',
                    ], 200);
                } else {

                    $vehicleId = null;

                    $bluetoothLog = BluetoothLogData::where('driver_id', $driverId)
                        ->latest('created_at')
                        ->first();

                    if ($bluetoothLog) {

                        $vehicleId = $bluetoothLog->vehicle_id;

                    } else {

                        $vehicleIds = VehicleAssign::where('driver_id', $driverId)
                            ->pluck('vechile_id');

                        $deviceIds = Device::whereIn('vehicle_id', $vehicleIds)
                            ->pluck('id');

                        $latestVehLog = VehicleLogHistory::whereIn('device_id', $deviceIds)
                            ->latest('event_date_time')
                            ->first();

                        if ($latestVehLog) {
                            $vehicleId = Device::where('id', $latestVehLog->device_id)
                                ->value('vehicle_id');
                        }
                    }

                    DriverShiftLog::create([
                        "driver_id" => $driverId,
                        "vehicle_id" => $vehicleId,
                        "start_log_time" => $currentTime,
                        "end_log_time" => null,
                        'start_log_time_unix' => Carbon::parse($currentTime)->timestamp,
                        'end_log_time_unix' => null,
                        'message_reason' => $text,
                        'current_shift_status' => $id,
                        "is_add_approved" => 1,
                        'is_edit_approved' => 1,
                        'is_edit' => 1,
                        "is_active" => 1,
                        'shift_start' => 1,
                        'cycle_start' => 1,
                        "created_by" => $driverId,
                    ]);

                    return response()->json([
                        'status' => "success",
                        'statusCode' => 200,
                        'message' => 'Saved successfully',
                    ], 200);

                }
            } else {

                return response()->json([
                    'status' => 'failure',
                    'statusCode' => 401,
                    'message' => "Unauthorized user",
                ], 401);
            }
        } else {

            return response()->json([
                'status' => 'failure',
                'statusCode' => 401,
                'message' => "Not authenticated",
            ], 401);
        }
    }

    public function hos_mobile_data()
    {

        $auth = Auth::check();

        $datas = [];

        if ($auth) {

            $user = Auth::user();

            $id = $user->id;

            $masterId = $user->created_by;

            //Helper function name hos_date_data

            $userDriver = User::where("user_type", "U")
                ->where("id", "!=", $id)
                ->where("master_id", $user->master_id)
                ->select("id", "first_name", "last_name")
                ->get();

            $userInfo = UserInfo::where('user_id', $id)->first();

            $timezone = $userInfo->home_terminal_timezone;

            $currentTime = Carbon::parse()->setTimezone($timezone)->toDateTimeLocalString();

            $currentTime = Carbon::parse($currentTime);

            $end = date('Y-m-d', strtotime($currentTime));
            $start = date('Y-m-d', strtotime($end . ' -7 days'));

            $period = CarbonPeriod::create($start, "1 day", $end);

            $dates = [];

            foreach ($period as $date) {

                $dates[] = $date->format("Y-m-d");
            }

            if ($dates && count($dates) > 0) {

                for ($i = count($dates) - 1; $i >= 0; $i--) {

                    $data = $dates[$i];

                    $coDriver = CoDriver::where("user_id", $id)
                        ->where("codriver_date", $data)
                        ->select("user_id", "codriver_id")
                        ->first();

                    $startDay = Carbon::parse($data)->startOfDay();

                    $endDay = Carbon::parse($data)->endOfDay();

                    $graphData = mobile_graph_hos_log_data($id, $startDay, $endDay, $currentTime, $masterId);

                    $finalData = [];

                    if ($graphData && count($graphData) >= 3) {

                        $finalData['graph_data'] = $graphData[0];

                        $distinctVehicle = [];

                        if (!empty($finalData['graph_data']) && count($finalData['graph_data']) > 0) {

                            foreach ($finalData['graph_data'] as $veh) {

                                $name = $veh["vehicle_name"];

                                // Check if name already exists in the array
                                $exists = collect($distinctVehicle)->contains('name', $name);

                                if (!$exists) {

                                    $distinctVehicle[] = ['name' => $name];
                                }
                            }
                        }

                        $finalData['vehicle'] = $distinctVehicle;
                        $finalData['violation_data'] = $graphData[2];
                        $finalData['total_log_time'] = $graphData[3];
                        $finalData['inspection_exist'] = $graphData[4];
                        $finalData['coDriver_data'] = $coDriver;
                        $finalData['coDriver_list'] = $userDriver;
                        $finalData['distance_diffr'] = $graphData[5];
                        $finalData["odometer"] = $graphData[6];
                        $finalData["start_location"] = $graphData[7];
                        $finalData['location_end'] = $graphData[8];
                        $finalData['engine_hour'] = $graphData[9];

                    }

                    $datas[] = [

                        $data => $finalData

                    ];
                }

            }

            // $datas = hos_date_data($id, $start, $end);

            $data = [
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data fetched successfully',
                'log_data' => $datas,
            ];
        } else {

            $data = [
                'status' => 'success',
                'statusCode' => 401,
                'message' => 'Not authenticated',
            ];
        }

        return response()->json($data, $data['statusCode']);
    }
    public function hos_mobile_test_data($start, $end)
    {

        $auth = Auth::check();

        $datas = [];

        if ($auth) {

            $user = Auth::user();

            $id = $user->id;

            $datas = hos_date_data_test($id, $start, $end);

            $data = [
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data fetched successfully',
                'log_data' => $datas,
            ];
        } else {

            $data = [
                'status' => 'success',
                'statusCode' => 401,
                'message' => 'Not authenticated',
            ];
        }

        return response()->json($data, $data['statusCode']);
    }

    public function graph_hos_chart_data($date)
    {

        $auth = Auth::check();

        if ($auth) {

            $user = Auth::user();

            $id = $user->id;

            $masterId = $user->created_by;

            $startTime = Carbon::parse($date)->startOfDay();

            $endTime = Carbon::parse($date)->endOfDay();

            $userInfo = UserInfo::where('user_id', $id)->first();

            $timezone = $userInfo->home_terminal_timezone;

            $currentTime = Carbon::parse()->setTimezone($timezone)->toDateTimeLocalString();

            $currentTime = Carbon::parse($currentTime);

            $datas = mobile_graph_hos_chart($id, $startTime, $endTime, $currentTime, $masterId);

            if ($datas && count($datas) >= 3) {

                $finalData['graph_data'] = $datas[0];

                $distinctVehicle = [];

                if (!empty($finalData['graph_data']) && count($finalData['graph_data']) > 0) {

                    foreach ($finalData['graph_data'] as $veh) {

                        $name = $veh["vehicle_name"];

                        // Check if name already exists in the array
                        $exists = collect($distinctVehicle)->contains('name', $name);

                        if (!$exists) {

                            $distinctVehicle[] = ['name' => $name];
                        }
                    }
                }

                $finalData['vehicle'] = $distinctVehicle;

                $finalData['violation_data'] = $datas[2];
                $finalData['total_log_time'] = $datas[3];
                $finalData['inspection_exist'] = $datas[4];

                $data = [
                    'status' => 'success',
                    'statusCode' => 200,
                    'message' => 'Data fetched successfully',
                    'data' => $finalData,
                ];
            } else {

                $data = [
                    'status' => 'failure',
                    'statusCode' => 403,
                    'message' => 'Data does not exist',
                ];
            }
        } else {

            $data = [
                'status' => 'success',
                'statusCode' => 401,
                'message' => 'Not authenticated',
            ];
        }

        return response()->json($data, $data['statusCode']);
    }

    public function new_change_mobile_duty_status(Request $request)
    {

        $cycle_start = 0;
        $shift_start = 0;

        $key = config('app.Map_key'); // Fetch the Google Maps API key

        // Check if the user is authenticated
        if (Auth::check()) {

            try {

                $request->validate([
                    'shift_id' => 'required|string|max:255',
                    'text' => 'required|string',
                    'lat' => 'required|numeric',
                    'long' => 'required|numeric',
                ]);

            } catch (ValidationException $e) {

                return response()->json([
                    'status' => 'failure',
                    'statusCode' => 422,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);

            }

            $id = $request->shift_id;
            $text = $request->text;
            $long = $request->long;
            $lat = $request->lat;

            $user = Auth::user();

            $driverId = $user->id;

            // Find the master user
            $master = User::find($user->master_id);

            $locationName = fetchFullAddressName($lat, $long);

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

                    $vehicleId = $latestLog->vehicle_id;

                    $device = Device::where('vehicle_id', $vehicleId)->first();

                    $engineHour = get_driver_activity_rpm($device, $currentTime);

                    $odometer = get_driver_activity_odometer($device, $currentTime);

                    if (!is_null($latestEndLogTime)) {

                        if (Carbon::parse($latestEndLogTime)->ne($currentTime)) {

                            $updatedBtwLog = DriverShiftLog::create([
                                'created_at' => $currentTime,
                                'start_log_time' => $latestEndLogTime,
                                "end_log_time" => Carbon::parse($currentTime),
                                'start_log_time_unix' => Carbon::parse($latestEndLogTime)->timestamp,
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
                        'message_reason' => $text,
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
                            'engineHour' => $engineHour,
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

                    $vehicleData = Vehicle::where('id', $vehicleId)->select('id', 'name')->first();

                    return response()->json([
                        'status' => "success",
                        'statusCode' => 200,
                        'message' => 'Saved successfully',
                        'data' => [
                            'location' => $locationName,
                            'vehicle' => $vehicleData,
                            'start_tme' => $currentTime,
                            'end_time' => null,
                            'shift_id' => $id,
                        ],
                    ], 200);
                } else {

                    $vehicleId = null;

                    $bluetoothLog = BluetoothLogData::where('driver_id', $driverId)
                        ->latest('created_at')
                        ->first();

                    if ($bluetoothLog) {

                        $vehicleId = $bluetoothLog->vehicle_id;

                    } else {

                        $vehicleIds = VehicleAssign::where('driver_id', $driverId)
                            ->pluck('vechile_id');

                        $deviceIds = Device::whereIn('vehicle_id', $vehicleIds)
                            ->pluck('id');

                        $latestVehLog = VehicleLogHistory::whereIn('device_id', $deviceIds)
                            ->latest('event_date_time')
                            ->first();

                        if ($latestVehLog) {
                            $vehicleId = Device::where('id', $latestVehLog->device_id)
                                ->value('vehicle_id');
                        }
                    }

                    DriverShiftLog::create([
                        "driver_id" => $driverId,
                        "vehicle_id" => $vehicleId,
                        "start_log_time" => $currentTime,
                        "end_log_time" => null,
                        'start_log_time_unix' => Carbon::parse($currentTime)->timestamp,
                        'end_log_time_unix' => null,
                        'message_reason' => $text,
                        'current_shift_status' => $id,
                        "is_add_approved" => 1,
                        'is_edit_approved' => 1,
                        'is_edit' => 1,
                        "is_active" => 1,
                        'shift_start' => 1,
                        'cycle_start' => 1,
                        "created_by" => $driverId,
                    ]);

                    $vehicleData = Vehicle::where('id', $vehicleId)->select('id', 'name')->first();

                    return response()->json([
                        'status' => "success",
                        'statusCode' => 200,
                        'message' => 'Saved successfully',
                        'data' => [
                            'location' => $locationName,
                            'vehicle' => $vehicleData,
                            'start_tme' => $currentTime,
                            'end_time' => null,
                            'shift_id' => $id,
                        ],
                    ], 200);

                }
            } else {

                return response()->json([
                    'status' => 'failure',
                    'statusCode' => 401,
                    'message' => "Unauthorized user",
                ], 401);
            }
        } else {

            return response()->json([
                'status' => 'failure',
                'statusCode' => 401,
                'message' => "Not authenticated",
            ], 401);
        }
    }

    public function mobile_hos_duty_status_log_edit_insert(Request $request)
    {

        if (!Auth::check()) {

            return response()->json([
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {

            $validated = $request->validate([

                'log_date' => 'required|date',
                'log_data' => 'required|array|min:1',
                'log_data.*.vehicle_id' => 'required|integer',
                'log_data.*.shift_id' => 'required|integer',
                'log_data.*.log_id' => 'required|integer',
                'log_data.*.edit_start' => 'required|date_format:H:i:s',
                'log_data.*.edit_end' => 'required|date_format:H:i:s',
                'log_data.*.location' => 'required|string|max:255',
                'log_data.*.notes' => 'nullable|string|max:1000',
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                'status' => 'failure',
                'statusCode' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $driverId = Auth::id();
        $logDate = $validated['log_date'];

        $user = Auth::user();
        $masterId = $user->created_by;

        $userInfo = UserInfo::where('user_id', $driverId)->first();

        if (!$userInfo) {

            return response()->json([
                'status' => 'failure',
                'statusCode' => 404,
                'message' => 'User information not found.'
            ], 404);
        }

        $timezone = $userInfo->home_terminal_timezone ?? config('app.timezone');
        $currentTime = Carbon::now($timezone);

        $startTime = Carbon::parse($request->log_date)->startOfDay();

        $endTime = Carbon::parse($request->log_date)->endOfDay();

        foreach ($validated['log_data'] as $log) {

            $vehicleId = $log['vehicle_id'];
            $shiftId = $log['shift_id'];
            $location = $log['location'];
            $notes = $log['notes'] ?? null;

            $logStartTime = Carbon::parse($logDate . ' ' . $log['edit_start'], $timezone);
            $logEndTime = Carbon::parse($logDate . ' ' . $log['edit_end'], $timezone);

            $exist = check_hos_mobile_log_driver_exist(
                $driverId,
                $logStartTime,
                $logEndTime,
                $currentTime
            );

            if (!$exist['status']) {

                hod_log_mobile_time_data_edit(
                    $driverId,
                    $vehicleId,
                    $shiftId,
                    $logStartTime,
                    $logEndTime,
                    $currentTime,
                    $location,
                    $notes
                );
            }
        }

        $datas = mobile_graph_hos_chart($driverId, $startTime, $endTime, $currentTime, $masterId);
        $finalData['graph_data'] = $datas[0];

        return response()->json([
            'status' => 'success',
            'statusCode' => 200,
            'message' => 'All logs processed successfully.',
            'data' => $finalData
        ]);
    }
}