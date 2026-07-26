<?php

namespace App\Http\Controllers\API\Transport\Report;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Models\VehicleAssign;
use App\Models\VehicleLogHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\Count;

class FuelPerformanceAPIController extends Controller
{
    public function index($start, $end, $driver, $vehicle)
    {
        $start = Carbon::parse($start)->startOfDay();

        $end = Carbon::parse($end)->endOfDay();

        $userId = Auth::user()->id;

        $data = [];

        // Handle driver as null
        if ($driver == 'null') {
            $driver = User::where('master_id', $userId)->where('user_type', 'U')->pluck('id')->toArray();
        } else {
            $driver = explode(',', $driver);
        }

        // Handle vehicle as null
        if ($vehicle == 'null') {
            $vehicle = Vehicle::where('created_by', $userId)->pluck('id')->toArray();
        } else {
            $vehicle = explode(',', $vehicle);
        }

        // Fetch vehicle assignments
        $vehicleAssign = VehicleAssign::whereIn('driver_id', $driver)
            ->whereIn('vechile_id', $vehicle)
            ->select('driver_id', 'vechile_id')
            ->with('device:vehicle_id,serial_number', 'vehicle:id,name', 'driver:id,first_name,last_name')
            ->get();

        if ($vehicleAssign && count($vehicleAssign) > 0) {

            foreach ($vehicleAssign as $assign) {

                $avgMotionMPG = 0;

                $avgIdleMPG = 0;

                $avgMpg = 0;

                $totalTime = 0;

                $totalSpeed = 0;

                $totalIdleFuel = 0;

                $driverId = $assign->driver->id;

                $totalDist = 0;

                $avgSpeed = 0;

                $totalIdleMpg = 0;

                $totalMotionMpg = 0;

                $totalMpg = 0;

                $totalFuel = 0;

                $totalMotionFuel = 0;

                $totalMotionTime = 0;

                $totalIdleTime = 0;

                $totalRpmCount = 0;

                $serialNumber = $assign->device->serial_number;

                $vehicle = $assign->vehicle->name;

                $driverName = $assign->driver->first_name . " " . $assign->driver->last_name;

                $haEventPerMiles = ha_event_per_miles($driverId, $serialNumber, $start, $end);

                $hbEventPerMiles = hb_event_per_miles($driverId, $serialNumber, $start, $end);

                $huEventPerMiles = hu_event_per_miles($driverId, $serialNumber, $start, $end);

                $idleLog = VehicleLogHistory::where('identifier', $serialNumber)
                    ->whereBetween('event_date_time', [$start, $end])
                    ->where(function ($query) {
                        $query->where('message_reason', 'IDLING')
                            ->orWhere('message_reason', 'IDLING_END');
                    })
                    ->select('id', 'identifier', 'message_reason', 'event_date_time', 'location', 'duration', 'created_at', 'obd_mpg', 'obd_speed', 'odometer', 'obd_odometer', 'obd_fuel', 'obd_trip_mpg', 'obd_instant_mpg', 'obd_engine_rpm', 'speed')
                    ->orderBy("event_date_time", "ASC")
                    ->get();

                $totalLog = VehicleLogHistory::where('identifier', $serialNumber)
                    ->whereBetween('event_date_time', [$start, $end])
                    ->select('id', 'identifier', 'message_reason', 'event_date_time', 'location', 'duration', 'created_at', 'obd_mpg', 'obd_speed', 'odometer', 'obd_odometer', 'obd_fuel', 'obd_trip_mpg', 'obd_instant_mpg', 'obd_engine_rpm', 'speed', 'obd_fuel')
                    ->orderBy("event_date_time", "ASC")
                    ->get();

                $over5MPHLog = VehicleLogHistory::where('identifier', $serialNumber)
                    ->whereBetween('event_date_time', [$start, $end])
                    ->whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])
                    ->where('obd_speed', '>', 5)
                    ->where(function ($query) {
                        $query->where('message_reason', '!=', 'IDLING')
                            ->Where('message_reason', '!=', 'IDLING_END');
                    })
                    ->select('id', 'identifier', 'message_reason', 'event_date_time', 'location', 'duration', 'created_at', 'obd_mpg', 'obd_speed', 'odometer', 'obd_odometer', 'obd_fuel', 'obd_trip_mpg', 'obd_instant_mpg', 'obd_engine_rpm', 'speed')
                    ->orderBy("event_date_time", "ASC")
                    ->get();

                $motionLog = VehicleLogHistory::where('identifier', $serialNumber)
                    ->whereBetween('event_date_time', [$start, $end])
                    ->whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])
                    ->where(function ($query) {
                        $query->where('message_reason', '!=', 'IDLING')
                            ->Where('message_reason', '!=', 'IDLING_END');
                    })
                    ->select('id', 'identifier', 'message_reason', 'event_date_time', 'location', 'duration', 'created_at', 'obd_mpg', 'obd_speed', 'odometer', 'obd_odometer', 'obd_fuel', 'obd_trip_mpg', 'obd_instant_mpg', 'obd_engine_rpm', 'speed')
                    ->orderBy("event_date_time", "ASC")
                    ->get();

                if ($idleLog && count($idleLog) > 0) {

                    foreach ($idleLog as $log) {

                        if ($log->obd_speed < 5) {

                            if ($log->obd_instant_mpg != null) {

                                $totalIdleMpg += ($log->obd_instant_mpg) / 100;
                            }

                            $totalIdleTime += $log->duration;

                            $totalIdleFuel += $log->obd_fuel;
                        }
                    }
                }

                if ($totalLog && count($totalLog) > 0) {

                    for ($i = 0; $i < count($totalLog); $i++) {

                        $currentLog = $totalLog[$i];

                        $totalSpeed += $currentLog->obd_speed;

                        $totalFuel += $currentLog->obd_fuel;

                        $totalTime += $currentLog->duration;

                        // Get the next log (index + 1) if it exists
                        $nextLog = ($i + 1 < count($totalLog)) ? $totalLog[$i + 1] : "Undefined";

                        if ($currentLog->obd_instant_mpg != null) {
                            $totalMpg += $currentLog->obd_instant_mpg;
                        }

                        if ($currentLog->obd_engine_rpm > 1700) {
                            $totalRpmCount += 1;
                        }

                        // Optionally process $nextLog if it's not null
                        if ($nextLog != "Undefined") {

                            $totalDist += $nextLog->odometer - $currentLog->odometer;
                        }
                    }
                }

                if ($motionLog && count($motionLog) > 0) {


                    foreach ($motionLog as $log) {

                        if ($log->obd_speed > 5) {

                            if ($log->obd_instant_mpg != null) {

                                $totalMotionMpg += ($log->obd_instant_mpg) / 100;
                            }

                            $totalMotionFuel += $log->obd_fuel;

                            $totalMotionTime += $log->duration;
                        }
                    }
                }

                $avgMotionTime = count($over5MPHLog) == 0 ? 0 : ($totalMotionTime/count($over5MPHLog));

                $avgIdleTime = Count($motionLog) == 0 ? 0 : ($totalIdleTime / count($motionLog));

                $avgIdleFuelPercent = count($idleLog) == 0 ? 0 : ($totalIdleFuel / count($idleLog));

                $percentMotionFuel = count($over5MPHLog) == 0 ? 0 : $totalMotionFuel / count($over5MPHLog);

                $avgPerFuel = count($totalLog) == 0 ? 0 : ($totalFuel / count($totalLog));

                $rpmPercent = count($totalLog) == 0  ? 0 : ($totalRpmCount / count($totalLog)) * 100;

                $avgIdleMPG = count($idleLog) == 0  ? 0 : $totalIdleMpg / count($idleLog);

                $avgMotionMPG = count($motionLog) == 0  ? 0 : $totalMotionMpg / count($motionLog);

                $avgMpg = (count($motionLog) == 0 && count($idleLog) == 0) ? 0 : (($avgIdleMPG * count($idleLog)) + ($avgMotionMPG * count($motionLog))) / (count($motionLog) + count($idleLog));

                $avgSpeed = count($totalLog) == 0  ? 0 : $totalSpeed / count($totalLog);

                $utilizationData = ($totalTime == 0 || $totalMotionTime == 0) ? 0 : (($totalMotionTime / $totalTime) * 100);

                $data[$driverName] = [
                    'vehicleName' => $vehicle,
                    'avg_idle_mpg' => $avgIdleMPG,
                    'avg_motion_mpg' => $avgMotionMPG,
                    'total_motion_time' => $avgMotionTime,
                    'utilization_time' => $utilizationData,
                    'avg_mpg' => $avgMpg,
                    'avg_idle_fuel' => $avgIdleFuelPercent,
                    'total_idle_time' =>  $totalIdleTime,
                    'percent_motion_fuel' => $percentMotionFuel,
                    'rpm_percent' => $rpmPercent,
                    'total_distance' => $totalDist,
                    'avg_speed' => $avgSpeed,
                    'avg_fuel_percent' => $avgPerFuel,
                    'ha_event_per_miles' => $haEventPerMiles,
                    'hb_event_per_miles' => $hbEventPerMiles,
                    'hu_event_per_miles' => $huEventPerMiles
                ];
            }
        }

        return response()->json($data);
    }
}
