<?php
namespace App\Http\Controllers\Mobile\API;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DriverShiftLog;
use App\Models\RuleAssign;
use App\Models\User;
use App\Models\UserInfo;
use Carbon\Carbon;
use App\Models\VehicleAssign;
use App\Models\Vehicle;
use App\Models\VehicleLogHistory;
use App\Models\BluetoothLogData;
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

        $key = config('app.Map_key');  // Fetch the Google Maps API key

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
                        'message' => $validator->errors()->first('message_reason')
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

                    $locationName = null;

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
                        "created_by" => $driverId
                    ]);

                    return response()->json([
                        'status' => "success",
                        'statusCode' => 200,
                        'message' => 'Saved successfully'
                    ], 200);

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

    public function hos_mobile_data($start, $end)
    {

        $auth = Auth::check();

        $datas = [];

        if ($auth) {

            $user = Auth::user();

            $id = $user->id;

            $datas = hos_date_data($id, $start, $end);

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

            $startTime = Carbon::parse($date)->startOfDay();

            $endTime = Carbon::parse($date)->endOfDay();

            $userInfo = UserInfo::where('user_id', $id)->first();

            $timezone = $userInfo->home_terminal_timezone;

            $currentTime = Carbon::parse()->setTimezone($timezone)->toDateTimeLocalString();

            $currentTime = Carbon::parse($currentTime);

            $datas = graph_hos_chart($id, $startTime, $endTime, $currentTime);

            if ($datas && count($datas) >= 3) {

                $finalData['graph_data'] = $datas[0];

                $distinctVehicle = [];

                if (!empty($finalData['graph_data']) && count($finalData['graph_data']) > 0) {

                    foreach ($finalData['graph_data'] as $veh) {

                        $name = $veh[5];

                        // Check if name already exists in the array
                        $exists = collect($distinctVehicle)->contains('name', $name);

                        if (!$exists) {

                            $distinctVehicle[] = ['name' => $name];
                        }
                    }
                }

                $finalData['vehicle'] = $distinctVehicle;

                $finalData['violation_data'] = $datas[2];

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

        $key = config('app.Map_key');  // Fetch the Google Maps API key

        // Check if the user is authenticated
        if (Auth::check()) {

            try {

                $request->validate([
                    'shift_id' => 'required|string|max:255',
                    'text' => 'required|string',
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
                        "created_by" => $driverId
                    ]);

                    return response()->json([
                        'status' => "success",
                        'statusCode' => 200,
                        'message' => 'Saved successfully'
                    ], 200);

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

// <?php
// namespace App\Http\Controllers\Mobile\API;

// use App\Http\Controllers\Controller;
// use App\Models\Device;
// use App\Models\DriverShiftLog;
// use App\Models\RuleAssign;
// use App\Models\User;
// use App\Models\UserInfo;
// use Carbon\Carbon;
// use App\Models\VehicleAssign;
// use App\Models\Vehicle;
// use App\Models\VehicleLogHistory;
// use App\Models\BluetoothLogData;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Validation\ValidationException;

// class HOSMobileAPIController extends Controller
// {



//     public function hos_mobile_data($start, $end)
//     {

//         $auth = Auth::check();

//         $datas = [];

//         if ($auth) {

//             $user = Auth::user();

//             $id = $user->id;

//             $datas = hos_date_data($id, $start, $end);

//             $data = [
//                 'status' => 'success',
//                 'statusCode' => 200,
//                 'message' => 'Data fetched successfully',
//                 'log_data' => $datas,
//             ];
//         } else {

//             $data = [
//                 'status' => 'success',
//                 'statusCode' => 401,
//                 'message' => 'Not authenticated',
//             ];
//         }

//         return response()->json($data, $data['statusCode']);
//     }
//     public function hos_mobile_test_data($start, $end)
//     {

//         $auth = Auth::check();

//         $datas = [];

//         if ($auth) {

//             $user = Auth::user();

//             $id = $user->id;

//             $datas = hos_date_data_test($id, $start, $end);

//             $data = [
//                 'status' => 'success',
//                 'statusCode' => 200,
//                 'message' => 'Data fetched successfully',
//                 'log_data' => $datas,
//             ];
//         } else {

//             $data = [
//                 'status' => 'success',
//                 'statusCode' => 401,
//                 'message' => 'Not authenticated',
//             ];
//         }

//         return response()->json($data, $data['statusCode']);
//     }

//     public function graph_hos_chart_data($date)
//     {

//         $auth = Auth::check();

//         if ($auth) {

//             $user = Auth::user();

//             $id = $user->id;

//             $startTime = Carbon::parse($date)->startOfDay();

//             $endTime = Carbon::parse($date)->endOfDay();

//             $userInfo = UserInfo::where('user_id', $id)->first();

//             $timezone = $userInfo->home_terminal_timezone;

//             $currentTime = Carbon::parse()->setTimezone($timezone)->toDateTimeLocalString();

//             $currentTime = Carbon::parse($currentTime);

//             $datas = graph_hos_chart($id, $startTime, $endTime, $currentTime);

//             if ($datas && count($datas) >= 3) {

//                 $finalData['graph_data'] = $datas[0];

//                 $distinctVehicle = [];

//                 if (!empty($finalData['graph_data']) && count($finalData['graph_data']) > 0) {

//                     foreach ($finalData['graph_data'] as $veh) {

//                         $name = $veh[5];

//                         // Check if name already exists in the array
//                         $exists = collect($distinctVehicle)->contains('name', $name);

//                         if (!$exists) {

//                             $distinctVehicle[] = ['name' => $name];
//                         }
//                     }
//                 }

//                 $finalData['vehicle'] = $distinctVehicle;

//                 $finalData['violation_data'] = $datas[2];

//                 $data = [
//                     'status' => 'success',
//                     'statusCode' => 200,
//                     'message' => 'Data fetched successfully',
//                     'data' => $finalData,
//                 ];
//             } else {

//                 $data = [
//                     'status' => 'failure',
//                     'statusCode' => 403,
//                     'message' => 'Data does not exist',
//                 ];
//             }
//         } else {

//             $data = [
//                 'status' => 'success',
//                 'statusCode' => 401,
//                 'message' => 'Not authenticated',
//             ];
//         }

//         return response()->json($data, $data['statusCode']);
//     }
// }
