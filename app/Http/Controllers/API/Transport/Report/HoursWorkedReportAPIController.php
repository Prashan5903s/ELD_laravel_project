<?php

namespace App\Http\Controllers\API\Transport\Report;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Device;
use Carbon\CarbonPeriod;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use App\Models\DriverShiftLog;
use App\Http\Controllers\Controller;
use App\Models\VehicleLogHistory;
use Illuminate\Support\Facades\Auth;

class HoursWorkedReportAPIController extends Controller
{
    public function index($start, $end, $driver)
    {

        $user = Auth::user();

        $driverData = [];

        $totalTime = 0;

        $totalDistance = 0;

        if ($driver == null || $driver == 'null') {

            $driver = User::where('master_id', $user->id)->where('user_type', 'U')->pluck('id')->toArray();
        } else {

            $driver = explode(',', $driver);
        }

        $start = Carbon::parse($start)->startOfDay();

        $end = Carbon::parse($end)->endOfDay();

        $period = CarbonPeriod::create($start, $end);

        // Loop through the period and collect the dates
        $dates = [];

        foreach ($period as $date) {
            $dates[] = $date->toDateString(); // You can use other formats like toDateTimeString()
        }

        foreach ($driver as $driverId) {

            $drTime = 0;

            $drDist = 0;

            $totalData = [];

            $driverInfo = User::find($driverId);

            $name = $driverInfo->first_name . " " . $driverInfo->last_name;

            $currentTime = getDriverCurrentTime($driverId);

            $currentTime = Carbon::parse($currentTime);

            foreach ($dates as $time) {

                $driverTime = 0;

                $driverDist = 0;

                $create = Carbon::parse($time)->startOfDay();

                $last = Carbon::parse($time)->endOfDay();

                $logRow = DriverShiftLog::where('driver_id', $driverId)
                    ->where('is_add_approved', 1)
                    ->whereNotIn('current_shift_status', [1, 2, 5])
                    ->where(function ($query) use ($create, $last, $currentTime) {
                        $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                            $subQuery
                                // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
                                ->where(function ($overlapQuery) use ($create, $last, $currentTime) {
                                    $overlapQuery->where('start_log_time', '<=', $last)
                                        ->whereRaw('IFNULL(end_log_time, ?) >= ?', [$currentTime, $create]);
                                })
                                // Exclude cases where $create equals end_log_time or $last equals start_log_time
                                ->whereRaw('IFNULL(end_log_time, ?) != ?', [$currentTime, $create])
                                ->whereRaw('? != start_log_time', [$last]);
                        });
                    })
                    ->orderBy('start_log_time', 'asc')
                    ->get();

                foreach ($logRow as $log) {

                    $device = Device::where('vehicle_id', $log->vehicle_id)->first();

                    $timeData = create_end_time($log, $create, $log, $last, $currentTime);

                    $timeStart = Carbon::parse($timeData[0]);

                    $timeEnd = Carbon::parse($timeData[1]);

                    $timeDiff = $timeEnd->diffInSeconds($timeStart);

                    $driverTime += $timeDiff;

                    $drTime += $timeDiff;

                    $totalTime += $timeDiff;

                    $identifier = $device->serial_number;

                    $vehicleLog = VehicleLogHistory::where('identifier', $identifier)
                        ->whereBetween('event_date_time', [$timeStart, $timeEnd])
                        ->orderBy('event_date_time', 'ASC')
                        ->get();

                    $previousOdometer = null;

                    foreach ($vehicleLog as $logData) {
                        if ($previousOdometer !== null) {
                            $odometerDiff = $logData->odometer - $previousOdometer;

                            if ($odometerDiff > 0) {
                                $driverDist += $odometerDiff;
                                $drDist += $odometerDiff;
                                $totalDistance += $odometerDiff;
                            }
                        }
                        $previousOdometer = $logData->odometer;
                    }
                }

                $totalData[] = [
                    'time_date' => $create,
                    'total_time' => secondsToTime($driverTime),
                    'total_distance' => $driverDist
                ];
            }

            $driverData[$name][] = [$totalData, secondsToTime($drTime), $drDist];
        }

        return response()->json([$driverData, secondsToTime($totalTime), $totalDistance]);
    }
}
