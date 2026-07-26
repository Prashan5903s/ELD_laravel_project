<?php

namespace App\Http\Controllers\API\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DriverShiftLog;
use App\Models\Vehicle;
use App\Models\UserInfo;
use App\Models\Device;
use App\Models\VehicleLogHistory;
use App\Models\User;
use App\Models\VehicleAssign;
use Carbon\Carbon;

class ComplianceAPIController extends Controller
{

    public function index(Request $request, $type, $start = null, $end = null)
    {

        $user = Auth::user();

        $userId = $user->id;

        $user = User::find($userId);

        $data = [];

        $startDay = Carbon::parse($start)->startOfDay();
        $endDay = Carbon::parse($end)->endOfDay();

        function convertTimeToSeconds($time)
        {
            list($hours, $minutes, $seconds) = explode(':', $time);
            return $hours * 3600 + $minutes * 60 + $seconds;
        }

        function convertSecondsToTime($seconds)
        {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $seconds = $seconds % 60;

            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        $drivers = User::where('user_type', 'U')->where('master_id', $userId)->get();

        if ($drivers->isEmpty()) {
            return response()->json($data);
        }

        foreach ($drivers as $driver) {

            $assignedDriveSec = 0;

            $totalDriveTimeSec = 0;

            $deviceIds = [];

            $driverId = $driver->id;

            $driverName = $driver->first_name . ' ' . $driver->last_name;

            $userInfo = UserInfo::where('user_id', $driverId)->first();

            $timeZone = $userInfo->home_terminal_timezone;

            $currentTime = Carbon::parse()->setTimezone($timeZone)->toDateTimeLocalString();

            $currentTime = Carbon::parse($currentTime);

            $create = $startDay;

            $last = $endDay;

            $vehicleId = VehicleAssign::where('driver_id', $driverId)->pluck('vechile_id')->toArray();

            if (count($vehicleId) > 0) {
                $device = Device::whereIn('vehicle_id', $vehicleId)->pluck('serial_number')->toArray();
                $deviceIds = $device;
            }

            if (!empty($deviceIds)) {

                foreach ($deviceIds as $deviceData) {

                    $vehicleLogs = VehicleLogHistory::where('identifier', $deviceData)
                        ->whereBetween('event_date_time', [$startDay, $endDay])
                        ->whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])
                        ->orderBy('event_date_time', "ASC")
                        ->get();

                    if ($vehicleLogs->isNotEmpty()) {
                        foreach ($vehicleLogs as $logData) {
                            $logCreate = Carbon::parse($logData->event_date_time);
                            $aboveLatestTime = ($this->getAboveLatestTime($logData, $logData->identifier, $create, $last, $currentTime));
                            $totalDriveTimeSec += $aboveLatestTime->diffInSeconds($logCreate);
                        }
                    }
                }
            }

            $val = check_eld_rules($driverId, $start, $end);

            $shiftTime = $val['total_shift_time'] ?? '00:00:00';
            $cycleTime = $val['total_cycle_time'] ?? '00:00:00';
            $elevenTime = $val['total_drive_shift_time'] ?? '00:00:00';
            $eightTime = $val['total_drive_time'] ?? '00:00:00';

            $shift = $val['Shift_data'];
            $cycle = $val['cycle_data'];
            $eight = $val['eight_hour_break_violation'];
            $eleven = $val['driver_eleven_viol_data'];

            $violation1 = 0;

            $violation2 = 0;

            $violation3 = 0;

            $violation4 = 0;

            if ($shift && count($shift) > 0) {

                foreach ($shift as $data1) {

                    $violation_duration1 = $data1['violation_duration'];

                    $violation1 += convertTimeToSeconds($violation_duration1);
                }
            }


            if ($cycle && count($cycle) > 0) {

                foreach ($cycle as $data2) {

                    $violation_duration2 = ($data2['violation_duration']);

                    $violation2 += convertTimeToSeconds($violation_duration2);
                }
            }

            if ($eight && count($eight) > 0) {

                foreach ($eight as $data3) {

                    $break_violation = $data3['break_violation'];

                    $violation3 = convertTimeToSeconds($break_violation);
                }
            }

            if ($eleven && count($eleven) > 0) {

                foreach ($eleven as $data4) {

                    $drive_violate = convertTimeToSeconds($data4['drive_violate']);

                    $violation4 += $drive_violate;
                }
            }

            // Convert each violation to seconds
            $violation1_seconds = $violation1;
            $violation2_seconds = $violation2;
            $violation3_seconds = $violation3;
            $violation4_seconds = $violation4;

            $shiftTime_seconds = convertTimeToSeconds($shiftTime);
            $cycleTime_seconds = convertTimeToSeconds($cycleTime);
            $elevenTime_seconds = convertTimeToSeconds($elevenTime);
            $eightTime_seconds = convertTimeToSeconds($eightTime);

            // Add all violations in seconds
            $total_seconds = $violation1_seconds + $violation2_seconds + $violation3_seconds + $violation4_seconds;

            $total_time_seconds = $shiftTime_seconds + $cycleTime_seconds + $elevenTime_seconds + $eightTime_seconds;

            // Convert total seconds back to H:MM:SS format
            $total_time_violation = convertSecondsToTime($total_seconds);

            $total_time = convertSecondsToTime($total_time_seconds);

            // Fetch shift logs
            $driverShifts = DriverShiftLog::where('driver_id', $driverId)
                ->where('is_add_approved', 1)
                ->where('current_shift_status', 3)
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

            if ($driverShifts && count($driverShifts) > 0) {

                foreach ($driverShifts as $shiftData) {

                    $time = create_end_time($shiftData, $create, $shiftData, $last, $currentTime);

                    $timeStart = $time[0];

                    $timeEnd = $time[1];

                    $vehicleId = $shiftData->vehicle_id;

                    if ($vehicleId) {

                        $device = Device::where('vehicle_id', $vehicleId)->first();

                        if ($device) {

                            // Fetch vehicle logs
                            $vehicleLogs = VehicleLogHistory::where('identifier', $device->serial_number)
                                ->whereBetween('event_date_time', [$timeStart, $timeEnd])
                                ->whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])
                                ->where('obd_speed', '>', 5)
                                ->orderBy('event_date_time', 'ASC')
                                ->get();

                            foreach ($vehicleLogs as $logData) {
                                $logCreate = Carbon::parse($logData->event_date_time);
                                $aboveLatestTime = Carbon::parse($this->getAboveLatestTime($logData, $logData->identifier, $create, $last, $currentTime));
                                $assignedDriveSec += $aboveLatestTime->diffInSeconds($logCreate);
                            }
                        }
                    }
                }
            }

            // Store results
            $data[] = [
                'driver_name' => $driverName,
                'total_time_violation' => $total_time_violation,
                'total_time' => $total_time,
                'assigned_time' => $assignedDriveSec,
                'total_drive' => $totalDriveTimeSec,
            ];
        }

        return response()->json($data, 200);
    }

    private function convertTimeToSeconds($time)
    {
        list($hours, $minutes, $seconds) = explode(':', $time);
        return $hours * 3600 + $minutes * 60 + $seconds;
    }

    private function convertSecondsToTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds % 60);
    }

    private function getAboveTimes($shiftData, $driverId, $currentTime, $endDay)
    {
        $abvRW = DriverShiftLog::where('id', '>', $shiftData->id)
            ->where('is_add_approved', 1)
            ->where('driver_id', $driverId)
            ->orderBy('id', 'asc')
            ->first();

        return $abvRW ? ($abvRW->end_log_time != null ? $abvRW->end_log_time : $currentTime) : min($currentTime, $endDay);
    }

    private function getAboveLatestTime($logData, $serialNumber, $create, $last, $currentTime)
    {
        $aboveLatestVehicleRow = VehicleLogHistory::where('id', '>', $logData->id)
            ->where('identifier', $serialNumber)
            ->orderBy('id', 'asc')
            ->first();


        $timezone = Auth::user()->timezone;

        $currentTime = Carbon::parse()->setTimezone($timezone)->toDateTimeLocalString();

        $currentTime = Carbon::parse($currentTime);

        if ($aboveLatestVehicleRow) {
            $lastTime = $aboveLatestVehicleRow->event_date_time;
        } else {
            $lastTime = $currentTime;
        }

        $timeData = ($lastTime > $currentTime ? $currentTime : ($lastTime > $last ? $last : $lastTime));
        return Carbon::parse($timeData);
    }
}
