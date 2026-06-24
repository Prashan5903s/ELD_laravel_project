<?php

namespace App\Http\Controllers\Mobile\API;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Models\DriverShiftLog;
use App\Models\UserInfo;
use App\Models\RuleAssign;
use Illuminate\Support\Facades\DB;
use App\Models\BluetoothLogData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class BluetoothAPIController extends Controller
{

    public function create(Request $request)
    {
        $driver = Auth::user();

        try {

            $request->validate([
                'start_log_time' => 'required|date',
                'end_log_time' => 'required|date|after:start_log_time',
                'vin' => 'required',
                'odometer' => 'required',
                'speed' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'engineHours' => 'required',
                "request_json" => "required",
            ]);

            DB::beginTransaction();

            $vehicle = Vehicle::where("vin", $request->vin)->first();

            if (!$vehicle) {

                return response()->json([
                    "status" => "failure",
                    "statusCode" => 404,
                    "message" => "Vin does not exist"
                ], 404);
            }

            $driverId = $driver->id;
            $vehicleId = $vehicle->id;

            $startLogTime = Carbon::parse($request->start_log_time);
            $endLogTime = Carbon::parse($request->end_log_time);

            $startLogTimeUnix = $startLogTime->timestamp;
            $endLogTimeUnix = $endLogTime->timestamp;

            $userInfo = UserInfo::where("user_id", $driverId)->first();
            $driverTimeZone = $userInfo->home_terminal_timezone;

            $currentTime = Carbon::parse()->setTimezone($driverTimeZone)->toDateTimeLocalString();
            $currentTime = Carbon::parse($currentTime);

            $rule_ids = RuleAssign::where('user_id', $driverId)->pluck('rule_id');

            bluetooth_log_add($driverId, $startLogTime, $endLogTime, $currentTime);

            $currentShift = $request->speed >= 5 ? 3 : 1;
            $locationName = fetchFullAddressName($request->latitude, $request->longitude);

            $logCreate = DriverShiftLog::create([
                "driver_id" => $driverId,
                "vehicle_id" => $vehicleId,
                "shift_changed_time" => $currentTime,
                "start_log_time" => $startLogTime,
                "end_log_time" => $endLogTime,
                "start_log_time_unix" => $startLogTimeUnix,
                "end_log_time_unix" => $endLogTimeUnix,
                "current_shift_status" => $currentShift,
                "location_name" => $locationName,
                "location_end" => $locationName,
                "engineHour" => $request->engineHours,
                "odometer" => $request->odometer,
                "odometer_end" => $request->odometer,
                "system_entry" => 1,
                "log_type" => 1,
                "is_active" => 1,
                "is_edit" => 1,
                "accepted" => 1,
            ]);

            $shiftData = shift_cycle_start_check($logCreate, $currentTime, $locationName, $rule_ids, 0);

            $shiftStart = $shiftData[0];
            $cycleStart = $shiftData[1];

            $logCreate->update([
                "shift_start" => $shiftStart,
                "cycle_start" => $cycleStart,
            ]);

            BluetoothLogData::create([
    "driver_id" => $driverId,
    "vehicle_id" => $vehicleId,
    "log_data" => json_encode($request->all()),
    "request_json" => json_encode($request->request_json),
    "ip" => $request->ip(),
    "created_by" => $driverId,
]);

            DB::commit();

            return response()->json(
                [
                    "status" => "success",
                    "statusCode" => 200,
                    "message" => "Bluetooth log data inserted successfully",
                    "data" => $logCreate,
                ],
                200
            );

        } catch (ValidationException $th) {

            DB::rollBack();

            return response()->json([
                'status' => 'failure',
                'statusCode' => 422,
                'message' => 'Validation failed',
                'errors' => $th->errors(),
            ], 422);
        }
    }

}
