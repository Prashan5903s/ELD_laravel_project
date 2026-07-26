<?php

namespace App\Http\Controllers\Mobile\API;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Device;
use App\Models\UserInfo;
use App\Models\ListOption;
use Illuminate\Http\Request;
use App\Models\DriverShiftLog;
use App\Models\VehicleLogHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SafetyMobileAPIController extends Controller
{
    public function safety_dashboard_page()
    {

        $auth = Auth::check();

        $percentWeight = ListOption::where('list_id', 'safety_event_percent')->orderBy('seq', 'ASC')->get();

        // Retrieve event percentage points
        $eventPoints = [];
        foreach ([1 => 'speeding', 2 => 'hardAccel', 3 => 'hardBrake', 4 => 'hardStop', 5 => 'hardTurn'] as $id => $key) {
            $eventPoints[$key] = intval(optional($percentWeight->where('option_id', $id)->first())->short_name ?? 0);
        }

        // Get weights for different violation types
        $weights = [
            'speeding' => config('app.weight_speeding', 1),
            'hardAccel' => config('app.weight_hard_accel', 1),
            'hardBrake' => config('app.weight_hard_braking', 1),
            'hardStop' => config('app.weight_hard_stop', 1),
            'hardTurn' => config('app.weight_hard_turn', 1),
        ];

        $spdPercent = $eventPoints['speeding'];
        $HAPercent = $eventPoints['hardAccel'];
        $HBPercent = $eventPoints['hardBrake'];
        $HSPercent = $eventPoints['hardStop'];
        $HTPercent = $eventPoints['hardTurn'];

        $spdPoints = $weights['speeding'];

        $HAPoints = $weights['hardAccel'];

        $HBPoints = $weights['hardBrake'];

        $HSPoints = $weights['hardStop'];

        $HTPoints = $weights['hardTurn'];

        if ($auth) {

            $user = Auth::user();

            $userId = $user->id;

            $driverName = $user->first_name . " " . $user->last_name;

            $now = Carbon::now();
            $latestSunday = $now->previous(Carbon::SUNDAY);
            $elevenWeeksBefore = $latestSunday->copy()->subWeeks(11); // Use copy() to avoid modifying $latestSunday

            $create = $latestSunday;

            $last = $elevenWeeksBefore;

            $userInfo = UserInfo::where('user_id', $userId)->first();

            $timezone = $userInfo->home_terminal_timezone;

            $currentTime = Carbon::parse()->setTimezone($timezone)->toDateTimeLocalString();

            $currentTime = Carbon::parse($currentTime);

            $LogsData = DriverShiftLog::where('driver_id', $userId)
                ->where('is_add_approved', 1)
                ->where(function ($query) use ($create, $last, $currentTime) {
                    $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                        $subQuery->where(function ($q) use ($create, $last, $currentTime) {
                            $q->whereBetween('start_log_time', [$create, $last])
                                ->whereRaw('? != start_log_time', [$last])
                                ->whereRaw('IFNULL(end_log_time, ?) != ?', [$currentTime, $create])
                                ->orWhere(function ($query) use ($create, $last, $currentTime) {
                                    $query->whereRaw('IFNULL(end_log_time, ?) BETWEEN ? AND ?', [$currentTime, $create, $last])
                                        ->whereRaw('? != start_log_time', [$last])
                                        ->whereRaw('IFNULL(end_log_time, ?) != ?', [$currentTime, $create]);
                                });
                        });
                    });
                })
                ->select('id', 'driver_id', 'vehicle_id', 'current_shift_status', 'start_log_time', 'end_log_time')
                ->with('driver:id,first_name,last_name', 'vehicle:id,name', 'vehicle.devices:id,vehicle_id,serial_number', 'driver.userInfo:id,user_id,driver_id,home_terminal_timezone')
                ->orderBy('start_log_time', 'ASC')
                ->get();

            $data = calculation_of_safety_events($LogsData, $userId, $create, $last, $currentTime, $driverName);

            $eventPerMiles = event_per_driver_miles($userId, $create, $last);

            $eventSafetyScore = calculating_event_safety_score_factor($userId, $create, $last);

            $driverData = $data[$userId][$driverName];

            $countHA =  $driverData['hardAccelCount'];
            $countHB = $driverData['hardBrakeCount'];
            $countHS = $driverData['hardStopCount'];
            $countHT = $driverData['hardTurnCount'];
            $countSPD = $driverData['speedingCount'];
            $countNOExist = $driverData['NoEventCount'];
            $totalDriveMile = $driverData['total_miles'];

            $countNOExist = $countNOExist / 5;

            $impactHA = $countHA * $HAPoints;
            $impactHB = $countHB * $HBPoints;
            $impactHS = $countHS * $HSPoints;
            $impactHT = $countHT * $HTPoints;
            $impactSPD = $countSPD * $spdPoints;

            $earnedHA = $countNOExist * $HAPoints;
            $earnedHB = $countNOExist * $HBPoints;
            $earnedHS = $countNOExist * $HSPoints;
            $earnedHT = $countNOExist * $HTPoints;
            $earnedSPD = $countNOExist * $spdPoints;

            $impactHA =  $impactHA * ($HAPercent / 100);
            $impactHB = $impactHB * ($HBPercent / 100);
            $impactHS = $impactHS * ($HSPercent / 100);
            $impactHT = $impactHT * ($HTPercent / 100);
            $impactSPD  = $impactSPD * ($spdPercent / 100);

            $earnedHA =  $earnedHA * ($HAPercent / 100);
            $earnedHB = $earnedHB * ($HBPercent / 100);
            $earnedHS = $earnedHS * ($HSPercent / 100);
            $earnedHT = $earnedHT * ($HTPercent / 100);
            $earnedSPD  = $earnedSPD * ($spdPercent / 100);

            $HAEarned = max(0, min($HAPercent, $HAPercent - $impactHA + $earnedHA));
            $HAEarned = $HAPercent - $HAEarned;

            $HBEarned = max(0, min($HBPercent, $HBPercent - $impactHB + $earnedHB));
            $HBEarned = $HBPercent - $HBEarned;

            $HSEarned = max(0, min($HSPercent, $HSPercent - $impactHS + $earnedHS));
            $HSEarned = $HSPercent - $HSEarned;

            $HTEarned = max(0, min($HTPercent, $HTPercent - $impactHT + $earnedHT));
            $HTEarned = $HTPercent - $HTEarned;

            $SPDEarned = max(0, min($spdPercent, $spdPercent - $impactSPD + $earnedSPD));
            $SPDEarned = $spdPercent - $spdPercent;

            $safetyScore = max(0, min(100, (100 - ($HAEarned + $HBEarned + $HSEarned + $HTEarned + $SPDEarned))));

            $data = [
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Safety data fetched successfully!',
                'safety_score' => $safetyScore,
                'event_per_miles' => $eventPerMiles,
                'driver_total_drive' => $totalDriveMile,
                'event_points' => $eventSafetyScore,
            ];
        } else {

            $data = [

                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'Not authenticated',

            ];
        }

        return response()->json($data, $data['statusCode']);
    }
    
    public function safety_event_data($event, $start = null, $end = null)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 403,
                'message' => 'Not authenticated'
            ], 403);
        }

        // Get the authenticated user
        $user = Auth::user();

        $userId = $user->id;

        $logData = [];

        if (!$user) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'User does not exist'
            ], 401);
        }

        $timezone = UserInfo::where('user_id', $userId)->pluck('home_terminal_timezone')->first();
        $currentTime = Carbon::parse($timezone)->setTimezone($timezone)->toDateTimeLocalString();
        $currentTime = Carbon::parse($currentTime);

        // Set date range
        if ($start != null && $end != null) {
            $startDay = Carbon::parse($start)->startOfDay();
            $endDay = Carbon::parse($end)->endOfDay();
        } else {
            // Default to current day if no date range is provided
            $startDay = Carbon::parse($currentTime)->startOfDay();
            $endDay = Carbon::parse($currentTime)->endOfDay();
        }

        $create = $startDay;

        $last = $endDay;

        $driverLog = DriverShiftLog::where('driver_id', $userId)
            ->where('is_add_approved', 1)
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


        foreach ($driverLog as $log) {

            $vehicle = $log->vehicle_id;

            $device = Device::where('vehicle_id', $vehicle)->first();

            $startTime = Carbon::parse($log->start_log_time);

            $endTime = $log->end_log_time;

            $endTime = ($endTime == null || $endTime == 'null') ? $currentTime : Carbon::parse($endTime);

            if ($device) {

                $identifier = $device->serial_number;

                $logs = VehicleLogHistory::select('id', 'message_reason', 'event_date_time', 'location', 'duration', 'speed', 'odometer', 'obd_vin', 'obd_fuel')
                    ->where('identifier', $identifier)
                    ->where('message_reason', $event)
                    ->whereBetween('event_date_time', [$startTime, $endTime])
                    ->get();

                if ($logs && count($logs) > 0) {

                    foreach ($logs as $datas) {

                        $locationName = null;

                        $locationStart = [];

                        if ($datas && isset($datas->location)) {

                            $location = json_decode($datas->location);

                            if (isset($location->GeoLocation->Latitude) && isset($location->GeoLocation->Longitude)) {

                                $latitude = $location->GeoLocation->Latitude;

                                $longitude = $location->GeoLocation->Longitude;

                                $locationStart = [$latitude, $longitude];
                            }
                        }

                        if (count($locationStart) > 0) {
                            $locationName = fetchFullAddressName($locationStart[0], $locationStart[1]);
                        }

                        $logData[] = [

                            'location_name' => $locationName,
                            'reason' => $datas->message_reason,
                            'timeData' => $datas->event_date_time,
                            'speed' => $datas->speed,
                            'duration' => $datas->duration,
                            'odometer' => $datas->odometer,
                            'obd_vin' => $datas->obd_vin,
                            'obd_fuel' => $datas->obd_fuel,
                            'lat_lang_data' => $locationStart,
                            'risk' => getRiskLevel($datas)
                           
                        ];
                    }


                }

            }
        }

        // Return the fetched logs in a successful response
        return response()->json([
            'status' => 'success',
            'statusCode' => 200,
            'message' => 'Data fetched successfully',
            'data' => $logData
        ], 200);
    }
    
}
