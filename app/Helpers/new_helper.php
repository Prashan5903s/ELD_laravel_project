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

    $result = [];

    $idCounter = 200; // Start new IDs from 200 or any unique number

    for ($i = 0; $i < count($data) - 1; $i++) {

        // Add the current log to the result

        $result[] = $data[$i];

        // Check if the end_time of the current log matches the start_time of the next log

        $currentEndTime = $data[$i]["end_log_time"];

        $nextStartTime = $data[$i + 1]["start_log_time"];

        // If there's a mismatch, insert a new log

        if ($currentEndTime !== $nextStartTime) {

            $newLog = [

                "log_id" => $data[$i]["log_id"], // Unique ID
                "shift_id" => 1, // Same status
                "log_name" => "Off duty", // Same status description
                "start_log_time" => $currentEndTime, // Start time is the end time of the current log
                "end_log_time" => $nextStartTime, // End time is the start time of the next log
                'vehicle_name' => $data[$i]["vehicle_name"], // Same location (A02),
                'vehicle_id' => $data[$i]["vehicle_id"], // Same location (A02)
            ];

            $result[] = $newLog; // Add the new log to the result

        }
    }

    // Add the last log
    if (count($data) > 0) {

        $result[] = $data[count($data) - 1];
    }

    return $result;
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
                    'vehicle_id' => $vehicle ? $vehicle->id : ""

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
                    'vehicle_id' => $datass[$arrayLen - 1]['vehicle_id']
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
                        'vehicle_id' => $datass[$arrayLen - 1]['vehicle_id']
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
                        'vehicle_id' => $datass[$arrayLen - 1]['vehicle_id']
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
                    'vehicle_id' => ""
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
                    'vehicle_id' => ""
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
                    'vehicle_id' => ""
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
                'vehicle_name' => "abc",
                'vehicle_id' => ""
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
                'vehicle_name' => "abc",
                'vehicle_id' => ""
            ];
        }
    }

    if ($distinctVehicles && count($distinctVehicles) == 0) {

        $distinctVehicles[] = [
            "id" => 1,
            "name" => "abc",

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