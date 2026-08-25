<?php

use App\Services\WhatsAppService;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\Rules;
use Carbon\Carbon;
use App\Models\RuleAssign;
use App\Models\DriverShiftLog;
use App\Models\Inspection;
use App\Models\Vehicle;
use App\Models\ListOption;
use Illuminate\Support\Facades\DB;

if (!function_exists('send_message_whatsApp')) {

    function send_message_whatsApp(
        array $numbers,
        string $contentSid,
        array $variables = []
    ) {
        $whatsApp = app(App\Services\WhatsAppService::class);

        foreach ($numbers as $number) {

            if (empty($number)) {
                continue;
            }

            $whatsApp->sendTemplateMessage(
                $number,
                $contentSid,
                $variables
            );
        }
    }
}

function new_dashboard_driver_log_time($id, $time)
{

    $driveTime = 0;
    $shiftViolTime = 0;

    $ViolShift = null;
    $curretLog = null;
    $driver = null;
    $userInfo = null;
    $violCycleTime = null;
    $latestDiffTime = null;
    $violDriveTime = null;
    $violBreakTime = null;

    $user = User::where("user_type", "U")
        ->where("id", $id)
        ->select(
            "id",
            "first_name",
            "last_name",
            "email",
            "mobile_no",
            "pin_code",
            "address",
            "timezone",
            "avatar_image",
            "language_id",
            "is_active"
        )
        ->with("language")
        ->first();

    $driver = $user;

    $userInfo = UserInfo::where("user_id", $id)
        ->with(
            "homeTerminal:id,name,address,type,shapeData,latitude,longitude,radius,tags,notes,status"
        )
        ->first();

    $currentTime = Carbon::parse($time);

    $timeSet = Carbon::parse($currentTime)->format("y-m-d");

    $ruleAssgn = RuleAssign::where("user_id", $id)->get();

    $latestLog = DriverShiftLog::where("driver_id", $id)
        ->where("is_add_approved", 1)
        ->where('is_unidentified', 0)
        ->latest("start_log_time")
        ->first();

    $shiftCheck = true;
    $cycleCheck = true;

    if ($latestLog) {

        $currentShiftStatus = $latestLog->current_shift_status;

        $latestEndLogTime = $latestLog->end_log_time;

        if (!is_null($latestEndLogTime)) {
            if (Carbon::parse($latestEndLogTime)->ne($currentTime)) {
                $currentShiftStatus = 1;
            }
        }

        if (in_array($currentShiftStatus, [1, 2, 5])) {

            $ruleId = RuleAssign::where('user_id', $id)
                ->pluck('rule_id'); // Get an array of rule_ids from RuleAssign

            $locationName = null;

            $timeStartData = shift_cycle_start_check(
                $latestLog,
                $currentTime,
                $locationName,
                $ruleId,
                0
            );

            if (count($timeStartData) > 1) {
                $shiftStart = $timeStartData[0];
                $cycleStart = $timeStartData[1];

                // $currentShiftStatus is in the array
                if ($shiftStart == 1) {
                    $shiftCheck = false;
                }

                if ($cycleStart == 1) {
                    $cycleCheck = false;
                }
            }
        }
    }

    $shiftLogData = DriverShiftLog::where("driver_id", $id)
        ->where("is_add_approved", 1)
        ->where('shift_start', 1)
        ->orderBy('start_log_time', 'DESC')
        ->select(
            "id",
            "driver_id",
            "vehicle_id",
            "codriver_id",
            "shift_changed_time",
            "start_log_time",
            "end_log_time",
            "current_shift_status",
            "location_name",
            "location_end",
            "notes",
            "message_reason",
            "shift_start",
            "cycle_start",
            "system_entry",
            "created_at"
        )
        ->with(
            "vehicle:id,name",
            "user:id,first_name,last_name",
            "option:id,list_id,option_id,title"
        )
        ->first();

    $cycleLogData = DriverShiftLog::where("driver_id", $id)
        ->where("is_add_approved", 1)
        ->where('shift_start', 1)
        ->orderBy('start_log_time', 'DESC')
        ->select(
            "id",
            "driver_id",
            "vehicle_id",
            "codriver_id",
            "shift_changed_time",
            "start_log_time",
            "end_log_time",
            "current_shift_status",
            "location_name",
            "location_end",
            "notes",
            "message_reason",
            "shift_start",
            "cycle_start",
            "system_entry",
            "created_at"
        )
        ->with(
            "vehicle:id,name",
            "user:id,first_name,last_name",
            "option:id,list_id,option_id,title"
        )
        ->first();

    $shiftLog[] = $shiftLogData;
    $cycleLog[] = $cycleLogData;
    $driveLog = [];
    $currentLatestLog = $latestLog;

    $currentCycleLog = $cycleLogData;
    $currentShiftLog = $shiftLogData;
    $currentDriveLog = $shiftLogData;

    if ($shiftLogData && $shiftLogData->current_shift_status == 3) {
        $driveLog[] = $shiftLogData;
    }

    while (true && $currentShiftLog) {
        $nextShiftLog = DriverShiftLog::where('driver_id', $id)
            ->where('start_log_time', '>', $currentShiftLog->start_log_time)
            ->where('is_add_approved', 1)
            ->orderBy('start_log_time', 'asc')
            ->select(
                "id",
                "driver_id",
                "vehicle_id",
                "codriver_id",
                "shift_changed_time",
                "start_log_time",
                "end_log_time",
                "current_shift_status",
                "location_name",
                "location_end",
                "notes",
                "message_reason",
                "shift_start",
                "cycle_start",
                "system_entry",
                "created_at"
            )
            ->with(
                "vehicle:id,name",
                "user:id,first_name,last_name",
                "option:id,list_id,option_id,title"
            )
            ->first();

        if (!$nextShiftLog) {
            break; // Exit loop if no previous log is found
        }

        $shiftLog[] = $nextShiftLog;
        $currentShiftLog = $nextShiftLog;
    }

    while (true && $currentCycleLog) {
        $nextCycleLog = DriverShiftLog::where('driver_id', $id)
            ->where('start_log_time', '>', $currentCycleLog->start_log_time)
            ->where('is_add_approved', 1)
            ->orderBy('start_log_time', 'asc')
            ->select(
                "id",
                "driver_id",
                "vehicle_id",
                "codriver_id",
                "shift_changed_time",
                "start_log_time",
                "end_log_time",
                "current_shift_status",
                "location_name",
                "location_end",
                "notes",
                "message_reason",
                "shift_start",
                "cycle_start",
                "system_entry",
                "created_at"
            )
            ->with(
                "vehicle:id,name",
                "user:id,first_name,last_name",
                "option:id,list_id,option_id,title"
            )
            ->first();

        if (!$nextCycleLog) {
            break; // Exit loop if no previous log is found
        }

        $cycleLog[] = $nextCycleLog;
        $currentCycleLog = $nextCycleLog;
    }

    while (true && $currentDriveLog) {
        $nextDriveLog = DriverShiftLog::where('driver_id', $id)
            ->where('current_shift_status', 3)
            ->where('start_log_time', '>', $currentDriveLog->start_log_time)
            ->where('is_add_approved', 1)
            ->orderBy('start_log_time', 'asc')
            ->select(
                "id",
                "driver_id",
                "vehicle_id",
                "codriver_id",
                "shift_changed_time",
                "start_log_time",
                "end_log_time",
                "current_shift_status",
                "location_name",
                "location_end",
                "notes",
                "message_reason",
                "shift_start",
                "cycle_start",
                "system_entry",
                "created_at"
            )
            ->with(
                "vehicle:id,name",
                "user:id,first_name,last_name",
                "option:id,list_id,option_id,title"
            )
            ->first();

        if (!$nextDriveLog) {
            break; // Exit loop if no previous log is found
        }

        $driveLog[] = $nextDriveLog;
        $currentDriveLog = $nextDriveLog;
    }

    $eightDriverLog = [];

    if ($latestLog && $latestLog->current_shift_status == 3) {
        $eightDriverLog[] = $latestLog;
        $currentLatestLog = $latestLog;

        while (true) {
            $nextDriveLog = DriverShiftLog::where('driver_id', $id)
                ->where('end_log_time', '=', $currentLatestLog->start_log_time)
                ->where('is_add_approved', 1)
                ->orderBy('start_log_time', 'desc')
                ->select(
                    "id",
                    "driver_id",
                    "vehicle_id",
                    "codriver_id",
                    "shift_changed_time",
                    "start_log_time",
                    "end_log_time",
                    "current_shift_status",
                    "location_name",
                    "location_end",
                    "notes",
                    "message_reason",
                    "shift_start",
                    "cycle_start",
                    "system_entry",
                    "created_at"
                )
                ->with(
                    "vehicle:id,name",
                    "user:id,first_name,last_name",
                    "option:id,list_id,option_id,title"
                )
                ->first();

            //Break if no more logs or the next one isn't status 3
            if (!$nextDriveLog || $nextDriveLog->current_shift_status != 3) {
                break;
            }

            $eightDriverLog[] = $nextDriveLog;
            $currentLatestLog = $nextDriveLog;
        }
    }

    $vehicles = null;

    if ($latestLog) {

        $vehicleId = $latestLog->vehicle_id;

        $startTime = $latestLog->start_log_time;
        $endTime = $latestLog->end_log_time;

        $startTime = Carbon::parse($startTime);
        $endTime = is_null($endTime) ? Carbon::parse($currentTime) : Carbon::parse($endTime);

        $vehicles = Vehicle::where("id", $vehicleId)
            ->select(
                "id",
                "name",
                "vin",
                "make",
                "model",
                "year",
                "fuel_type",
                "license_state",
                "fuel_tank_primary",
                "fuel_tank_secondary",
                "throttle_wifi",
                "license_plate",
                "status"
            )
            ->first();

        $currentShiftStatus = $latestLog->current_shift_status;

        // if (!is_null($latestEndLogTime)) {
        //   if (Carbon::parse($endTime)->ne($currentTime)) {

        //       $startTime = $endTime;

        //       $endTime = $currentTime;

        //       $currentShiftStatus = 1;
        //   }
        // }

        $log = ListOption::where("list_id", "driving_status")
            ->where("option_id", $currentShiftStatus)
            ->pluck("title")
            ->first();

        $curretLog = $currentShiftStatus;

        $latestInSec = 0;

        if (!is_null($startTime) && !is_null($endTime)) {

            $startTime = Carbon::parse($startTime);

            $endTime = Carbon::parse($endTime);

            $latestInSec = $endTime->diffInSeconds($startTime);
        }

        $latestDiffTime = secondsToTime($latestInSec);
    }

    if ($ruleAssgn) {

        foreach ($ruleAssgn as $data) {

            $rule = Rules::find($data->rule_id);

            //This is for rule of shift 14 hour
            if ($rule->reason == 1) {

                $maxHr = $rule->max_hour_limit; // 14 hours

                // Convert $maxHr to seconds
                $maxHrSeconds = $maxHr * 3600;

                $shiftTimeSeconds = 0;

                if ($shiftCheck && !empty($shiftLog)) {
                    foreach ($shiftLog as $logShift) {
                        if (!$logShift) {
                            continue;
                        }

                        $status = $logShift->current_shift_status;
                        $startTime = Carbon::parse($logShift->start_log_time);
                        $endTime = $logShift->end_log_time ? Carbon::parse($logShift->end_log_time) : $currentTime;

                        // Only count shift time if status is NOT 1, 2, or 5
                        if (!in_array($status, [1, 2, 5])) {
                            $shiftTimeSeconds += $endTime->diffInSeconds($startTime);
                        }
                    }
                }

                if ($maxHrSeconds > $shiftTimeSeconds) {
                    $shiftViolTime = $maxHrSeconds - $shiftTimeSeconds;
                    $ViolShift = secondsToTime($shiftViolTime);
                } else {
                    $ViolShift = "00:00:00";
                }
            } elseif ($rule->reason == 5 || $rule->reason == 2) {

                $maxHr = $rule->max_hour_limit;

                // Convert $maxHr to seconds
                $maxHrSeconds = $maxHr * 3600;

                $cycleTimeSeconds = 0;

                if ($cycleCheck && !empty($cycleLog)) {
                    foreach ($cycleLog as $log) {
                        if (!$log) {
                            continue;
                        }

                        $status = $log->current_shift_status;
                        $startTime = Carbon::parse($log->start_log_time);
                        $endTime = $log->end_log_time ? Carbon::parse($log->end_log_time) : $currentTime;

                        // Only count cycle time if status is NOT 1, 2, or 5
                        if (!in_array($status, [1, 2, 5])) {
                            $cycleTimeSeconds += $endTime->diffInSeconds($startTime);
                        }
                    }
                }

                if ($maxHrSeconds > $cycleTimeSeconds) {
                    $violCycleTime = $maxHrSeconds - $cycleTimeSeconds;
                    $violCycleTime = secondsToTime($violCycleTime);
                } else {
                    $violCycleTime = "00:00:00";
                }
            } elseif ($rule->reason == 3) {

                $maxHr = $rule->max_hour_limit;

                // Convert $maxHr to seconds
                $maxHrSeconds = $maxHr * 3600;

                $driveTimeSeconds = 0;

                if ($shiftCheck && !empty($driveLog)) {
                    foreach ($driveLog as $log) {
                        if (!$log) {
                            continue;
                        }

                        $status = $log->current_shift_status;
                        $startTime = Carbon::parse($log->start_log_time);
                        $endTime = $log->end_log_time ? Carbon::parse($log->end_log_time) : $currentTime;

                        // Count only when status is 3 (Driving)
                        if ($status == 3) {
                            $driveTimeSeconds += $endTime->diffInSeconds($startTime);
                        }
                    }
                }

                if ($maxHrSeconds > $driveTimeSeconds) {
                    $violDriveTimes = $maxHrSeconds - $driveTimeSeconds;
                    $violDriveTime = secondsToTime($violDriveTimes);
                } else {
                    $violDriveTime = "00:00:00";
                }
            } elseif ($rule->reason == 4) {

                $totalCountDrive = 0;

                if (!empty($eightDriverLog)) {
                    foreach ($eightDriverLog as $log) {
                        if (!$log) {
                            continue;
                        }

                        $status = $log->current_shift_status;
                        $startTime = Carbon::parse($log->start_log_time);
                        $endTime = $log->end_log_time ? Carbon::parse($log->end_log_time) : $currentTime;

                        // Only count driving time (status == 3)
                        if ($status == 3) {
                            $totalCountDrive += $endTime->diffInSeconds($startTime);
                        }
                    }
                }

                $maxHr = $rule->max_hour_limit;

                // Convert $maxHr to seconds
                $maxHrSeconds = $maxHr * 3600;

                // Convert $shiftTime to seconds
                $driveTimeSeconds = timeToSeconds($driveTime);

                if ($maxHrSeconds > $totalCountDrive) {
                    $violBreakTimes = $maxHrSeconds - $totalCountDrive;
                    $violBreakTime = secondsToTime($violBreakTimes);
                } else {
                    $violBreakTime = "00:00:00";
                }
            }
        }
    }

    $data = [
        $driver,
        $vehicles,
        is_null($latestDiffTime) ? "00:00:00" : $latestDiffTime,
        $userInfo,
        $ViolShift,
        is_null($curretLog) ? "1" : $curretLog,
        $violCycleTime,
        $violDriveTime,
        $violBreakTime,
        $shiftLog,
        $timeSet,
    ];

    return $data;
}

function mobile_insertMissingLogs($data)
{
    if (count($data) <= 1) {
        return $data;
    }

    // Sort by start time first
    usort($data, function ($a, $b) {
        return Carbon::parse($a['start_log_time'])
            ->lte(Carbon::parse($b['start_log_time'])) ? -1 : 1;
    });

    $result = [];

    for ($i = 0; $i < count($data) - 1; $i++) {

        $currentStart = Carbon::parse($data[$i]['start_log_time']);
        $currentEnd = Carbon::parse($data[$i]['end_log_time']);

        // Skip invalid logs
        if ($currentStart->gte($currentEnd)) {
            continue;
        }

        $nextStart = Carbon::parse($data[$i + 1]['start_log_time']);

        // If this log overlaps the next one, trim its end back to the
        // next log's start so overlapping fragments never reach the response.
        if ($currentEnd->gt($nextStart)) {
            $data[$i]['end_log_time'] = $nextStart->format('H:i:s');
            $currentEnd = $nextStart;
        }

        // After trimming, the log may have become zero/negative length; drop it.
        if ($currentStart->gte($currentEnd)) {
            continue;
        }

        $result[] = $data[$i];

        // Insert missing OFF DUTY log only when there is a real gap
        if ($currentEnd->lt($nextStart)) {

            $result[] = [
                "log_id" => $data[$i]["log_id"],
                "shift_id" => 1,
                "log_name" => "Off duty",
                "start_log_time" => $currentEnd->format('H:i:s'),
                "end_log_time" => $nextStart->format('H:i:s'),
                "vehicle_name" => $data[$i]["vehicle_name"],
                "vehicle_id" => $data[$i]["vehicle_id"],
            ];
        }
    }

    $last = end($data);

    if (Carbon::parse($last['start_log_time'])->lt(Carbon::parse($last['end_log_time']))) {
        $result[] = $last;
    }

    return array_values($result);
}

function mobile_graph_hos_chart($id, $startTime, $endTime, $currentTime, $masterId)
{

    $startTime = Carbon::parse($startTime)->startOfDay();

    $endTime = Carbon::parse($endTime)->endOfDay();

    $viol = check_eld_rules($id, $startTime, $endTime);

    $datass = [];

    $create = $startTime;

    $last = $endTime;

    $totalTimeDiffInSec = 0;

    $viol = check_eld_rules($id, $startTime, $endTime);

    $inspection = Inspection::where('created_by', $masterId)
        ->where(function ($query) use ($startTime, $endTime) {
            $query->where('inspection_start_time', '<=', $endTime)
                ->where('inspection_end_time', '>=', $startTime);
        })
        ->exists();

    $distinctVehicleIds = DriverShiftLog::where("driver_id", $id)
        ->where("is_add_approved", 1)
        ->where(function ($query) use ($create, $last, $currentTime) {

            $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                $subQuery->where(function ($q) use ($create, $last, $currentTime) {

                    $q->where("start_log_time", ">=", $create)

                        ->where("start_log_time", "<=", $last)

                        ->orWhere(function ($query) use ($create, $last, $currentTime) {

                            $query

                                ->whereRaw("IFNULL(end_log_time, ?) >= ?", [

                                    $currentTime,

                                    $create,

                                ])

                                ->whereRaw("IFNULL(end_log_time, ?) <= ?", [

                                    $currentTime,

                                    $last,

                                ]);
                        })

                        ->orWhere(function ($q2) use ($create, $last, $currentTime) {

                            $q2->where("start_log_time", "<=", $create)

                                ->whereRaw("IFNULL(end_log_time, ?) >= ?", [

                                    $currentTime,

                                    $last,

                                ]);
                        })

                        ->orWhere(function ($q3) use ($create) {

                            $q3->whereColumn("end_log_time", "start_log_time")

                                ->orWhereRaw("end_log_time = ?", [$create]);
                        });
                });
            });
        })
        ->select("vehicle_id", "start_log_time") // Add start_log_time to select
        ->orderBy("start_log_time", "asc")
        ->distinct()
        ->pluck("vehicle_id");

    // Get distinct vehicle records

    $distinctVehicles = Vehicle::whereIn("id", $distinctVehicleIds)->get();

    $driverShift = DriverShiftLog::where("driver_id", $id)
        ->where("is_add_approved", 1)
        ->where(function ($query) use ($create, $last, $currentTime) {

            $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                // Check if there is any overlap between the time range and the log times
    
                $subQuery->where(function ($q) use ($create, $last, $currentTime) {

                    // Check if the log's start time is within the range of create and last
    
                    $q->where("start_log_time", ">=", $create)

                        ->where("start_log_time", "<=", $last)

                        // Check if the log's end time is within the range of create and last
    
                        ->orWhere(function ($query) use ($create, $last, $currentTime) {

                            $query

                                ->whereRaw("IFNULL(end_log_time, ?) >= ?", [

                                    $currentTime,

                                    $create,

                                ])

                                ->whereRaw("IFNULL(end_log_time, ?) <= ?", [

                                    $currentTime,

                                    $last,

                                ]);
                        })

                        // Check if the log encompasses the range between create and last
    
                        ->orWhere(function ($q2) use ($create, $last, $currentTime) {

                            $q2->where("start_log_time", "<=", $create)

                                ->whereRaw("IFNULL(end_log_time, ?) >= ?", [

                                    $currentTime,

                                    $last,

                                ]);
                        })

                        // Check if end_log_time equals start_log_time or create
    
                        ->orWhere(function ($q3) use ($create) {

                            $q3->whereColumn("end_log_time", "start_log_time")

                                ->orWhereRaw("end_log_time = ?", [$create]);
                        });
                });
            });
        })
        ->orderBy("start_log_time", "asc")
        ->get();

    if ($driverShift && count($driverShift) > 0) {

        foreach ($driverShift as $data) {

            $timeData = create_end_time(
                $data,
                $startTime,
                $data,
                $endTime,
                $currentTime
            );

            $create = Carbon::parse($timeData[0]);

            $last = Carbon::parse($timeData[1]);

            $vehicle = Vehicle::select('id', 'name')
                ->where('id', $data->vehicle_id)
                ->first();

            $log = $data->current_shift_status;

            $logs = ListOption::where("list_id", "driving_status")
                ->where("option_id", $log)
                ->pluck("title")
                ->first();

            $startTimeFormatted = Carbon::parse($create)->format("H:i:s");

            $endTimeFormatted = Carbon::parse($last)->format("H:i:s");

            if ($create->greaterThanOrEqualTo($last)) {
                continue;
            }


            if ($startTimeFormatted != $endTimeFormatted) {

                $totalTimeDiffInSec += Carbon::parse($startTimeFormatted)
                    ->diffInSeconds(Carbon::parse($endTimeFormatted));

                $datass[] = [

                    "log_id" => $data->id,
                    "shift_id" => $log,
                    "log_name" => $logs,
                    "start_log_time" => $startTimeFormatted,
                    "end_log_time" => $endTimeFormatted,
                    'vehicle_name' => $vehicle ? $vehicle->name : '',
                    'vehicle_id' => $vehicle ? $vehicle->id : "",
                    "is_edit_allowed" => $data->system_entry === 1 && $log === 3
                ];
            }
        }

        $datass = array_values(array_filter($datass, function ($log) {

            $start = Carbon::parse($log['start_log_time']);
            $end = Carbon::parse($log['end_log_time']);

            return $start->lt($end);
        }));

        $datass = mobile_insertMissingLogs($datass);

        $arrayLen = count($datass);

        if ($arrayLen > 0) {

            $startTime = Carbon::parse($startTime)->startOfDay();
            $currentTimeStart = Carbon::parse($currentTime)->startOfDay();
            $startTimeHI = Carbon::parse($startTime)->format("H:i:s");
            $currentHI = Carbon::parse($currentTime)->format("H:i:s");
            $endTimeHI = Carbon::parse($endTime)->format("H:i:s");

            $startTimeLogData = $datass[0]["start_log_time"];

            if ($startTimeLogData != $startTimeHI) {

                $totalTimeDiffInSec += Carbon::parse($startTimeHI)
                    ->diffInSeconds(Carbon::parse($startTimeLogData));

                $newLog = [
                    "log_id" => 116111,
                    "shift_id" => 1,
                    "log_name" => "Off duty",
                    "start_log_time" => $startTimeHI,
                    "end_log_time" => $startTimeLogData,
                    'vehicle_name' => $datass[$arrayLen - 1]['vehicle_name'],
                    'vehicle_id' => $datass[$arrayLen - 1]['vehicle_id'],
                    "is_edit_allowed" => true
                ];

                array_unshift($datass, $newLog);
            }

            $arrayLogLength = count($datass);

            $lastLogData = $datass[$arrayLogLength - 1]["end_log_time"];

            if ($startTime == $currentTimeStart) {

                if ($lastLogData != $currentHI) {

                    $totalTimeDiffInSec += Carbon::parse($datass[$arrayLen - 1]['end_log_time'])
                        ->diffInSeconds(Carbon::parse($currentHI));

                    $datass[] = [
                        "log_id" => 116,
                        "shift_id" => 1,
                        "log_name" => "Off duty",
                        "start_log_time" => $datass[$arrayLen - 1]['end_log_time'],
                        "end_log_time" => $currentHI,
                        'vehicle_name' => $datass[$arrayLen - 1]["vehicle_name"],
                        'vehicle_id' => $datass[$arrayLen - 1]['vehicle_id'],
                        "is_edit_allowed" => true
                    ];

                }
            } else {

                if ($lastLogData != "23:59:59") {

                    $totalTimeDiffInSec += Carbon::parse($datass[$arrayLen - 1]['end_log_time'])
                        ->diffInSeconds(Carbon::parse("23:59:59"));

                    $datass[] = [
                        "log_id" => 116,
                        "shift_id" => 1,
                        "log_name" => "Off duty",
                        "start_log_time" => $datass[$arrayLen - 1]['end_log_time'],
                        "end_log_time" => "23:59:59",
                        'vehicle_name' => $datass[$arrayLen - 1]['vehicle_name'],
                        'vehicle_id' => $datass[$arrayLen - 1]['vehicle_id'],
                        "is_edit_allowed" => true
                    ];

                }
            }
        } else {

            $startTime = Carbon::parse($startTime)->startOfDay();

            $currentTimeStart = Carbon::parse($currentTime)->startOfDay();

            $startTimeHI = Carbon::parse($startTime)->format("H:i:s");

            $currentHI = Carbon::parse($currentTime)->format("H:i:s");

            $endTimeHI = Carbon::parse($endTime)->format("H:i:s");

            if ($startTime == $currentTimeStart) {

                $totalTimeDiffInSec += Carbon::parse($startTimeHI)
                    ->diffInSeconds(Carbon::parse($currentHI));

                $datass[] = [
                    "log_id" => 1,
                    "shift_id" => 1,
                    "log_name" => "Off duty",
                    "start_log_time" => $startTimeHI,
                    "end_log_time" => $currentHI,
                    'vehicle_name' => "",
                    'vehicle_id' => "",
                    "is_edit_allowed" => true
                ];

            } elseif ($startTime < $currentTime) {

                $totalTimeDiffInSec += Carbon::parse($startTimeHI)
                    ->diffInSeconds(Carbon::parse($endTimeHI));

                $datass[] = [
                    "log_id" => 1,
                    "shift_id" => 1,
                    "log_name" => "Off duty",
                    "start_log_time" => $startTimeHI,
                    "end_log_time" => $endTimeHI,
                    'vehicle_name' => "",
                    'vehicle_id' => "",
                    "is_edit_allowed" => true
                ];

            } else {

                $totalTimeDiffInSec += Carbon::parse($startTimeHI)
                    ->diffInSeconds(Carbon::parse($startTimeHI));

                $datass[] = [
                    "log_id" => 1,
                    "shift_id" => 1,
                    "log_name" => "Off duty",
                    "start_log_time" => $startTimeHI,
                    "end_log_time" => $startTimeHI,
                    'vehicle_name' => "",
                    'vehicle_id' => "",
                    "is_edit_allowed" => true
                ];

            }
        }
    } else {

        $currentStartTime = Carbon::parse($currentTime)->startOfDay();

        $startTime = Carbon::parse($startTime);

        if ($startTime == $currentStartTime) {

            $currentStartTime = Carbon::parse($currentTime)->format("H:i:s");

            $startTime = Carbon::parse($startTime)->format("H:i:s");

            $totalTimeDiffInSec += Carbon::parse($startTime)
                ->diffInSeconds(Carbon::parse($currentStartTime));

            $datass[] = [
                "log_id" => 1,
                "shift_id" => 1,
                "log_name" => "Off duty",
                "start_log_time" => $startTime,
                "end_log_time" => $currentStartTime,
                'vehicle_name' => "",
                'vehicle_id' => "",
                "is_edit_allowed" => true

            ];
        } else {

            $startTime = Carbon::parse($startTime)->format("H:i:s");

            $endTime = Carbon::parse($endTime)->format("H:i:s");

            $totalTimeDiffInSec += Carbon::parse($startTime)
                ->diffInSeconds(Carbon::parse($endTime));

            $datass[] = [
                "log_id" => 1,
                "shift_id" => 1,
                "log_name" => "Off duty",
                "start_log_time" => $startTime,
                "end_log_time" => $endTime,
                'vehicle_name' => "",
                'vehicle_id' => ""
            ];
        }
    }

    if ($distinctVehicles && count($distinctVehicles) == 0) {

        $distinctVehicles[] = [
            "id" => "",
            "name" => "",

        ];
    }

    $totalTimeFormatted = sprintf(
        '%02d:%02d:%02d',
        floor($totalTimeDiffInSec / 3600),
        floor(($totalTimeDiffInSec % 3600) / 60),
        $totalTimeDiffInSec % 60
    );

    return [$datass, $distinctVehicles, $viol, $totalTimeFormatted, $inspection];
}

function mobile_graph_hos_log_data($id, $startTime, $endTime, $currentTime, $masterId)
{

    $startTime = Carbon::parse($startTime)->startOfDay();

    $endTime = Carbon::parse($endTime)->endOfDay();

    $viol = check_eld_rules($id, $startTime, $endTime);

    $datass = [];

    $create = $startTime;

    $last = $endTime;

    $totalTimeDiffInSec = 0;

    $viol = check_eld_rules($id, $startTime, $endTime);

    $inspection = Inspection::where('created_by', $masterId)
        ->where(function ($query) use ($startTime, $endTime) {
            $query->where('inspection_start_time', '<=', $endTime)
                ->where('inspection_end_time', '>=', $startTime);
        })
        ->exists();

    $distinctVehicleIds = DriverShiftLog::where("driver_id", $id)
        ->where("is_add_approved", 1)
        ->where(function ($query) use ($create, $last, $currentTime) {
            $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                $subQuery->where(function ($q) use ($create, $last, $currentTime) {
                    $q->where("start_log_time", ">=", $create)
                        ->where("start_log_time", "<=", $last)
                        ->orWhere(function ($query) use ($create, $last, $currentTime) {
                            $query
                                ->whereRaw("IFNULL(end_log_time, ?) >= ?", [
                                    $currentTime,
                                    $create,
                                ])
                                ->whereRaw("IFNULL(end_log_time, ?) <= ?", [
                                    $currentTime,
                                    $last,
                                ]);
                        })
                        ->orWhere(function ($q2) use ($create, $last, $currentTime) {
                            $q2->where("start_log_time", "<=", $create)
                                ->whereRaw("IFNULL(end_log_time, ?) >= ?", [
                                    $currentTime,
                                    $last,
                                ]);
                        })
                        ->orWhere(function ($q3) use ($create) {
                            $q3->whereColumn("end_log_time", "start_log_time")
                                ->orWhereRaw("end_log_time = ?", [$create]);
                        });
                });
            });
        })
        ->select("vehicle_id", "start_log_time") // Add start_log_time to select
        ->orderBy("start_log_time", "asc")
        ->distinct()
        ->pluck("vehicle_id");

    // Get distinct vehicle records
    $distinctVehicles = Vehicle::whereIn("id", $distinctVehicleIds)->get();

    $driverShift = DriverShiftLog::where("driver_id", $id)
        ->where("is_add_approved", 1)
        ->where(function ($query) use ($create, $last, $currentTime) {
            $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                $subQuery->where(function ($q) use ($create, $last, $currentTime) {
                    $q->where("start_log_time", ">=", $create)
                        ->where("start_log_time", "<=", $last)
                        ->orWhere(function ($query) use ($create, $last, $currentTime) {
                            $query
                                ->whereRaw("IFNULL(end_log_time, ?) >= ?", [
                                    $currentTime,
                                    $create,
                                ])
                                ->whereRaw("IFNULL(end_log_time, ?) <= ?", [
                                    $currentTime,
                                    $last,
                                ]);
                        })
                        ->orWhere(function ($q2) use ($create, $last, $currentTime) {
                            $q2->where("start_log_time", "<=", $create)
                                ->whereRaw("IFNULL(end_log_time, ?) >= ?", [
                                    $currentTime,
                                    $last,
                                ]);
                        })
                        ->orWhere(function ($q3) use ($create) {
                            $q3->whereColumn("end_log_time", "start_log_time")
                                ->orWhereRaw("end_log_time = ?", [$create]);
                        });
                });
            });
        })
        ->orderBy("start_log_time", "asc")
        ->get();

    if ($driverShift && count($driverShift) > 0) {

        foreach ($driverShift as $data) {

            $timeData = create_end_time(
                $data,
                $startTime,
                $data,
                $endTime,
                $currentTime
            );

            $create = Carbon::parse($timeData[0]);

            $last = Carbon::parse($timeData[1]);

            $vehicle = Vehicle::select('id', 'name')
                ->where('id', $data->vehicle_id)
                ->first();

            $log = $data->current_shift_status;

            $logs = ListOption::where("list_id", "driving_status")
                ->where("option_id", $log)
                ->pluck("title")
                ->first();

            $startTimeFormatted = Carbon::parse($create)->format("H:i:s");

            $endTimeFormatted = Carbon::parse($last)->format("H:i:s");

            $odometer = $data->odometer;

            $locationStart = $data->location_name;
            $locationEnd = $data->location_end;

            $engineHour = $data->engineHour;

            if ($startTimeFormatted != $endTimeFormatted) {

                $totalTimeDiffInSec += Carbon::parse($startTimeFormatted)
                    ->diffInSeconds(Carbon::parse($endTimeFormatted));

                $datass[] = [

                    "log_id" => $data->id,
                    "shift_id" => $log,
                    "log_name" => $logs,
                    "start_log_time" => $startTimeFormatted,
                    "end_log_time" => $endTimeFormatted,
                    'vehicle_name' => $vehicle ? $vehicle->name : '',
                    'vehicle_id' => $vehicle ? $vehicle->id : "",
                    "odometer" => $odometer,
                    "location_start" => $locationStart,
                    "location_end" => $locationEnd,
                    "engine_hour" => $engineHour,
                    "is_edit_allowed" => $data->system_entry === 1 && $log === 3
                ];
            }
        }

        $datass = mobile_insertMissingLogs($datass);

        $arrayLen = count($datass);

        if ($arrayLen > 0) {

            $startTime = Carbon::parse($startTime)->startOfDay();
            $currentTimeStart = Carbon::parse($currentTime)->startOfDay();
            $startTimeHI = Carbon::parse($startTime)->format("H:i:s");
            $currentHI = Carbon::parse($currentTime)->format("H:i:s");
            $endTimeHI = Carbon::parse($endTime)->format("H:i:s");

            $startTimeLogData = $datass[0]["start_log_time"];

            if ($startTimeLogData != $startTimeHI) {

                $totalTimeDiffInSec += Carbon::parse($startTimeHI)
                    ->diffInSeconds(Carbon::parse($startTimeLogData));

                $newLog = [
                    "log_id" => 116111,
                    "shift_id" => 1,
                    "log_name" => "Off duty",
                    "start_log_time" => $startTimeHI,
                    "end_log_time" => $startTimeLogData,
                    'vehicle_name' => $datass[$arrayLen - 1]['vehicle_name'],
                    'vehicle_id' => $datass[$arrayLen - 1]['vehicle_id'],
                    "odometer" => $datass[$arrayLen - 1]['odometer'],
                    "location_start" => $datass[$arrayLen - 1]['location_start'],
                    "location_end" => $datass[$arrayLen - 1]['location_end'],
                    "engine_hour" => $datass[$arrayLen - 1]['engineHour'],
                    "is_edit_allowed" => true
                ];

                array_unshift($datass, $newLog);
            }

            $arrayLogLength = count($datass);

            $lastLogData = $datass[$arrayLogLength - 1]["end_log_time"];

            if ($startTime == $currentTimeStart) {

                if ($lastLogData != $currentHI) {

                    $totalTimeDiffInSec += Carbon::parse($datass[$arrayLen - 1]['end_log_time'])
                        ->diffInSeconds(Carbon::parse($currentHI));

                    $datass[] = [
                        "log_id" => 116,
                        "shift_id" => 1,
                        "log_name" => "Off duty",
                        "start_log_time" => $datass[$arrayLen - 1]['end_log_time'],
                        "end_log_time" => $currentHI,
                        'vehicle_name' => $datass[$arrayLen - 1]["vehicle_name"],
                        'vehicle_id' => $datass[$arrayLen - 1]['vehicle_id'],
                        "odometer" => $datass[$arrayLen - 1]['odometer'],
                        "location_start" => $datass[$arrayLen - 1]['location_start'],
                        "location_end" => $datass[$arrayLen - 1]['location_end'],
                        "engine_hour" => $datass[$arrayLen - 1]['engineHour'],
                        "is_edit_allowed" => true
                    ];
                }
            } else {

                if ($lastLogData != "23:59:59") {

                    $totalTimeDiffInSec += Carbon::parse($datass[$arrayLen - 1]['end_log_time'])
                        ->diffInSeconds(Carbon::parse("23:59:59"));

                    $datass[] = [
                        "log_id" => 116,
                        "shift_id" => 1,
                        "log_name" => "Off duty",
                        "start_log_time" => $datass[$arrayLen - 1]['end_log_time'],
                        "end_log_time" => "23:59:59",
                        'vehicle_name' => $datass[$arrayLen - 1]['vehicle_name'],
                        'vehicle_id' => $datass[$arrayLen - 1]['vehicle_id'],
                        "odometer" => $datass[$arrayLen - 1]['odometer'],
                        "location_start" => $datass[$arrayLen - 1]['location_start'],
                        "location_end" => $datass[$arrayLen - 1]['location_end'],
                        "engine_hour" => $datass[$arrayLen - 1]['engineHour'],
                        "is_edit_allowed" => true
                    ];
                }
            }
        } else {

            $startTime = Carbon::parse($startTime)->startOfDay();

            $currentTimeStart = Carbon::parse($currentTime)->startOfDay();

            $startTimeHI = Carbon::parse($startTime)->format("H:i:s");

            $currentHI = Carbon::parse($currentTime)->format("H:i:s");

            $endTimeHI = Carbon::parse($endTime)->format("H:i:s");

            if ($startTime == $currentTimeStart) {

                $totalTimeDiffInSec += Carbon::parse($startTimeHI)
                    ->diffInSeconds(Carbon::parse($currentHI));

                $datass[] = [
                    "log_id" => 1,
                    "shift_id" => 1,
                    "log_name" => "Off duty",
                    "start_log_time" => $startTimeHI,
                    "end_log_time" => $currentHI,
                    'vehicle_name' => "",
                    'vehicle_id' => "",
                    "odometer" => "",
                    "location_start" => "",
                    "location_end" => "",
                    "engine_hour" => "",
                    "is_edit_allowed" => true
                ];
            } elseif ($startTime < $currentTime) {

                $totalTimeDiffInSec += Carbon::parse($startTimeHI)
                    ->diffInSeconds(Carbon::parse($endTimeHI));

                $datass[] = [
                    "log_id" => 1,
                    "shift_id" => 1,
                    "log_name" => "Off duty",
                    "start_log_time" => $startTimeHI,
                    "end_log_time" => $endTimeHI,
                    'vehicle_name' => "",
                    'vehicle_id' => "",
                    "odometer" => "",
                    "location_start" => "",
                    "location_end" => "",
                    "engine_hour" => "",
                    "is_edit_allowed" => true

                ];
            } else {

                $totalTimeDiffInSec += Carbon::parse($startTimeHI)
                    ->diffInSeconds(Carbon::parse($startTimeHI));

                $datass[] = [
                    "log_id" => 1,
                    "shift_id" => 1,
                    "log_name" => "Off duty",
                    "start_log_time" => $startTimeHI,
                    "end_log_time" => $startTimeHI,
                    'vehicle_name' => "",
                    'vehicle_id' => "",
                    "odometer" => "",
                    "location_start" => "",
                    "location_end" => "",
                    "engine_hour" => "",
                    "is_edit_allowed" => true

                ];
            }
        }
    } else {

        $currentStartTime = Carbon::parse($currentTime)->startOfDay();

        $startTime = Carbon::parse($startTime);

        if ($startTime == $currentStartTime) {

            $currentStartTime = Carbon::parse($currentTime)->format("H:i:s");

            $startTime = Carbon::parse($startTime)->format("H:i:s");

            $totalTimeDiffInSec += Carbon::parse($startTime)
                ->diffInSeconds(Carbon::parse($currentStartTime));

            $datass[] = [
                "log_id" => 1,
                "shift_id" => 1,
                "log_name" => "Off duty",
                "start_log_time" => $startTime,
                "end_log_time" => $currentStartTime,
                'vehicle_name' => "",
                'vehicle_id' => "",
                "odometer" => "",
                "location_start" => "",
                "location_end" => "",
                "engine_hour" => "",
                "is_edit_allowed" => true

            ];
        } else {

            $startTime = Carbon::parse($startTime)->format("H:i:s");

            $endTime = Carbon::parse($endTime)->format("H:i:s");

            $totalTimeDiffInSec += Carbon::parse($startTime)
                ->diffInSeconds(Carbon::parse($endTime));

            $datass[] = [
                "log_id" => 1,
                "shift_id" => 1,
                "log_name" => "Off duty",
                "start_log_time" => $startTime,
                "end_log_time" => $endTime,
                'vehicle_name' => "",
                'vehicle_id' => "",
                "odometer" => "",
                "location_start" => "",
                "location_end" => "",
                "engine_hour" => "",
                "is_edit_allowed" => true

            ];
        }
    }

    if ($distinctVehicles && count($distinctVehicles) == 0) {

        $distinctVehicles[] = [
            "id" => "",
            "name" => "",

        ];
    }

    $lastLogOdometer = null;

    $startLogOdometer = null;

    $diffDistance = 0;

    $engineHourFinal = 0;

    $startDateLocationName = null;

    $endDateLocationName = null;

    if ($datass && count($datass) > 0) {

        $arraylen = count($datass); // <-- add this line

        $startLog = $datass[0];
        $lastLog = $datass[$arraylen - 1];

        if ($startLog && $lastLog) {

            $startLogOdometer = $startLog["odometer"];
            $lastLogOdometer = $lastLog["odometer"];

            if ($startLogOdometer > 0 && $lastLogOdometer > 0) {
                $diffDistance = $lastLogOdometer - $startLogOdometer;
            }

            $engineHourFinal = $lastLog["engine_hour"];
            $startDateLocationName = $startLog["location_start"];
            $endDateLocationName = $lastLog["location_end"];

            $engineHourFinal = $engineHourFinal > 0 ? $engineHourFinal / 3600 : 0;
        }
    }

    $totalTimeFormatted = sprintf(
        '%02d:%02d:%02d',
        floor($totalTimeDiffInSec / 3600),
        floor(($totalTimeDiffInSec % 3600) / 60),
        $totalTimeDiffInSec % 60
    );

    return [
        $datass,
        $distinctVehicles,
        $viol,
        $totalTimeFormatted,
        $inspection,
        $diffDistance,
        $lastLogOdometer,
        $startDateLocationName,
        $endDateLocationName,
        $engineHourFinal,
    ];
}
function check_hos_mobile_log_driver_exist(
    $driverId,
    $logStartTime,
    $logEndTime,
    $currentTime,
) {

    $data = [];

    $create = Carbon::parse($logStartTime);
    $last = Carbon::parse($logEndTime);

    // Get user's timezone
    $userInfo = UserInfo::where("user_id", $driverId)->first();
    $timezone = $userInfo->home_terminal_timezone;

    $currentTime = Carbon::parse()->setTimezone($timezone)->toDateTimeLocalString();
    $currentTime = Carbon::parse($currentTime);

    $checkLog = DriverShiftLog::where("driver_id", $driverId)
        ->where("is_add_approved", 1)
        ->where(function ($query) use ($create, $last, $currentTime) {
            $query
                ->where(function ($q) use ($create, $currentTime) {
                    $q->where("start_log_time", "<", $create)
                        ->whereRaw(
                            "(CASE WHEN end_log_time IS NULL THEN ? ELSE end_log_time END) > ?",
                            [$currentTime, $create]
                        );
                })
                ->orWhere(function ($q) use ($last, $currentTime) {
                    $q->where("start_log_time", "<", $last)
                        ->whereRaw(
                            "(CASE WHEN end_log_time IS NULL THEN ? ELSE end_log_time END) > ?",
                            [$currentTime, $last]
                        );
                })
                ->orWhere(function ($q) use ($create, $currentTime) {
                    $q->where("start_log_time", ">", $create)
                        ->whereRaw(
                            "(CASE WHEN end_log_time IS NULL THEN ? ELSE end_log_time END) > ?",
                            [$currentTime, $create]
                        );
                });
        })
        ->orderBy("start_log_time", "DESC")
        ->get();

    // Find the nearest log before the create time
    $beforeLog = DriverShiftLog::where("driver_id", $driverId)
        ->where("is_add_approved", 1)
        ->where("end_log_time", "<=", $create)
        ->orderBy("end_log_time", "DESC")
        ->first();

    // Find the nearest log after the last time
    $afterLog = DriverShiftLog::where("driver_id", $driverId)
        ->where("is_add_approved", 1)
        ->where("start_log_time", ">=", $last)
        ->orderBy("start_log_time", "ASC")
        ->first();

    if ($checkLog && count($checkLog) > 0) {

        $exists = $checkLog->contains(function ($log) {
            return $log->current_shift_status == 3 &&
                $log->system_entry == 1 &&
                $log->is_add_approved == 1;
        });

        $data = [
            "exists" => true,
            "status" => $exists,
            "log" => $checkLog,
            "beforeLog" => $beforeLog,
            "afterLog" => $afterLog,
        ];

    } else {

        $data = [
            "exists" => false,
            "status" => false,
            "log" => null,
            "beforeLog" => $beforeLog,
            "afterLog" => $afterLog,
        ];

    }

    return $data;
}

function hod_log_mobile_time_data_edit(
    $driverId,
    $vehicleId,
    $shiftId,
    $logStartTime,
    $logEndTime,
    $currentTime,
    $location,
    $notes
) {
    $create = Carbon::parse($logStartTime);
    $last = Carbon::parse($logEndTime);
    $currentTime = Carbon::parse($currentTime);

    if ($create->greaterThanOrEqualTo($last)) {
        throw new \Exception('Invalid log duration.');
    }

    DB::transaction(function () use ($driverId, $vehicleId, $shiftId, $create, $last, $currentTime, $location, $notes) {

        $ruleIds = RuleAssign::where('user_id', $driverId)->pluck('rule_id');

        /*
        |--------------------------------------------------------------------------
        | Helper: recompute shift_start / cycle_start for a log after it has
        | been truncated, and persist them.
        |--------------------------------------------------------------------------
        */
        $applyShiftCycle = function ($log) use ($currentTime, $location, $ruleIds) {
            $shiftStart = 0;
            $cycleStart = 0;

            $shiftData = shift_cycle_start_check($log, $currentTime, $location, $ruleIds, 0);

            if ($shiftData) {
                $shiftStart = $shiftData[0];
                $cycleStart = $shiftData[1];
            }

            $log->update([
                'shift_start' => $shiftStart,
                'cycle_start' => $cycleStart,
            ]);
        };

        /*
        |--------------------------------------------------------------------------
        | Find logs overlapping the edited range
        |--------------------------------------------------------------------------
        */

        $baseLogQuery = DriverShiftLog::where('driver_id', $driverId)
            ->where('vehicle_id', $vehicleId);

        $existingLogs = (clone $baseLogQuery)
            ->where('start_log_time', '<', $last)
            ->where(function ($query) use ($create) {
                $query->whereNull('end_log_time')
                    ->orWhere('end_log_time', '>', $create);
            })
            ->orderBy('start_log_time', 'asc')
            ->get();

        foreach ($existingLogs as $existingLog) {

            $existingStart = Carbon::parse($existingLog->start_log_time);

            $existingEnd = $existingLog->end_log_time
                ? Carbon::parse($existingLog->end_log_time)
                : $currentTime;

            /*
            |----------------------------------------------------------------
            | CASE 1
            |
            | Existing log fully inside (or equal to) the edited range.
            |
            | Existing: 04:00 -> 06:00
            | Edited:   01:00 -> 09:00
            |
            | DELETE EXISTING
            |----------------------------------------------------------------
            */
            if ($existingStart->gte($create) && $existingEnd->lte($last)) {

                $existingLog->delete();
                continue;
            }

            /*
            |----------------------------------------------------------------
            | CASE 2
            |
            | Existing straddles the LEFT edge of the edit window:
            | existingStart < create < existingEnd <= last
            |
            | Existing: 00:00 -> 05:00
            | Edited:   03:00 -> 09:00
            |
            | Result:
            | Existing: 00:00 -> 03:00 (truncated)
            |----------------------------------------------------------------
            */
            if ($existingStart->lt($create) && $existingEnd->gt($create) && $existingEnd->lte($last)) {

                $existingLog->update([
                    'end_log_time' => $create,
                    'end_log_time_unix' => $create->timestamp,
                ]);

                $applyShiftCycle($existingLog);
                continue;
            }

            /*
            |----------------------------------------------------------------
            | CASE 3
            |
            | Existing straddles the RIGHT edge of the edit window:
            | create <= existingStart < last < existingEnd
            |
            | Existing: 06:00 -> 12:00
            | Edited:   01:00 -> 09:00
            |
            | Result:
            | Existing: 09:00 -> 12:00 (truncated)
            |----------------------------------------------------------------
            */
            if ($existingStart->gte($create) && $existingStart->lt($last) && $existingEnd->gt($last)) {

                $existingLog->update([
                    'start_log_time' => $last,
                    'start_log_time_unix' => $last->timestamp,
                ]);

                $applyShiftCycle($existingLog);
                continue;
            }

            /*
            |----------------------------------------------------------------
            | CASE 4
            |
            | Edit window is fully INSIDE the existing log:
            | existingStart < create AND last < existingEnd
            |
            | Existing: 00:00 -> 09:00
            | Edited:   03:00 -> 05:00
            |
            | Result:
            | Existing (left remainder):  00:00 -> 03:00
            | New row (right remainder):  05:00 -> 09:00
            |
            | This is the split case that was MISSING before and was the
            | main source of overlapping rows.
            |----------------------------------------------------------------
            */
            if ($existingStart->lt($create) && $existingEnd->gt($last)) {

                // Shrink existing to the left remainder
                $existingLog->update([
                    'end_log_time' => $create,
                    'end_log_time_unix' => $create->timestamp,
                ]);

                $applyShiftCycle($existingLog);

                // Create the right remainder as a brand-new row
                $rightRemainder = DriverShiftLog::create([
                    'driver_id' => $driverId,
                    'vehicle_id' => $vehicleId,
                    'current_shift_status' => $existingLog->current_shift_status,
                    'start_log_time' => $last,
                    'end_log_time' => $existingEnd,
                    'start_log_time_unix' => $last->timestamp,
                    'end_log_time_unix' => $existingEnd->timestamp,
                    'location_name' => $existingLog->location_name,
                    'location_end' => $existingLog->location_end,
                    'notes' => $existingLog->notes,
                    'system_entry' => 0,
                    'is_add_approved' => 1,
                ]);

                $applyShiftCycle($rightRemainder);
                continue;
            }

            // Any other overlap shape (shouldn't occur given the query filter,
            // but guard against it rather than silently leaving a stale row).
            if ($existingStart->lt($last) && $existingEnd->gt($create)) {
                $existingLog->delete();
            }
        }

        // Clean up any zero-length or invalid fragments produced above
        (clone $baseLogQuery)
            ->whereColumn('start_log_time', '>=', 'end_log_time')
            ->delete();

        /*
        |--------------------------------------------------------------------------
        | Check exact same range
        |--------------------------------------------------------------------------
        */

        $sameLog = (clone $baseLogQuery)
            ->where('start_log_time', $create)
            ->where('end_log_time', $last)
            ->first();

        if ($sameLog) {

            $sameLog->update([
                'current_shift_status' => $shiftId,
                'location_name' => $location,
                'location_end' => $location,
                'notes' => $notes,
                'is_add_approved' => 1,
            ]);

            $applyShiftCycle($sameLog);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Insert edited log
        |--------------------------------------------------------------------------
        */

        $newLog = DriverShiftLog::create([
            'driver_id' => $driverId,
            'vehicle_id' => $vehicleId,
            'current_shift_status' => $shiftId,
            'start_log_time' => $create,
            'end_log_time' => $last,
            'start_log_time_unix' => $create->timestamp,
            'end_log_time_unix' => $last->timestamp,
            'location_name' => $location,
            'location_end' => $location,
            'notes' => $notes,
            'system_entry' => 0,
            'is_add_approved' => 1,
        ]);

        $applyShiftCycle($newLog);

    });

    return true;
}