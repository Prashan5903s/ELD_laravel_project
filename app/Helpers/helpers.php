<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Rules;
use App\Models\Device;
use App\Models\Vehicle;
use App\Models\CoDriver;
use App\Models\UserInfo;
use Carbon\CarbonPeriod;
use App\Models\RuleAssign;
use App\Models\ListOption;
use App\Models\DriverShiftLog;
use App\Models\HOSActivityLog;
use App\Models\VehicleLogHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

//To check the ELD rules

if (!function_exists("check_eld_rules")) {

    function check_eld_rules($driver_id, $startTime = null, $endTime = null)
    {

        $userInfo = UserInfo::where("user_id", $driver_id)->first();

        $timeZone = $userInfo->home_terminal_timezone;

        //Current time of today
        $currTime = Carbon::now()->setTimezone($timeZone);

        $currentTime = conTimezone($timeZone, $currTime);

        //Data of driver shift log
        $logData = DriverShiftLog::where("driver_id", $driver_id)
            ->where("is_add_approved", 1)
            ->get();

        //This row is total shift row
        $shiftRow = DriverShiftLog::where("driver_id", $driver_id)
            ->where("shift_start", 1)
            ->where("is_add_approved", 1)
            ->get();

        //This has all the cycle row
        $cycleRow = DriverShiftLog::where("driver_id", $driver_id)
            ->where("cycle_start", 1)
            ->where("is_add_approved", 1)
            ->get();

        $logsWithViol = 0;

        //To filter data start and end date is present

        if (!is_null($startTime) && !is_null($endTime)) {

            //Format start and end date
            $startTime = Carbon::parse($startTime)->startOfDay();

            $endTime = Carbon::parse($endTime)->endOfDay();

            $create = Carbon::parse($startTime);

            $last = Carbon::parse($endTime);

            //This has all the shift row
            $shiftRow = DriverShiftLog::where("driver_id", $driver_id)
                ->where("is_add_approved", 1)
                ->where("shift_start", 1)
                ->where(function ($query) use ($create, $last, $currentTime) {
                    $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                        $subQuery
                            // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
                            ->where(function ($overlapQuery) use ($create, $last, $currentTime) {
                                $overlapQuery
                                    ->where("start_log_time", "<=", $last)
                                    ->whereRaw("IFNULL(end_log_time, ?) >= ?", [
                                        $currentTime,
                                        $create,
                                    ]);
                            })
                            // Exclude cases where $create equals end_log_time or $last equals start_log_time
                            ->whereRaw("IFNULL(end_log_time, ?) != ?", [
                                $currentTime,
                                $create,
                            ])
                            ->whereRaw("? != start_log_time", [$last]);
                    });
                })
                ->orderBy("start_log_time", "asc")
                ->get();

            //This has all the cycle row
            $cycleRow = DriverShiftLog::where("cycle_start", 1)
                ->where("driver_id", $driver_id)
                ->where("is_add_approved", 1)
                ->where(function ($query) use ($create, $last, $currentTime) {
                    $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                        $subQuery
                            // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
                            ->where(function ($overlapQuery) use ($create, $last, $currentTime) {
                                $overlapQuery
                                    ->where("start_log_time", "<=", $last)
                                    ->whereRaw("IFNULL(end_log_time, ?) >= ?", [
                                        $currentTime,
                                        $create,
                                    ]);
                            })
                            // Exclude cases where $create equals end_log_time or $last equals start_log_time
                            ->whereRaw("IFNULL(end_log_time, ?) != ?", [
                                $currentTime,
                                $create,
                            ])
                            ->whereRaw("? != start_log_time", [$last]);
                    });
                })
                ->orderBy("start_log_time", "asc")
                ->get();

            $logsArray = DriverShiftLog::where("driver_id", $driver_id)
                ->where("is_add_approved", 1)
                ->where(function ($query) use ($create, $last, $currentTime) {
                    $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                        $subQuery
                            // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
                            ->where(function ($overlapQuery) use ($create, $last, $currentTime) {
                                $overlapQuery
                                    ->where("start_log_time", "<=", $last)
                                    ->whereRaw("IFNULL(end_log_time, ?) >= ?", [
                                        $currentTime,
                                        $create,
                                    ]);
                            })
                            // Exclude cases where $create equals end_log_time or $last equals start_log_time
                            ->whereRaw("IFNULL(end_log_time, ?) != ?", [
                                $currentTime,
                                $create,
                            ])
                            ->whereRaw("? != start_log_time", [$last]);
                    });
                })
                ->orderBy("start_log_time", "asc")
                ->get();

            //If start time is only present

        } elseif (!is_null($startTime)) {

            //Format the start time

            $startTime = Carbon::parse($startTime)->startOfDay();

            //This has all the shift row

            $shiftRow = DriverShiftLog::where("driver_id", $driver_id)

                ->where("is_add_approved", 1)

                ->where("shift_start", 1)

                ->where("start_log_time", ">=", $startTime)

                ->orderBy("start_log_time", "asc")

                ->get();

            //This has all the cycle row

            $cycleRow = DriverShiftLog::where("cycle_start", 1)

                ->where("driver_id", $driver_id)

                ->where("is_add_approved", 1)

                ->where("start_log_time", ">=", $startTime)

                ->orderBy("start_log_time", "asc")

                ->get();

            $logsArray = DriverShiftLog::where("driver_id", $driver_id)

                ->where("is_add_approved", 1)

                ->where("start_log_time", ">=", $startTime)

                ->orderBy("start_log_time", "asc")

                ->get();

            //If end time is only present

        } elseif (!is_null($endTime)) {

            //Format the end date

            $endTime = Carbon::parse($endTime)->endOfDay();

            //This has all the shift row

            $shiftRow = DriverShiftLog::where("driver_id", $driver_id)

                ->where("is_add_approved", 1)

                ->where("shift_start", 1)

                ->where(

                    DB::raw('COALESCE(end_log_time, "' . $currentTime . '")'),

                    "<=",

                    $endTime

                )

                ->orderBy("start_log_time", "asc")

                ->get();

            //This has all the cycle row

            $cycleRow = DriverShiftLog::where("cycle_start", 1)

                ->where("driver_id", $driver_id)

                ->where("is_add_approved", 1)

                ->where(

                    DB::raw('COALESCE(end_log_time, "' . $currentTime . '")'),

                    "<=",

                    $endTime

                )

                ->orderBy("start_log_time", "asc")

                ->get();

            $logsArray = DriverShiftLog::where("driver_id", $driver_id)

                ->where("is_add_approved", 1)

                ->where(

                    DB::raw('COALESCE(end_log_time, "' . $currentTime . '")'),

                    "<=",

                    $endTime

                )

                ->orderBy("start_log_time", "asc")

                ->get();
        } else {

            //If start and end date not present then end date would be current time and start date would be 8 days before date and time

            $endTime = $currentTime;

            $startTime = Carbon::now()

                ->subDays(8)

                ->setTime(0, 0, 0);

            //Data of driver shift log

            $logsArray = DriverShiftLog::where("driver_id", $driver_id)

                ->where("is_add_approved", 1)

                ->where(function ($query) use ($startTime, $endTime) {

                    $query

                        ->whereBetween("start_log_time", [$startTime, $endTime])

                        ->orWhereBetween("end_log_time", [

                            $startTime,

                            $endTime,

                        ]);
                })

                ->orderBy("start_log_time", "asc")

                ->get();

            //This row is total shift row

            $shiftRow = DriverShiftLog::where("driver_id", $driver_id)

                ->where("is_add_approved", 1)

                ->where("shift_start", 1)

                ->where(function ($query) use ($startTime, $endTime) {

                    $query

                        ->whereBetween("start_log_time", [$startTime, $endTime])

                        ->orWhereBetween("end_log_time", [

                            $startTime,

                            $endTime,

                        ]);
                })

                ->orderBy("start_log_time", "asc")

                ->get();

            //This has all the cycle row

            $cycleRow = DriverShiftLog::where("cycle_start", 1)

                ->where("driver_id", $driver_id)

                ->where("is_add_approved", 1)

                ->where(function ($query) use ($startTime, $endTime) {

                    $query

                        ->whereBetween("start_log_time", [$startTime, $endTime])

                        ->orWhereBetween("end_log_time", [

                            $startTime,

                            $endTime,

                        ]);
                })

                ->orderBy("start_log_time", "asc")

                ->get();
        }

        //Last row of the log without filer
        $lastData = $logData->last();

        $totalLogCount = count($logsArray);

        //First row of the log after filter
        $firstLog = $logsArray->first();

        //Last row of the log after filter
        $lastRow = $logsArray->last();

        //Rule assign for driver
        $ruleAssign = RuleAssign::where("user_id", $driver_id)->get();

        //This is the last shift row
        $lastShift = $shiftRow->last();

        //This is the first row
        $firstShift = $shiftRow->first();

        //This is first row of the cycle
        $firstCycle = $cycleRow->first();

        //This is last row of the cycle
        $lastCycle = $cycleRow->last();

        $data = [];
        $rules = [];
        $totalShiftData = [];
        $totalCycleData = [];
        $totalDriveData = [];
        $totalEightDriveData = [];

        $totalShiftTime = 0;
        $totalCycleTime = 0;
        $totalDrive = 0;
        $totalShiftDrive = 0;


        if (
            $logsArray &&
            $ruleAssign &&
            count($ruleAssign) > 0 &&
            count($ruleAssign) > 0
        ) {

            foreach ($ruleAssign as $rule) {

                $rules = Rules::where("id", $rule->rule_id)
                    ->where("is_active", 1)
                    ->get();

                if ($rules && count($rules) > 0) {

                    foreach ($rules as $rule) {

                        if ($rule) {

                            //This is for max 14 hr of shift rule
                            if ($rule->reason == 1) {

                                //This is max for a shift
                                $maxHour = $rule->max_hour_limit;

                                if ($shiftRow && count($shiftRow) == 0) {

                                    if ($logsArray && count($logsArray) > 0) {

                                        //The log first row
                                        $rowTime = $firstLog->start_log_time;

                                        $timeData = create_end_time(
                                            $firstLog,
                                            $startTime,
                                            $lastRow,
                                            $endTime,
                                            $currentTime
                                        );

                                        $create = Carbon::parse($timeData[0]);

                                        $last = Carbon::parse($timeData[1]);

                                        $rowData = find_shift_above_time(
                                            $driver_id,
                                            $rowTime,
                                            $create,
                                            $last,
                                            $currentTime
                                        );

                                        $shiftabv = $rowData[0];

                                        $data = Shift_violation_time(
                                            $shiftabv,
                                            $lastRow,
                                            $driver_id,
                                            $startTime,
                                            $endTime,
                                            $currentTime,
                                            $firstLog,
                                            $maxHour,
                                            $totalShiftData
                                        );

                                        if (
                                            isset($data) &&
                                            isset($data[0]) &&
                                            isset($data[1])
                                        ) {

                                            $totalShiftTime += $data[1];

                                            $logsWithViol += $data[2];

                                            if (count($data[0]) > 0) {
                                                $totalShiftData = array_merge($totalShiftData, $data[0]);
                                            }
                                        }
                                    }
                                }

                                $dataCount = 0;

                                foreach ($shiftRow as $log) {

                                    if ($dataCount == 0) {

                                        //if first row is not where shift start
                                        if ($firstLog->id != $firstShift->id) {

                                            $timeData = create_end_time(
                                                $firstLog,
                                                $startTime,
                                                $firstShift,
                                                $endTime,
                                                $currentTime
                                            );

                                            $create = $timeData[0];

                                            $last = $timeData[1];

                                            $dataCount = 1;

                                            $rowTime = $firstLog->start_log_time;

                                            $belowRow = DriverShiftLog::where(
                                                "start_log_time",
                                                "<",
                                                $firstShift->start_log_time
                                            )
                                                ->where("is_add_approved", 1)
                                                ->where("driver_id", $driver_id)
                                                ->orderBy(
                                                    "start_log_time",
                                                    "desc"
                                                )
                                                ->first();

                                            $timeData = create_end_time(
                                                $firstLog,
                                                $startTime,
                                                $belowRow,
                                                $endTime,
                                                $currentTime
                                            );

                                            $create = Carbon::parse($timeData[0]);

                                            $last = Carbon::parse($timeData[1]);

                                            if ($belowRow) {

                                                $rowTime = $firstLog->start_log_time;

                                                $rowData = find_shift_above_time(
                                                    $driver_id,
                                                    $rowTime,
                                                    $create,
                                                    $last,
                                                    $currentTime
                                                );

                                                $shiftabv = $rowData[0];

                                                $data = Shift_violation_time(
                                                    $shiftabv,
                                                    $belowRow,
                                                    $driver_id,
                                                    $startTime,
                                                    $endTime,
                                                    $currentTime,
                                                    $firstLog,
                                                    $maxHour,
                                                    $totalShiftData
                                                );

                                                $totalShiftTime += $data[1];

                                                $logsWithViol += $data[2];

                                                if (count($data[0]) > 0) {

                                                    $totalShiftData = array_merge($totalShiftData, $data[0]);
                                                }
                                            }
                                        }
                                    }

                                    //This is current shift row start log time
                                    $rowTime = $log->start_log_time;

                                    //This is above row with shift above row time
                                    $aboveRow = DriverShiftLog::where(
                                        "start_log_time",
                                        ">",
                                        $log->start_log_time
                                    )
                                        ->where("is_add_approved", 1)
                                        ->where("driver_id", $driver_id)
                                        ->where("shift_start", 1)
                                        ->orderBy("start_log_time", "asc")
                                        ->first();


                                    if ($log->id == $lastShift->id) {

                                        if ($lastShift->id != $lastRow->id) {

                                            if ($lastShift->id != $lastData->id) {

                                                $timeData = create_end_time(
                                                    $log,
                                                    $startTime,
                                                    $lastRow,
                                                    $endTime,
                                                    $currentTime
                                                );

                                                $create = Carbon::parse($timeData[0]);

                                                $last = Carbon::parse($timeData[1]);

                                                $shiftabv = DriverShiftLog::where("driver_id", $driver_id)
                                                    ->where("is_add_approved", 1)
                                                    ->whereNotIn("current_shift_status", [1, 2, 5])
                                                    ->where(function ($query) use ($create, $last, $currentTime) {
                                                        $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                                                            $subQuery
                                                                // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
                                                                ->where(
                                                                    function ($overlapQuery) use ($create, $last, $currentTime) {
                                                                        $overlapQuery
                                                                            ->where(
                                                                                "start_log_time",
                                                                                "<=",
                                                                                $last
                                                                            )
                                                                            ->whereRaw(
                                                                                "IFNULL(end_log_time, ?) >= ?",
                                                                                [
                                                                                    $currentTime,
                                                                                    $create,
                                                                                ]
                                                                            );
                                                                    }
                                                                )
                                                                // Exclude cases where $create equals end_log_time or $last equals start_log_time
                                                                ->whereRaw(
                                                                    "IFNULL(end_log_time, ?) != ?",
                                                                    [
                                                                        $currentTime,
                                                                        $create,
                                                                    ]
                                                                )
                                                                ->whereRaw(
                                                                    "? != start_log_time",
                                                                    [$last]
                                                                );
                                                        });
                                                    })
                                                    ->orderBy(
                                                        "start_log_time",
                                                        "asc"
                                                    )
                                                    ->first();

                                                $data = Shift_violation_time(
                                                    $shiftabv,
                                                    $lastRow,
                                                    $driver_id,
                                                    $startTime,
                                                    $endTime,
                                                    $currentTime,
                                                    $firstLog,
                                                    $maxHour,
                                                    $totalShiftData
                                                );

                                                $totalShiftTime += $data[1];

                                                $logsWithViol += $data[2];

                                                if (count($data[0]) > 0) {

                                                    $totalShiftData = array_merge($totalShiftData, $data[0]);
                                                }
                                            }
                                        } else {

                                            $timeData = create_end_time(
                                                $lastShift,
                                                $startTime,
                                                $lastRow,
                                                $endTime,
                                                $currentTime
                                            );

                                            $create = Carbon::parse($timeData[0]);

                                            $last = Carbon::parse($timeData[1]);

                                            $shiftabv = DriverShiftLog::where(
                                                "driver_id",
                                                $driver_id
                                            )
                                                ->where("is_add_approved", 1)
                                                ->whereNotIn(
                                                    "current_shift_status",
                                                    [1, 2, 5]
                                                )
                                                ->where(function ($query) use ($create, $last, $currentTime) {
                                                    $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                                                        $subQuery
                                                            // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
                                                            ->where(function ($overlapQuery) use ($create, $last, $currentTime) {
                                                                $overlapQuery
                                                                    ->where(
                                                                        "start_log_time",
                                                                        "<=",
                                                                        $last
                                                                    )
                                                                    ->whereRaw(
                                                                        "IFNULL(end_log_time, ?) >= ?",
                                                                        [
                                                                            $currentTime,
                                                                            $create,
                                                                        ]
                                                                    );
                                                            })
                                                            // Exclude cases where $create equals end_log_time or $last equals start_log_time
                                                            ->whereRaw(
                                                                "IFNULL(end_log_time, ?) != ?",
                                                                [
                                                                    $currentTime,
                                                                    $create,
                                                                ]
                                                            )
                                                            ->whereRaw(
                                                                "? != start_log_time",
                                                                [$last]
                                                            );
                                                    });
                                                })
                                                ->orderBy(
                                                    "start_log_time",
                                                    "asc"
                                                )
                                                ->first();

                                            $data = Shift_violation_time(
                                                $shiftabv,
                                                $lastRow,
                                                $driver_id,
                                                $startTime,
                                                $endTime,
                                                $currentTime,
                                                $firstLog,
                                                $maxHour,
                                                $totalShiftData
                                            );

                                            $totalShiftTime += $data[1];

                                            $logsWithViol += $data[2];

                                            if (count($data[0]) > 0) {

                                                $totalShiftData = array_merge($totalShiftData, $data[0]);
                                            }
                                        }
                                    } else {

                                        if ($aboveRow) {

                                            $belowRow = DriverShiftLog::where(
                                                "start_log_time",
                                                "<",
                                                $aboveRow->start_log_time
                                            )
                                                ->where("is_add_approved", 1)
                                                ->where("driver_id", $driver_id)
                                                ->orderBy(
                                                    "start_log_time",
                                                    "desc"
                                                )
                                                ->first();

                                            $timeData = create_end_time(
                                                $log,
                                                $startTime,
                                                $belowRow,
                                                $endTime,
                                                $currentTime
                                            );

                                            $create = Carbon::parse($timeData[0]);

                                            $last = Carbon::parse($timeData[1]);

                                            $shiftabv = DriverShiftLog::where(
                                                "driver_id",
                                                $driver_id
                                            )
                                                ->where("is_add_approved", 1)
                                                ->whereNotIn(
                                                    "current_shift_status",
                                                    [1, 2, 5]
                                                )
                                                ->where(function ($query) use ($create, $last, $currentTime) {
                                                    $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                                                        $subQuery
                                                            // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
                                                            ->where(function ($overlapQuery) use ($create, $last, $currentTime) {
                                                                $overlapQuery
                                                                    ->where(
                                                                        "start_log_time",
                                                                        "<=",
                                                                        $last
                                                                    )
                                                                    ->whereRaw(
                                                                        "IFNULL(end_log_time, ?) >= ?",
                                                                        [
                                                                            $currentTime,
                                                                            $create,
                                                                        ]
                                                                    );
                                                            })
                                                            // Exclude cases where $create equals end_log_time or $last equals start_log_time
                                                            ->whereRaw(
                                                                "IFNULL(end_log_time, ?) != ?",
                                                                [
                                                                    $currentTime,
                                                                    $create,
                                                                ]
                                                            )
                                                            ->whereRaw(
                                                                "? != start_log_time",
                                                                [$last]
                                                            );
                                                    });
                                                })
                                                ->orderBy(
                                                    "start_log_time",
                                                    "asc"
                                                )
                                                ->first();

                                            $data = Shift_violation_time(
                                                $shiftabv,
                                                $belowRow,
                                                $driver_id,
                                                $startTime,
                                                $endTime,
                                                $currentTime,
                                                $firstLog,
                                                $maxHour,
                                                $totalShiftData
                                            );

                                            $totalShiftTime += $data[1];

                                            $logsWithViol += $data[2];

                                            if (count($data[0]) > 0) {

                                                $totalShiftData = array_merge($totalShiftData, $data[0]);
                                            }
                                        }
                                    }
                                }

                                //Max 70 hr and 60 hr of cycle for 8 days
                            } elseif (
                                $rule->reason == 2 || $rule->reason == 5
                            ) {

                                //Total hour denoted to this rule
                                $maxHour = $rule->max_hour_limit;

                                $data = cycle_calculation_dual(
                                    $cycleRow,
                                    $logsArray,
                                    $firstLog,
                                    $startTime,
                                    $lastRow,
                                    $endTime,
                                    $currentTime,
                                    $driver_id,
                                    $maxHour,
                                    $totalCycleData,
                                    $totalCycleTime,
                                    $firstCycle,
                                    $rule,
                                    $lastCycle,
                                    $lastData
                                );

                                if (count($data[0]) > 0) {

                                    $totalCycleData = array_merge($totalCycleData, $data[0]);
                                }

                                $totalCycleTime = $data[1];

                                $logsWithViol += $data[2];

                                //This rule is for 30 min break rule and 11 eleven hours drive
                            } elseif (
                                $rule->reason == 4 ||
                                $rule->reason == 3
                            ) {

                                $reasons = $rule->reason;

                                $maxDriveLimit = $rule->max_hour_limit;

                                $maxDriveLimitSec = $maxDriveLimit * 3600;

                                $dataCount = 0;

                                if (count($shiftRow) == 0) {

                                    if (count($logsArray) > 0) {

                                        $timeData = create_end_time(
                                            $firstLog,
                                            $startTime,
                                            $lastRow,
                                            $endTime,
                                            $currentTime
                                        );

                                        $create = $timeData[0];

                                        $last = $timeData[1];

                                        $rowTime = $create;

                                        $shiftAboveTime = find_shift_above_time(
                                            $driver_id,
                                            $rowTime,
                                            $create,
                                            $last,
                                            $currentTime

                                        );

                                        $create = Carbon::parse(

                                            $shiftAboveTime[1]

                                        );

                                        $driveData = DriverShiftLog::where(
                                            "driver_id",
                                            $driver_id
                                        )
                                            ->where("is_add_approved", 1)
                                            ->where("current_shift_status", 3)
                                            ->where(function ($query) use ($create, $last, $currentTime) {
                                                $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                                                    $subQuery
                                                        // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
                                                        ->where(function ($overlapQuery) use ($create, $last, $currentTime) {

                                                            $overlapQuery

                                                                ->where(

                                                                    "start_log_time",

                                                                    "<=",

                                                                    $last

                                                                )

                                                                ->whereRaw(

                                                                    "IFNULL(end_log_time, ?) >= ?",

                                                                    [

                                                                        $currentTime,

                                                                        $create,

                                                                    ]

                                                                );
                                                        })

                                                        // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                                                        ->whereRaw(

                                                            "IFNULL(end_log_time, ?) != ?",

                                                            [

                                                                $currentTime,

                                                                $create,

                                                            ]

                                                        )

                                                        ->whereRaw(

                                                            "? != start_log_time",

                                                            [$last]

                                                        );
                                                });
                                            })

                                            ->orderBy("start_log_time", "ASC")

                                            ->get();

                                        if ($reasons == 4) {

                                            $data = eight_violation_time(

                                                $driveData,

                                                $driver_id,

                                                $startTime,

                                                $endTime,

                                                $currentTime,

                                                $maxDriveLimit,

                                                $maxDriveLimitSec,

                                                $totalDriveData

                                            );

                                            $totalDrive += $data[1];

                                            $logsWithViol += $data[2];

                                            if (count($data[0]) > 0) {

                                                $totalEightDriveData[] =

                                                    $data[0];
                                            }
                                        } else {

                                            $data = eleven_violation_time(

                                                $driveData,

                                                $driver_id,

                                                $startTime,

                                                $endTime,

                                                $currentTime,

                                                $maxDriveLimit,

                                                $maxDriveLimitSec,

                                                $totalDriveData

                                            );

                                            $totalShiftDrive += $data[1];

                                            $logsWithViol += $data[2];

                                            if (count($data[0]) > 0) {

                                                $totalDriveData[] = $data[0];
                                            }
                                        }
                                    }
                                }

                                foreach ($shiftRow as $log) {

                                    //This is above row with shift above row time

                                    $aboveRow = DriverShiftLog::where(
                                        "start_log_time",
                                        ">",
                                        $log->start_log_time
                                    )
                                        ->where("is_add_approved", 1)
                                        ->where("driver_id", $driver_id)
                                        ->where("shift_start", 1)
                                        ->orderBy("start_log_time", "asc")
                                        ->first();

                                    if ($dataCount == 0) {

                                        //if first row is not where shift start
                                        if ($firstLog->id != $firstShift->id) {

                                            $belowRow = DriverShiftLog::where(

                                                "start_log_time",

                                                "<",

                                                $firstShift->start_log_time

                                            )

                                                ->where("is_add_approved", 1)

                                                ->where("driver_id", $driver_id)

                                                ->orderBy(

                                                    "start_log_time",

                                                    "desc"

                                                )

                                                ->first();

                                            $rowTime =

                                                $firstLog->start_log_time;

                                            $shiftAbv = find_shift_above_time(

                                                $driver_id,

                                                $rowTime,

                                                $create,

                                                $last,

                                                $currentTime

                                            );

                                            $create = Carbon::parse(

                                                $shiftAbv[1]

                                            );

                                            if ($belowRow) {

                                                $timeData = create_end_time(

                                                    $firstLog,

                                                    $startTime,

                                                    $belowRow,

                                                    $endTime,

                                                    $currentTime

                                                );

                                                $last = $timeData[1];

                                                $dataCount = 1;

                                                $driveData = DriverShiftLog::where(

                                                    "driver_id",

                                                    $driver_id

                                                )

                                                    ->where(

                                                        "is_add_approved",

                                                        1

                                                    )

                                                    ->where(

                                                        "current_shift_status",

                                                        3

                                                    )

                                                    ->where(function ($query) use ($create, $last, $currentTime) {

                                                        $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                                                            $subQuery

                                                                // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
    
                                                                ->where(

                                                                    function ($overlapQuery) use ($create, $last, $currentTime) {

                                                                        $overlapQuery

                                                                            ->where(

                                                                                "start_log_time",

                                                                                "<=",

                                                                                $last

                                                                            )

                                                                            ->whereRaw(

                                                                                "IFNULL(end_log_time, ?) >= ?",

                                                                                [

                                                                                    $currentTime,

                                                                                    $create,

                                                                                ]

                                                                            );
                                                                    }

                                                                )

                                                                // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                                                                ->whereRaw(

                                                                    "IFNULL(end_log_time, ?) != ?",

                                                                    [

                                                                        $currentTime,

                                                                        $create,

                                                                    ]

                                                                )

                                                                ->whereRaw(

                                                                    "? != start_log_time",

                                                                    [$last]

                                                                );
                                                        });
                                                    })

                                                    ->orderBy(

                                                        "start_log_time",

                                                        "ASC"

                                                    )

                                                    ->get();

                                                if ($reasons == 4) {

                                                    $data = eight_violation_time(

                                                        $driveData,

                                                        $driver_id,

                                                        $startTime,

                                                        $endTime,

                                                        $currentTime,

                                                        $maxDriveLimit,

                                                        $maxDriveLimitSec,

                                                        $totalDriveData

                                                    );

                                                    $totalDrive += $data[1];

                                                    $logsWithViol += $data[2];

                                                    if (count($data[0]) > 0) {

                                                        $totalEightDriveData[] =

                                                            $data[0];
                                                    }
                                                } else {

                                                    $data = eleven_violation_time(

                                                        $driveData,

                                                        $driver_id,

                                                        $startTime,

                                                        $endTime,

                                                        $currentTime,

                                                        $maxDriveLimit,

                                                        $maxDriveLimitSec,

                                                        $totalDriveData

                                                    );

                                                    $totalShiftDrive +=

                                                        $data[1];

                                                    $logsWithViol += $data[2];

                                                    if (count($data[0]) > 0) {

                                                        $totalDriveData[] =

                                                            $data[0];
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    $rowTime = $log->start_log_time;

                                    if ($log->id == $lastShift->id) {

                                        if ($lastShift->id != $lastData->id) {

                                            $timeData = create_end_time(
                                                $log,
                                                $startTime,
                                                $lastRow,
                                                $endTime,
                                                $currentTime
                                            );

                                            $create = $timeData[0];

                                            $last = $timeData[1];

                                            $driveData = DriverShiftLog::where("current_shift_status", 3)
                                                ->where("is_add_approved", 1)
                                                ->where("driver_id", $driver_id)
                                                ->where(function ($query) use ($create, $last, $currentTime) {
                                                    $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                                                        $subQuery
                                                            // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
                                                            ->where(function ($overlapQuery) use ($create, $last, $currentTime) {
                                                                $overlapQuery
                                                                    ->where(
                                                                        "start_log_time",
                                                                        "<=",
                                                                        $last
                                                                    )
                                                                    ->whereRaw(
                                                                        "IFNULL(end_log_time, ?) >= ?",
                                                                        [
                                                                            $currentTime,
                                                                            $create,
                                                                        ]
                                                                    );
                                                            })
                                                            // Exclude cases where $create equals end_log_time or $last equals start_log_time
                                                            ->whereRaw(
                                                                "IFNULL(end_log_time, ?) != ?",
                                                                [
                                                                    $currentTime,
                                                                    $create,
                                                                ]
                                                            )
                                                            ->whereRaw(
                                                                "? != start_log_time",
                                                                [$last]
                                                            );
                                                    });
                                                })
                                                ->orderBy(
                                                    "start_log_time",
                                                    "ASC"
                                                )
                                                ->get();

                                            if ($reasons == 4) {

                                                $data = eight_violation_time(

                                                    $driveData,

                                                    $driver_id,

                                                    $startTime,

                                                    $endTime,

                                                    $currentTime,

                                                    $maxDriveLimit,

                                                    $maxDriveLimitSec,

                                                    $totalDriveData

                                                );

                                                $totalDrive += $data[1];

                                                $logsWithViol += $data[2];

                                                if (count($data[0]) > 0) {

                                                    $totalEightDriveData[] = $data[0];
                                                }
                                            } else {

                                                $data = eleven_violation_time(
                                                    $driveData,
                                                    $driver_id,
                                                    $startTime,
                                                    $endTime,
                                                    $currentTime,
                                                    $maxDriveLimit,
                                                    $maxDriveLimitSec,
                                                    $totalDriveData
                                                );

                                                $totalShiftDrive += $data[1];

                                                $logsWithViol += $data[2];

                                                if (count($data[0]) > 0) {

                                                    $totalDriveData[] =

                                                        $data[0];
                                                }
                                            }
                                        } else {

                                            $timeData = create_end_time(

                                                $log,

                                                $startTime,

                                                $lastData,

                                                $endTime,

                                                $currentTime

                                            );

                                            $create = $timeData[0];

                                            $last = $timeData[1];

                                            $driveData = DriverShiftLog::where(

                                                "current_shift_status",

                                                3

                                            )

                                                ->where("is_add_approved", 1)

                                                ->where("driver_id", $driver_id)

                                                ->where(function ($query) use ($create, $last, $currentTime) {

                                                    $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                                                        $subQuery

                                                            // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
    
                                                            ->where(function ($overlapQuery) use ($create, $last, $currentTime) {

                                                                $overlapQuery

                                                                    ->where(

                                                                        "start_log_time",

                                                                        "<=",

                                                                        $last

                                                                    )

                                                                    ->whereRaw(

                                                                        "IFNULL(end_log_time, ?) >= ?",

                                                                        [

                                                                            $currentTime,

                                                                            $create,

                                                                        ]

                                                                    );
                                                            })

                                                            // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                                                            ->whereRaw(

                                                                "IFNULL(end_log_time, ?) != ?",

                                                                [

                                                                    $currentTime,

                                                                    $create,

                                                                ]

                                                            )

                                                            ->whereRaw(

                                                                "? != start_log_time",

                                                                [$last]

                                                            );
                                                    });
                                                })

                                                ->orderBy(

                                                    "start_log_time",

                                                    "ASC"

                                                )

                                                ->get();

                                            if ($reasons == 4) {

                                                $data = eight_violation_time(

                                                    $driveData,

                                                    $driver_id,

                                                    $startTime,

                                                    $endTime,

                                                    $currentTime,

                                                    $maxDriveLimit,

                                                    $maxDriveLimitSec,

                                                    $totalDriveData

                                                );

                                                $totalDrive += $data[1];

                                                $logsWithViol += $data[2];

                                                if (count($data[0]) > 0) {

                                                    $totalEightDriveData[] =

                                                        $data[0];
                                                }
                                            } else {

                                                $data = eleven_violation_time(

                                                    $driveData,

                                                    $driver_id,

                                                    $startTime,

                                                    $endTime,

                                                    $currentTime,

                                                    $maxDriveLimit,

                                                    $maxDriveLimitSec,

                                                    $totalDriveData

                                                );

                                                $totalShiftDrive += $data[1];

                                                $logsWithViol += $data[2];

                                                if (count($data[0]) > 0) {

                                                    $totalDriveData[] =

                                                        $data[0];
                                                }
                                            }
                                        }
                                    } else {

                                        if ($aboveRow) {

                                            $aboveTime = Carbon::parse(

                                                $aboveRow->start_log_time

                                            );

                                            if ($aboveTime > $endTime) {

                                                $aboveTime = $endTime;
                                            }

                                            $belowRow = DriverShiftLog::where(

                                                "start_log_time",

                                                "<",

                                                $aboveRow->start_log_time

                                            )

                                                ->where("is_add_approved", 1)

                                                ->where("driver_id", $driver_id)

                                                ->orderBy(

                                                    "start_log_time",

                                                    "desc"

                                                )

                                                ->first();

                                            $timeData = create_end_time(

                                                $log,

                                                $startTime,

                                                $belowRow,

                                                $endTime,

                                                $currentTime

                                            );

                                            $create = Carbon::parse(

                                                $timeData[0]

                                            );

                                            $last = Carbon::parse($timeData[1]);

                                            $driveData = DriverShiftLog::where(

                                                "current_shift_status",

                                                3

                                            )

                                                ->where("is_add_approved", 1)

                                                ->where("driver_id", $driver_id)

                                                ->where(function ($query) use ($create, $last, $currentTime) {

                                                    $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                                                        $subQuery

                                                            // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
    
                                                            ->where(function ($overlapQuery) use ($create, $last, $currentTime) {

                                                                $overlapQuery

                                                                    ->where(

                                                                        "start_log_time",

                                                                        "<=",

                                                                        $last

                                                                    )

                                                                    ->whereRaw(

                                                                        "IFNULL(end_log_time, ?) >= ?",

                                                                        [

                                                                            $currentTime,

                                                                            $create,

                                                                        ]

                                                                    );
                                                            })

                                                            // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                                                            ->whereRaw(

                                                                "IFNULL(end_log_time, ?) != ?",

                                                                [

                                                                    $currentTime,

                                                                    $create,

                                                                ]

                                                            )

                                                            ->whereRaw(

                                                                "? != start_log_time",

                                                                [$last]

                                                            );
                                                    });
                                                })

                                                ->orderBy(

                                                    "start_log_time",

                                                    "ASC"

                                                )

                                                ->get();

                                            if ($reasons == 4) {

                                                $data = eight_violation_time(

                                                    $driveData,

                                                    $driver_id,

                                                    $startTime,

                                                    $endTime,

                                                    $currentTime,

                                                    $maxDriveLimit,

                                                    $maxDriveLimitSec,

                                                    $totalDriveData

                                                );

                                                $totalDrive += $data[1];

                                                $logsWithViol += $data[2];

                                                if (count($data[0]) > 0) {

                                                    $totalEightDriveData[] =

                                                        $data[0];
                                                }
                                            } else {

                                                $data = eleven_violation_time(

                                                    $driveData,

                                                    $driver_id,

                                                    $startTime,

                                                    $endTime,

                                                    $currentTime,

                                                    $maxDriveLimit,

                                                    $maxDriveLimitSec,

                                                    $totalDriveData

                                                );

                                                // $totalShiftDrive += $data[1];

                                                // $logsWithViol += $data[2];

                                                // if (count($data[0]) > 0) {

                                                //     $totalDriveData[] =

                                                //         $data[0];
                                                // }
                                            }
                                        }
                                    }
                                }

                                if ($reasons == 4) {

                                    $totalEightDriveData = array_merge(

                                        ...$totalEightDriveData

                                    );
                                } else {

                                    if (count($totalDriveData) > 0) {

                                        $totalDriveData = [

                                            array_merge(...$totalDriveData),

                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $totalShift = secondsToTime($totalShiftTime);

        $totalCycle = secondsToTime($totalCycleTime);

        $totalDriveShift = secondsToTime($totalShiftDrive);

        $totalDriveTimess = secondsToTime($totalDrive);

        $totalDriveShift = $totalDriveTimess;

        $totalDriveData = array_merge(...$totalDriveData);

        $data = [

            "Shift_data" => $totalShiftData,

            "total_shift_time" => $totalShift,

            "cycle_data" => $totalCycleData,

            "total_cycle_time" => $totalCycle,

            "eight_hour_break_violation" => $totalEightDriveData,

            "total_drive_time" => $totalDriveTimess,

            "driver_eleven_viol_data" => $totalDriveData,

            "total_drive_shift_time" => $totalDriveShift,

            "total_log_count" => $totalLogCount,

        ];

        return $data;
    }
}

function formatTime($time)
{

    $dateTime = new DateTime($time);

    return $dateTime->format("d/m/Y h:i A");
}

function conTimezone($timezone, $time)
{

    // Parse the given time string and set it to the specified timezone

    $convertedTime = Carbon::parse($time)->setTimezone($timezone);

    return $convertedTime->toDateTimeString();
}

function secondsToTime($seconds)
{

    $hours = floor($seconds / 3600);

    $minutes = floor(($seconds % 3600) / 60);

    $seconds = $seconds % 60;

    return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
}

//This is used to select start and end time of the log
function create_end_time(

    $startRow,

    $startTime,

    $lastRow,

    $endTime,

    $currentTime

) {

    $create = $startRow->start_log_time;

    if ($create < $startTime) {

        $create = $startTime;
    }

    $last = null;

    if ($lastRow !== null) {

        $last = $lastRow->end_log_time;
    }

    if ($last === null) {

        $last = $currentTime;

        if ($last > $endTime) {

            $last = $endTime;
        }
    } else {

        if ($last > $endTime) {

            $last = $endTime;
        } elseif ($last < $startTime) {

            $last = $startTime;
        } elseif ($create > $endTime) {

            $create = $endTime;
        }
    }

    return [$create, $last];
}

function safety_score_report_calculation($userId, $start, $end)
{

    $safetyScoreArray = [];

    $safetyData = safety_driver_score_calculation($userId, $start, $end);

    if ($safetyData && count($safetyData) > 0) {

        foreach ($safetyData as $value) {

            $name = $value[0];

            $safetyScore = $value[1];

            $performance = getScoreCategory($safetyScore);

            $driverId = $value[3];

            $startDay = $value[4];

            $endDay = $value[5];

            $dataSet = $value[2];

            $haCount = $dataSet["count_hard_accel"];

            $hbCount = $dataSet["count_hard_brake"];

            $htCount = $dataSet["count_hard_turn"];

            $hsCount = $dataSet["count_hard_stop"];

            $speedingCount = $dataSet["count_speed"];

            $HAPoint = $dataSet["hard_accel_point"];

            $HBPoint = $dataSet["hard_brake_point"];

            $HSPoint = $dataSet["hard_stop_point"];

            $HTPoint = $dataSet["hard_turn_point"];

            $SPDPoint = $dataSet["speed_point"];

            $HAImpact = $dataSet["hard_accel_impact"];

            $HBImpact = $dataSet["hard_brake_impact"];

            $HSImpact = $dataSet["hard_stop_impact"];

            $HTImpact = $dataSet["hard_turn_impact"];

            $SPDImpact = $dataSet["speed_impact"];

            $totalMile = $dataSet["total_mile"];

            $maxHAPoint = $dataSet["max_hard_accel_point"];

            $maxHBPoint = $dataSet["max_hard_brake_point"];

            $maxHSPoint = $dataSet["max_hard_stop_point"];

            $maxHTPoint = $dataSet["max_hard_turn_points"];

            $maxSPDPoint = $dataSet["max_speed_points"];

            $HAEPM = avg_type_event_per_miles(

                $driverId,

                $startDay,

                $endDay,

                "HARDACCEL"

            );

            $HBEPM = avg_type_event_per_miles(

                $driverId,

                $startDay,

                $endDay,

                "HARDBRAKE"

            );

            $HSEPM = avg_type_event_per_miles(

                $driverId,

                $startDay,

                $endDay,

                "HARDSTOP"

            );

            $HTEPM = avg_type_event_per_miles(

                $driverId,

                $startDay,

                $endDay,

                "HARDTURN"

            );

            $SPDEPM = avg_type_event_per_miles(

                $driverId,

                $startDay,

                $endDay,

                "SPEEDING"

            );

            $safetyScoreArray[] = [

                "name" => $name,

                "driver_id" => $driverId,

                "startTime" => $startDay,

                "endTime" => $endDay,

                "safety_score" => $safetyScore,

                "performance" => $performance,

                "hard_accel_count" => $haCount,

                "hard_brake_count" => $hbCount,

                "hard_turn_count" => $htCount,

                "hard_stop_count" => $hsCount,

                "speeding_count" => $speedingCount,

                "hard_accel_point" => $HAPoint,

                "hard_brake_point" => $HBPoint,

                "hard_turn_point" => $HTPoint,

                "hard_stop_point" => $HSPoint,

                "speed_point" => $SPDPoint,

                "max_hard_accel_point" => $maxHAPoint,

                "max_hard_brake_point" => $maxHBPoint,

                "max_hard_turn_point" => $maxHTPoint,

                "max_hard_stop_point" => $maxHSPoint,

                "max_speed_point" => $maxSPDPoint,

                "hard_accel_impact" => -$HAImpact,

                "hard_brake_impact" => -$HBImpact,

                "hard_turn_impact" => -$HTImpact,

                "hard_stop_impact" => -$HSImpact,

                "speeding_impact" => -$SPDImpact,

                "total_mile" => $totalMile,

                "ha_event_per_mile" => $HAEPM,

                "hb_event_per_mile" => $HBEPM,

                "ht_event_per_mile" => $HTEPM,

                "hs_event_per_mile" => $HSEPM,

                "speed_event_per_mile" => $SPDEPM,

            ];
        }
    }

    // Return the safety scores for all drivers

    return $safetyScoreArray;
}

//This is function is used for shift violation
function Shift_violation_time(
    $shiftabv,
    $lastRow,
    $driver_id,
    $startTime,
    $endTime,
    $currentTime,
    $firstLog,
    $maxHour,
    $totalShiftData
) {

    $totalTime = 0;

    $logCount = 0;

    $secLimit = $maxHour * 3600;

    $totalShiftData = [];

    if ($shiftabv) {

        $timeData = create_end_time(
            $shiftabv,
            $startTime,
            $lastRow,
            $endTime,
            $currentTime
        );

        $create = Carbon::parse($shiftabv->start_log_time);

        $last = Carbon::parse($timeData[1]);

        $logShft = DriverShiftLog::where("driver_id", $driver_id)
            ->where("is_add_approved", 1)
            ->whereNotIn("current_shift_status", [1, 2, 5])
            ->where(function ($query) use ($create, $last, $currentTime) {
                $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                    $subQuery
                        // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
                        ->where(function ($overlapQuery) use ($create, $last, $currentTime) {
                            $overlapQuery
                                ->where("start_log_time", "<=", $last)
                                ->whereRaw("IFNULL(end_log_time, ?) >= ?", [
                                    $currentTime,
                                    $create,
                                ]);
                        })
                        // Exclude cases where $create equals end_log_time or $last equals start_log_time
                        ->whereRaw("IFNULL(end_log_time, ?) != ?", [
                            $currentTime,
                            $create,
                        ])
                        ->whereRaw("? != start_log_time", [$last]);
                });
            })
            ->orderBy("start_log_time", "ASC")
            ->get();

        if ($logShft && count($logShft) > 0) {

            $dataCount = 0;

            $totalShiftTime = 0;

            foreach ($logShft as $log) {

                $logId = $log->id;

                $timeData = create_end_time(
                    $log,
                    $startTime,
                    $log,
                    $endTime,
                    $currentTime
                );

                $create = Carbon::parse($timeData[0]);

                $last = Carbon::parse($timeData[1]);

                $timeDuration = $last->diffInSeconds($create);

                $totalTime += $timeDuration;

                $logStartTime = $log->start_log_time;

                $logStartTime = Carbon::parse($logStartTime);

                $logEndTime = $log->end_log_time;

                $logEndTime = check_end_log_time(
                    $logEndTime,
                    $currentTime,
                    $endTime
                );

                $logEndTime = Carbon::parse($logEndTime);

                $timeActualLeft = $logEndTime->diffInSeconds($logStartTime);

                $totalShiftTime += $timeActualLeft;

                if ($timeDuration > 0) {

                    if ($totalShiftTime > $secLimit) {

                        $logCount += 1;

                        if ($dataCount == 0) {

                            $time_left = $totalShiftTime - $secLimit;

                            if ($time_left > 0) {

                                $violtionStartTime = $last->copy()->subSeconds($time_left);

                                if ($violtionStartTime < $startTime) {

                                    $violtionStartTime = $startTime;
                                }

                                if ($last > $endTime) {

                                    $last = $endTime;
                                }

                                $viol_dur = $last->diffInSeconds($violtionStartTime);

                                if ($viol_dur > 0) {

                                    $finalLogData = log_violation_time_fix(
                                        $driver_id,
                                        $logId,
                                        $violtionStartTime,
                                        $last,
                                        $currentTime,
                                        $viol_dur,
                                        1
                                    );

                                    if (!empty($finalLogData)) {
                                        $totalShiftData = array_merge($totalShiftData, $finalLogData);
                                    }
                                }
                            }

                            $dataCount = 1;
                        } else {

                            $totalShiftData[] = [
                                "shift_id" => $logId,
                                "violation_duration" => secondsToTime($timeDuration),
                                "violation_startTime" => Carbon::parse($create),
                                "violation_endTime" => Carbon::parse($last),
                            ];
                        }
                    }
                }
            }
        }
    }

    return [$totalShiftData, $totalTime, $logCount];
}

//This function is used to calculate cycle violation for both 70 and 60 cycle hour
function cycle_violation_time(
    $cycleabv,
    $startTime,
    $endTime,
    $currentTime,
    $driver_id,
    $maxHour,
    $firstLog,
    $lastRow,
    $totalCycleData
) {

    $totalCycleTime = 0;

    $logCount = 0;

    $secLimit = $maxHour * 3600;

    $totalCycleData = [];

    if ($cycleabv) {

        $timeData = create_end_time(
            $cycleabv,
            $startTime,
            $lastRow,
            $endTime,
            $currentTime
        );

        $create = Carbon::parse($cycleabv->start_log_time);

        $last = Carbon::parse($timeData[1]);

        $dataCycl = DriverShiftLog::where("driver_id", $driver_id)
            ->where("is_add_approved", 1)
            ->whereNotIn("current_shift_status", [1, 2, 5])
            ->where(function ($query) use ($create, $last, $currentTime) {
                $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                    $subQuery
                        // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
                        ->where(function ($overlapQuery) use ($create, $last, $currentTime) {
                            $overlapQuery
                                ->where("start_log_time", "<=", $last)
                                ->whereRaw("IFNULL(end_log_time, ?) >= ?", [
                                    $currentTime,
                                    $create,
                                ]);
                        })
                        // Exclude cases where $create equals end_log_time or $last equals start_log_time
                        ->whereRaw("IFNULL(end_log_time, ?) != ?", [
                            $currentTime,
                            $create,
                        ])
                        ->whereRaw("? != start_log_time", [$last]);
                });
            })
            ->orderBy("start_log_time", "asc")
            ->get();

        if ($dataCycl && count($dataCycl) > 0) {

            $totalTime = 0;

            $dataCount = 0;

            foreach ($dataCycl as $log) {

                $logId = $log->id;

                $timeData = create_end_time(
                    $log,
                    $startTime,
                    $log,
                    $endTime,
                    $currentTime
                );

                $create = Carbon::parse($timeData[0]);

                $last = Carbon::parse($timeData[1]);

                $timeDuration = $last->diffInSeconds($create);

                $totalCycleTime += $timeDuration;

                $cycleStartTime = $log->start_log_time;

                $cycleStartTime = Carbon::parse($cycleStartTime);

                $cycleEndTime = $log->end_log_time;

                $cycleEndTime = check_end_log_time(
                    $cycleEndTime,
                    $currentTime,
                    $endTime
                );

                $timeActualLeft = $cycleEndTime->diffInSeconds($cycleStartTime);

                $totalTime += $timeActualLeft;

                if ($timeDuration > 0) {

                    if ($totalTime > $secLimit) {

                        $logCount += 1;

                        if ($dataCount == 0) {

                            $time_left = $totalTime - $secLimit;

                            if ($time_left > 0) {

                                $violtionStartTime = $last->copy()->subSeconds($time_left);

                                if ($violtionStartTime < $startTime) {

                                    $violtionStartTime = $startTime;
                                }

                                if ($last > $endTime) {

                                    $last = $endTime;
                                }

                                $viol_dur = $last->diffInSeconds($violtionStartTime);

                                if ($viol_dur > 0) {

                                    $finalLogData = log_violation_time_fix(
                                        $driver_id,
                                        $logId,
                                        $violtionStartTime,
                                        $last,
                                        $currentTime,
                                        $viol_dur,
                                        2
                                    );

                                    if (!empty($finalLogData)) {
                                        $totalCycleData = array_merge($totalCycleData, $finalLogData);
                                    }
                                }
                            }

                            $dataCount = 1;
                        } else {

                            $totalCycleData[] = [
                                "shift_id" => $logId,
                                "violation_duration" => secondsToTime($timeDuration),
                                "violation_startTime" => Carbon::parse($create),
                                "violation_endTime" => Carbon::parse($last),
                            ];
                        }
                    }
                }
            }
        }
    }

    return [$totalCycleData, $totalCycleTime, $logCount];
}

//Function to calculate Eight hours violation rule
function eight_violation_time(
    $driveData,
    $driver_id,
    $startTime,
    $endTime,
    $currentTime,
    $maxDriveLimit,
    $maxDriveLimitSec,
    $totalDriveData
) {

    $driveTotal = 0;

    $logCount = 0;

    $totalDriveData = [];

    if ($driveData && count($driveData) > 0) {

        $driveFirstData = $driveData->first();

        $logExist = check_above_driving_log($driveFirstData, $driver_id);

        // Make sure both are arrays before merging
        $mergedLogs = array_merge($logExist, $driveData->all());

        // Use collection to remove duplicates by 'id' and sort by 'start_log_time'
        $finalLogs = collect($mergedLogs)
            ->unique("id")
            ->sortBy("start_log_time")
            ->values()
            ->all();

        if ($finalLogs && count($finalLogs) > 0) {

            foreach ($finalLogs as $log) {

                $logId = $log->id;

                $driverId = $log->driver_id;

                $timeData = create_end_time(
                    $log,
                    $startTime,
                    $log,
                    $endTime,
                    $currentTime
                );

                $create = Carbon::parse($timeData[0]);

                $last = Carbon::parse($timeData[1]);

                $timeDuration = $last->diffInSeconds($create);

                $driveTotal += $timeDuration;

                $cycleStartTime = $log->start_log_time;

                $cycleStartTime = Carbon::parse($cycleStartTime);

                $check = check_above_driver_log_exist(
                    $driverId,
                    $cycleStartTime,
                    $currentTime,
                    $startTime,
                    $endTime

                );

                $cycleEndTime = $log->end_log_time;

                if (!$check) {

                    $driverLogData = check_lower_driving_log(
                        $driverId,
                        $cycleEndTime,
                        $currentTime,
                        $endTime
                    );

                    $cycleEndTime = check_end_log_time(
                        $cycleEndTime,
                        $currentTime,
                        $endTime
                    );

                    $cycleEndTime = Carbon::parse($cycleEndTime);

                    $timeActualLeft = $cycleEndTime->diffInSeconds($cycleStartTime);

                    if (
                        $driverLogData &&
                        count($driverLogData) > 0 &&
                        $driverLogData[1]
                    ) {

                        $timeActualLeft += $driverLogData[0];

                        $last = Carbon::parse($driverLogData[2]);
                    }

                    if (
                        $timeActualLeft > 0 &&
                        ($startTime <= $last && $last <= $endTime)
                    ) {

                        if ($timeActualLeft > $maxDriveLimitSec) {

                            $logCount += 1;

                            $time_left = $timeActualLeft - $maxDriveLimitSec;

                            if ($time_left > 0) {

                                $violtionStartTime = $last->copy()->subSeconds($time_left);

                                if ($violtionStartTime < $startTime) {
                                    $violtionStartTime = $startTime;
                                }

                                if ($last > $endTime) {
                                    $last = $endTime;
                                }

                                $viol_dur = $last->diffInSeconds($violtionStartTime);

                                if ($viol_dur > 0) {

                                    $totalDriveData[] = [
                                        "id_log" => $logId,
                                        "break_violation" => secondsToTime($viol_dur),
                                        "violation_start_time" => Carbon::parse($violtionStartTime),
                                        "violation_end_date" => Carbon::parse($last),
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return [$totalDriveData, $driveTotal, $logCount];
}

function eleven_violation_time(
    $driveData,
    $driver_id,
    $startTime,
    $endTime,
    $currentTime,
    $maxDriveLimit,
    $maxDriveLimitSec,
    $totalDriveData
) {

    $driveTotal = 0;

    $logCount = 0;

    $totalDriveData = [];

    if ($driveData && count($driveData) > 0) {

        $dataCount = 0;

        $totalDriveTime = 0;

        foreach ($driveData as $log) {

            $logId = $log->id;

            $timeData = create_end_time(
                $log,
                $startTime,
                $log,
                $endTime,
                $currentTime
            );

            $create = Carbon::parse($timeData[0]);

            $last = Carbon::parse($timeData[1]);

            $timeDuration = $last->diffInSeconds($create);

            $driveTotal += $timeDuration;

            $cycleStartTime = $log->start_log_time;

            $cycleStartTime = Carbon::parse($cycleStartTime);

            $cycleEndTime = $log->end_log_time;

            $cycleEndTime = check_end_log_time(
                $cycleEndTime,
                $currentTime,
                $endTime
            );

            $cycleEndTime = Carbon::parse($cycleEndTime);

            $timeActualLeft = $cycleEndTime->diffInSeconds($cycleStartTime);

            $totalDriveTime += $timeActualLeft;

            if ($timeDuration > 0) {

                if ($totalDriveTime > $maxDriveLimitSec) {

                    $logCount += 1;

                    if ($dataCount == 0) {

                        $time_left = $totalDriveTime - $maxDriveLimitSec;


                        if ($time_left > 0) {

                            $violtionStartTime = $last->copy()->subSeconds($time_left);

                            if ($violtionStartTime < $startTime) {
                                $violtionStartTime = $startTime;
                            }

                            if ($last > $endTime) {
                                $last = $endTime;
                            }

                            $viol_dur = $last->diffInSeconds($violtionStartTime);

                            if ($viol_dur > 0) {

                                $finalLogData = log_violation_time_fix(
                                    $driver_id,
                                    $logId,
                                    $violtionStartTime,
                                    $last,
                                    $currentTime,
                                    $viol_dur,
                                    3
                                );

                                if (!empty($finalLogData)) {
                                    $totalDriveData = array_merge($totalDriveData, $finalLogData);
                                }
                            }
                        }

                        $dataCount = 1;
                    } else {

                        $totalDriveData[] = [
                            "driver_id" => $logId,
                            "drive_violate" => secondsToTime($timeDuration),
                            "drive_start_time" => Carbon::parse($create),
                            "drive_end_time" => Carbon::parse($last),
                        ];
                    }
                }
            }
        }
    }

    return [$totalDriveData, $driveTotal, $logCount];
}

function driver_log_time($id, $time)
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

        $curretLog = $log;

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
        is_null($curretLog) ? "Off duty" : $curretLog,
        $violCycleTime,
        $violDriveTime,
        $violBreakTime,
        $shiftLog,
        $timeSet,
    ];

    return $data;
}

function driver_log_time_data($id, $time)
{

    $timeFormat = Carbon::parse($time)->format("Y-m-d");

    $activityLog = HOSActivityLog::where("timeData", $timeFormat)

        ->where("user_id", $id)

        ->first();

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

    $userInfo = UserInfo::where("user_id", $id)

        ->with(

            "homeTerminal:id,name,address,type,shapeData,latitude,longitude,radius,tags,notes,status"

        )

        ->first();

    if ($activityLog) {

        $driver = json_decode(

            json_encode([

                "id" => $id,

                "first_name" => $activityLog->first_name,

                "last_name" => $activityLog->last_name,

                "email" => $user->email,

                "mobile_no" => $user->mobile_no,

                "pincode" => $user->pincode,

                "address" => $user->address,

                "timezone" => $user->timezone,

                "avatar_image" => $user->avatar_image,

                "language_id" => $user->language_id,

                "is_active" => $user->is_active,

                "language" => $user->language,

            ])

        );

        $userInfo = json_decode(

            json_encode([

                "id" => $userInfo->id,

                "user_id" => $userInfo->user_id,

                "fleet_user_id" => $userInfo->fleet_user_id,

                "language_id" => $userInfo->language_id,

                "driver_id" => $userInfo->driver_id,

                "cargo_type_id" => $userInfo->cargo_type_id,

                "licenseNumber" => $userInfo->licenseNumber,

                "username" => $userInfo->username,

                "note" =>

                    $activityLog && $activityLog != "null"

                    ? $activityLog->notes

                    : $userInfo->note,

                "driver_license_state" => $userInfo->driver_license_state,

                "home_terminal_timezone" => $userInfo->home_terminal_timezone,

                "career_name" => $activityLog->cariier_name,

                "main_office_address" => $activityLog->main_office_address,

                "carrer_us_dot_number" => $userInfo->carrer_us_dot_number,

                "home_terminal_name" => $userInfo->home_terminal_name,

                "home_terminal_address" => $userInfo->home_terminal_address,

                "updated_at" => $userInfo->updated_at,

                "created_at" => $userInfo->created_at,

                "home_terminal" => [

                    "id" => $userInfo->homeTerminal->id,

                    "name" => $userInfo->homeTerminal->name,

                    "address" => $activityLog->home_terminal_address,

                    "type" => $userInfo->homeTerminal->type,

                    "shapeData" => $userInfo->homeTerminal->shapeData,

                    "latitude" => $userInfo->homeTerminal->latitude,

                    "longitude" => $userInfo->homeTerminal->longitude,

                    "radius" => $userInfo->homeTerminal->radius,

                    "tags" => $userInfo->homeTerminal->tags,

                    "notes" => $userInfo->homeTerminal->notes,

                    "status" => $userInfo->homeTerminal->status,

                ],

            ])

        );
    } else {

        $driver = $user;
    }

    $timeZone = $userInfo->home_terminal_timezone;

    //Current time of today

    $currTime = Carbon::now()

        ->setTimezone($timeZone)

        ->toDateTimeLocalString();

    $currTime = Carbon::parse($currTime);

    $currentTime = $currTime;

    $timeSet = Carbon::parse($currTime)->format("y-m-d");

    $startTime = Carbon::parse($time)->startOfDay(); // Start time of the day

    $endTime = Carbon::parse($time)->endOfDay();

    $create = $startTime;

    $last = $endTime;

    $ruleAssgn = RuleAssign::where("user_id", $id)->get();

    $latestLog = DriverShiftLog::where("driver_id", $id)
        ->where("is_add_approved", 1)
        ->latest("start_log_time")
        ->first();

    $logsData = DriverShiftLog::where("driver_id", $id)

        ->where("is_add_approved", 1)

        ->where(function ($query) use ($create, $last, $currentTime) {

            $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                // Check if there's overlap between the time range and log times, excluding exact matches with $create and $last
    
                $subQuery->where(function ($q) use ($create, $last, $currentTime) {

                    $q->where("start_log_time", ">=", $create)

                        ->where("start_log_time", "<=", $last)

                        // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                        ->whereRaw("? != start_log_time", [$last])

                        ->whereRaw("IFNULL(end_log_time, ?) != ?", [

                            $currentTime,

                            $create,

                        ])

                        ->orWhere(function ($query) use ($create, $last, $currentTime) {

                            $query

                                ->whereRaw("IFNULL(end_log_time, ?) >= ?", [

                                    $currentTime,

                                    $create,

                                ])

                                ->whereRaw("IFNULL(end_log_time, ?) <= ?", [

                                    $currentTime,

                                    $last,

                                ])

                                // Exclude cases where $create equals end_log_time or $last equals start_log_time
        
                                ->whereRaw("? != start_log_time", [$last])

                                ->whereRaw("IFNULL(end_log_time, ?) != ?", [

                                    $currentTime,

                                    $create,

                                ]);
                        });
                });
            });
        })

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

        ->orderBy("start_log_time", "asc")

        ->get();

    $lastShiftStartOne = $logsData->where("shift_start", 1)->last();

    // Filter data: include everything after the last shift_start = 1 record, or all records if none found

    $filteredLogs = $logsData->filter(function ($log) use ($lastShiftStartOne) {

        return !$lastShiftStartOne ||

            $log->start_log_time >= $lastShiftStartOne->start_log_time;
    });

    $lastCycleStartOne = $logsData->where("cycle_start", 1)->last();

    // Filter data: include everything after the last shift_start = 1 record, or all records if none found

    $filteredCycleLogs = $logsData->filter(function ($log) use ($lastCycleStartOne) {

        return !$lastCycleStartOne ||

            $log->start_log_time >= $lastCycleStartOne->start_log_time;
    });

    $vehicles = null;

    if ($latestLog) {

        $vehicleId = $latestLog->vehicle_id;

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

        $log = ListOption::where("list_id", "driving_status")

            ->where("option_id", $latestLog->current_shift_status)

            ->pluck("title")

            ->first();

        $curretLog = $log;

        $logTimeData = create_end_time(

            $latestLog,

            $startTime,

            $latestLog,

            $endTime,

            $currentTime

        );

        $rowTime = $latestLog->start_log_time;

        $aboveTime = $logTimeData[1];

        $latestInSec = 0;

        if (!is_null($aboveTime) && !is_null($rowTime)) {

            $aboveTime = Carbon::parse($aboveTime);

            $rowTime = Carbon::parse($rowTime);

            $latestInSec = $aboveTime->diffInSeconds($rowTime);
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

                foreach ($filteredLogs as $logShift) {

                    $status = $logShift->current_shift_status;

                    $log_time = create_end_time(

                        $logShift,

                        $startTime,

                        $logShift,

                        $endTime,

                        $currentTime

                    );

                    $logShiftStart = Carbon::parse($log_time[0]);

                    $logShiftEnd = Carbon::parse($log_time[1]);

                    if ($status != 1 && $status != 2 && $status != 5) {

                        $shiftTimeSeconds += $logShiftEnd->diffInSeconds(

                            $logShiftStart

                        );
                    }
                }

                if ($maxHrSeconds > $shiftTimeSeconds) {

                    $shiftViolTime = $maxHrSeconds - $shiftTimeSeconds;

                    $ViolShift = secondsToTime($shiftViolTime);
                } else {

                    $ViolShift = "00:00:00";
                }
            } elseif ($rule->reason == 5) {

                $maxHr = $rule->max_hour_limit;

                // Convert $maxHr to seconds

                $maxHrSeconds = $maxHr * 3600;

                $cycleTimeSeconds = 0;

                foreach ($filteredCycleLogs as $log) {

                    $status = $log->current_shift_status;

                    $log_time = create_end_time(

                        $log,

                        $startTime,

                        $log,

                        $endTime,

                        $currentTime

                    );

                    $logCycleStart = Carbon::parse($log_time[0]);

                    $logCycleEnd = Carbon::parse($log_time[1]);

                    if ($status != 1 && $status != 2 && $status != 5) {

                        $cycleTimeSeconds += $logCycleEnd->diffInSeconds(

                            $logCycleStart

                        );
                    }
                }

                if ($maxHrSeconds > $cycleTimeSeconds) {

                    $violCycleTime = $maxHrSeconds - $cycleTimeSeconds;

                    $violCycleTime = secondsToTime($violCycleTime);
                } else {

                    $violCycleTime = "00:00:00";
                }
            } elseif ($rule->reason == 2) {

                $maxHr = $rule->max_hour_limit;

                // Convert $maxHr to seconds

                $maxHrSeconds = $maxHr * 3600;

                $cycleTimeSeconds = 0;

                foreach ($filteredCycleLogs as $log) {

                    $status = $log->current_shift_status;

                    $log_time = create_end_time(

                        $log,

                        $startTime,

                        $log,

                        $endTime,

                        $currentTime

                    );

                    $logCycleStart = Carbon::parse($log_time[0]);

                    $logCycleEnd = Carbon::parse($log_time[1]);

                    if ($status != 1 && $status != 2 && $status != 5) {

                        $cycleTimeSeconds += $logCycleEnd->diffInSeconds(

                            $logCycleStart

                        );
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

                foreach ($filteredLogs as $log) {

                    $status = $log->current_shift_status;

                    $log_time = create_end_time(

                        $log,

                        $startTime,

                        $log,

                        $endTime,

                        $currentTime

                    );

                    $logDriveStart = Carbon::parse($log_time[0]);

                    $logDriveEnd = Carbon::parse($log_time[1]);

                    if ($status == 3) {

                        $driveTimeSeconds += $logDriveEnd->diffInSeconds(

                            $logDriveStart

                        );
                    }
                }

                if ($maxHrSeconds > $driveTimeSeconds) {

                    $violDriveTimes = $maxHrSeconds - $driveTimeSeconds;

                    $violDriveTime = secondsToTime($violDriveTimes);
                } else {

                    $violDriveTime = "00:00:00";
                }
            } elseif ($rule->reason == 4) {

                $create = $startTime;

                $last = $endTime;

                $totalCountDrive = 0;

                if ($latestLog) {

                    if ($latestLog->current_shift_status == 3) {

                        $timeSlotData = create_end_time(

                            $latestLog,

                            $startTime,

                            $latestLog,

                            $endTime,

                            $currentTime

                        );

                        $breakStartTime = Carbon::parse($timeSlotData[0]);

                        $breakEndTime = Carbon::parse($timeSlotData[1]);

                        $totalCountDrive = $breakEndTime->diffInSeconds(

                            $breakStartTime

                        );
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
        $latestDiffTime,
        $userInfo,
        $ViolShift,
        $curretLog,
        $violCycleTime,
        $violDriveTime,
        $violBreakTime,
        $logsData,
        $timeSet,
    ];

    return $data;
}

function driver_log_time_st_et($id, $time)
{

    $shiftTime = 0;

    $cycleTime = 0;

    $driveTime = 0;

    $shiftViolTime = 0;

    $ViolShift = null;

    $curretLog = null;

    $vehicle = null;

    $driver = null;

    $vehicleLog = null;

    $userInfo = null;

    $violCycleTime = null;

    $latestDiffTime = null;

    $violDriveTime = null;

    $violBreakTime = null;

    $userInfo = UserInfo::where("user_id", $id)->first();

    $timeZone = $userInfo->home_terminal_timezone;

    // Get the current time in the user's timezone

    $currentTime = Carbon::now()->setTimezone($timeZone);

    // If you want to format the time, you can do so:

    $formattedTime = $currentTime->format("Y-m-d H:i:s");

    $currentTime = Carbon::parse($formattedTime);

    $ruleAssgn = RuleAssign::where("user_id", $id)->get();

    $create = Carbon::parse($time)->startOfDay();

    $last = Carbon::parse($time)->endOfDay(); // Ensure the end time includes the whole day

    $logsData = DriverShiftLog::where("driver_id", $id)

        ->where("is_add_approved", 1)

        ->where(function ($query) use ($create, $last, $currentTime) {

            $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                // Check if there's overlap between the time range and log times, excluding exact matches with $create and $last
    
                $subQuery->where(function ($q) use ($create, $last, $currentTime) {

                    $q->where("start_log_time", ">=", $create)

                        ->where("start_log_time", "<=", $last)

                        // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                        ->whereRaw("? != start_log_time", [$last])

                        ->whereRaw("IFNULL(end_log_time, ?) != ?", [

                            $currentTime,

                            $create,

                        ])

                        ->orWhere(function ($query) use ($create, $last, $currentTime) {

                            $query

                                ->whereRaw("IFNULL(end_log_time, ?) >= ?", [

                                    $currentTime,

                                    $create,

                                ])

                                ->whereRaw("IFNULL(end_log_time, ?) <= ?", [

                                    $currentTime,

                                    $last,

                                ])

                                // Exclude cases where $create equals end_log_time or $last equals start_log_time
        
                                ->whereRaw("? != start_log_time", [$last])

                                ->whereRaw("IFNULL(end_log_time, ?) != ?", [

                                    $currentTime,

                                    $create,

                                ]);
                        });
                });
            });
        })

        ->orderBy("start_log_time", "asc")

        ->get();

    if ($id) {

        $rowTime = null;

        $aboveTime = null;

        $user = User::where("user_type", "U")

            ->where("id", $id)

            ->first();

        $driver = $user;

        $userInfo = UserInfo::where("user_id", $id)

            ->with("homeTerminal")

            ->first();

        $timeZone = $userInfo->home_terminal_timezone;

        //Current time of today

        $currTime = Carbon::now()->setTimezone($timeZone);

        $currentTime = conTimezone($timeZone, $currTime);

        $userId = $user->id;

        if ($user) {

            $driver = User::find($userId);

            $startDay = Carbon::parse($time)->startOfDay();

            $endDay = Carbon::parse($time)->endOfDay();

            $create = $startDay;

            $last = $endDay;

            $start = $startDay;

            $end = $endDay;

            if ($time) {

                $vehicleLog = DriverShiftLog::where("driver_id", $userId)

                    ->where("is_add_approved", 1)

                    ->where(function ($query) use ($create, $last, $currentTime) {

                        $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                            // Check if there's overlap between the time range and log times, excluding exact matches with $create and $last
    
                            $subQuery->where(function ($q) use ($create, $last, $currentTime) {

                                $q->where("start_log_time", ">=", $create)

                                    ->where("start_log_time", "<=", $last)

                                    // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                                    ->whereRaw("? != start_log_time", [$last])

                                    ->whereRaw("IFNULL(end_log_time, ?) != ?", [

                                        $currentTime,

                                        $create,

                                    ])

                                    ->orWhere(function ($query) use ($create, $last, $currentTime) {

                                        $query

                                            ->whereRaw(

                                                "IFNULL(end_log_time, ?) >= ?",

                                                [$currentTime, $create]

                                            )

                                            ->whereRaw(

                                                "IFNULL(end_log_time, ?) <= ?",

                                                [$currentTime, $last]

                                            )

                                            // Exclude cases where $create equals end_log_time or $last equals start_log_time
        
                                            ->whereRaw("? != start_log_time", [

                                                $last,

                                            ])

                                            ->whereRaw(

                                                "IFNULL(end_log_time, ?) != ?",

                                                [$currentTime, $create]

                                            );
                                    });
                            });
                        });
                    }) // Filter by date

                    ->latest("start_log_time")

                    ->first();

                if ($vehicleLog) {

                    $vehicleId = $vehicleLog->vehicle_id;

                    $vehicle = Vehicle::find($vehicleLog->id);

                    $device = Device::where("vehicle_id", $vehicleId)->first();

                    if ($create && $last) {

                        $ShiftLogs = DriverShiftLog::where("driver_id", $userId)

                            ->where("is_add_approved", 1)

                            ->where(function ($query) use ($create, $last, $currentTime) {

                                $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                                    // Check if there's overlap between the time range and log times, excluding exact matches with $create and $last
    
                                    $subQuery->where(function ($q) use ($create, $last, $currentTime) {

                                        $q->where(

                                            "start_log_time",

                                            ">=",

                                            $create

                                        )

                                            ->where(

                                                "start_log_time",

                                                "<=",

                                                $last

                                            )

                                            // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                                            ->whereRaw("? != start_log_time", [

                                                $last,

                                            ])

                                            ->whereRaw(

                                                "IFNULL(end_log_time, ?) != ?",

                                                [$currentTime, $create]

                                            )

                                            ->orWhere(function ($query) use ($create, $last, $currentTime) {

                                                $query

                                                    ->whereRaw(

                                                        "IFNULL(end_log_time, ?) >= ?",

                                                        [$currentTime, $create]

                                                    )

                                                    ->whereRaw(

                                                        "IFNULL(end_log_time, ?) <= ?",

                                                        [$currentTime, $last]

                                                    )

                                                    // Exclude cases where $create equals end_log_time or $last equals start_log_time
        
                                                    ->whereRaw(

                                                        "? != start_log_time",

                                                        [$last]

                                                    )

                                                    ->whereRaw(

                                                        "IFNULL(end_log_time, ?) != ?",

                                                        [$currentTime, $create]

                                                    );
                                            });
                                    });
                                });
                            })

                            ->orderBy("start_log_time", "asc")

                            ->get();

                        $cycleLogs = DriverShiftLog::where("driver_id", $userId)

                            ->where("is_add_approved", 1)

                            ->where(function ($query) use ($create, $last, $currentTime) {

                                $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                                    // Check if there's overlap between the time range and log times, excluding exact matches with $create and $last
    
                                    $subQuery->where(function ($q) use ($create, $last, $currentTime) {

                                        $q->where(

                                            "start_log_time",

                                            ">=",

                                            $create

                                        )

                                            ->where(

                                                "start_log_time",

                                                "<=",

                                                $last

                                            )

                                            // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                                            ->whereRaw("? != start_log_time", [

                                                $last,

                                            ])

                                            ->whereRaw(

                                                "IFNULL(end_log_time, ?) != ?",

                                                [$currentTime, $create]

                                            )

                                            ->orWhere(function ($query) use ($create, $last, $currentTime) {

                                                $query

                                                    ->whereRaw(

                                                        "IFNULL(end_log_time, ?) >= ?",

                                                        [$currentTime, $create]

                                                    )

                                                    ->whereRaw(

                                                        "IFNULL(end_log_time, ?) <= ?",

                                                        [$currentTime, $last]

                                                    )

                                                    // Exclude cases where $create equals end_log_time or $last equals start_log_time
        
                                                    ->whereRaw(

                                                        "? != start_log_time",

                                                        [$last]

                                                    )

                                                    ->whereRaw(

                                                        "IFNULL(end_log_time, ?) != ?",

                                                        [$currentTime, $create]

                                                    );
                                            });
                                    });
                                });
                            })

                            ->orderBy("start_log_time", "asc")

                            ->get();

                        $driverLog = DriverShiftLog::where("driver_id", $userId)

                            ->where("is_add_approved", 1)

                            ->where(function ($query) use ($create, $last, $currentTime) {

                                $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                                    // Check if there's overlap between the time range and log times, excluding exact matches with $create and $last
    
                                    $subQuery->where(function ($q) use ($create, $last, $currentTime) {

                                        $q->where(

                                            "start_log_time",

                                            ">=",

                                            $create

                                        )

                                            ->where(

                                                "start_log_time",

                                                "<=",

                                                $last

                                            )

                                            // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                                            ->whereRaw("? != start_log_time", [

                                                $last,

                                            ])

                                            ->whereRaw(

                                                "IFNULL(end_log_time, ?) != ?",

                                                [$currentTime, $create]

                                            )

                                            ->orWhere(function ($query) use ($create, $last, $currentTime) {

                                                $query

                                                    ->whereRaw(

                                                        "IFNULL(end_log_time, ?) >= ?",

                                                        [$currentTime, $create]

                                                    )

                                                    ->whereRaw(

                                                        "IFNULL(end_log_time, ?) <= ?",

                                                        [$currentTime, $last]

                                                    )

                                                    // Exclude cases where $create equals end_log_time or $last equals start_log_time
        
                                                    ->whereRaw(

                                                        "? != start_log_time",

                                                        [$last]

                                                    )

                                                    ->whereRaw(

                                                        "IFNULL(end_log_time, ?) != ?",

                                                        [$currentTime, $create]

                                                    );
                                            });
                                    });
                                });
                            })

                            ->orderBy("start_log_time", "asc")

                            ->where("current_shift_status", 3)

                            ->get();

                        $latestLog = DriverShiftLog::where("driver_id", $userId)
                            ->where("is_add_approved", 1)
                            ->where(function ($query) use ($create, $last, $currentTime) {
                                $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                                    // Check if there's overlap between the time range and log times, excluding exact matches with $create and $last
                                    $subQuery->where(function ($q) use ($create, $last, $currentTime) {
                                        $q->where(
                                            "start_log_time",
                                            ">=",
                                            $create
                                        )
                                            ->where(
                                                "start_log_time",
                                                "<=",
                                                $last
                                            )
                                            // Exclude cases where $create equals end_log_time or $last equals start_log_time
                                            ->whereRaw("? != start_log_time", [
                                                $last,
                                            ])
                                            ->whereRaw(
                                                "IFNULL(end_log_time, ?) != ?",
                                                [$currentTime, $create]
                                            )
                                            ->orWhere(function ($query) use ($create, $last, $currentTime) {
                                                $query
                                                    ->whereRaw(
                                                        "IFNULL(end_log_time, ?) >= ?",
                                                        [$currentTime, $create]
                                                    )
                                                    ->whereRaw(
                                                        "IFNULL(end_log_time, ?) <= ?",
                                                        [$currentTime, $last]
                                                    )
                                                    // Exclude cases where $create equals end_log_time or $last equals start_log_time
                                                    ->whereRaw(
                                                        "? != start_log_time",
                                                        [$last]
                                                    )
                                                    ->whereRaw(
                                                        "IFNULL(end_log_time, ?) != ?",
                                                        [$currentTime, $create]
                                                    );
                                            });
                                    });
                                });
                            })
                            ->latest("start_log_time")
                            ->first();

                        if ($latestLog) {

                            $log = ListOption::where(

                                "list_id",

                                "driving_status"

                            )

                                ->where(

                                    "option_id",

                                    $latestLog->current_shift_status

                                )

                                ->pluck("title")

                                ->first();

                            $curretLog = $log;

                            $rowTime = $latestLog->start_log_time;

                            $aboveTime = $latestLog->end_log_time;

                            if ($aboveTime == null) {

                                $aboveTime = Carbon::parse($currentTime);
                            }

                            $latestInSec = 0;

                            if (!is_null($aboveTime) && !is_null($rowTime)) {

                                $aboveTime = Carbon::parse($aboveTime);

                                $rowTime = Carbon::parse($rowTime);

                                // Calculate the difference in seconds

                                $latestInSec = $aboveTime->diffInSeconds(

                                    $rowTime

                                );
                            }

                            $latestDiffTime = secondsToTime($latestInSec);
                        }

                        if ($ShiftLogs) {

                            foreach ($ShiftLogs as $data) {

                                $rowTime = $data->start_log_time;

                                $currentTime = Carbon::parse($currentTime);

                                $timeData = create_end_time(

                                    $data,

                                    $start,

                                    $data,

                                    $end,

                                    $currentTime

                                );

                                $create = Carbon::parse($timeData[0]);

                                $last = Carbon::parse($timeData[1]);

                                $aboveTime = $last;

                                $rowTime = $create;

                                $rowStatus = $data->current_shift_status;

                                if (

                                    $rowStatus == 1 ||

                                    $rowStatus == 2 ||

                                    $rowStatus == 5

                                ) {

                                    $aboveTime = $rowTime;
                                }

                                $timeInSeconds = 0;

                                if (

                                    !is_null($aboveTime) &&

                                    !is_null($rowTime)

                                ) {

                                    $aboveTime = Carbon::parse($aboveTime);

                                    $rowTime = Carbon::parse($rowTime);

                                    // Calculate the difference in seconds

                                    $timeInSeconds = $aboveTime->diffInSeconds(

                                        $rowTime

                                    );
                                }

                                $shiftTime += $timeInSeconds;
                            }
                        }

                        if ($cycleLogs) {

                            foreach ($cycleLogs as $data) {

                                $timeData = create_end_time(

                                    $data,

                                    $start,

                                    $data,

                                    $end,

                                    $currentTime

                                );

                                $create = Carbon::parse($timeData[0]);

                                $last = Carbon::parse($timeData[1]);

                                $rowTime = $create;

                                $aboveTime = $last;

                                $rowStatus = $data->current_shift_status;

                                if (

                                    $rowStatus == 1 ||

                                    $rowStatus == 2 ||

                                    $rowStatus == 5

                                ) {

                                    $aboveTime = $rowTime;
                                }

                                $timeInSeconds = 0;

                                if (

                                    !is_null($aboveTime) &&

                                    !is_null($rowTime)

                                ) {

                                    $aboveTime = Carbon::parse($aboveTime);

                                    $rowTime = Carbon::parse($rowTime);

                                    // Calculate the difference in seconds

                                    $timeInSeconds = $aboveTime->diffInSeconds(

                                        $rowTime

                                    );
                                }

                                $cycleTime += $timeInSeconds;
                            }
                        }

                        if ($driverLog) {

                            foreach ($driverLog as $data) {

                                $rowTime = $data->start_log_time;

                                $timeData = create_end_time(

                                    $data,

                                    $start,

                                    $data,

                                    $end,

                                    $currentTime

                                );

                                $create = Carbon::parse($timeData[0]);

                                $last = Carbon::parse($timeData[1]);

                                $rowTime = $create;

                                $aboveTime = $last;

                                $rowStatus = $data->current_shift_status;

                                if (

                                    $rowStatus == 1 ||

                                    $rowStatus == 2 ||

                                    $rowStatus == 5

                                ) {

                                    $aboveTime = $rowTime;
                                }

                                if ($device) {

                                    $vehicleLog = VehicleLogHistory::where(

                                        "identifier",

                                        $device->serial_number

                                    )

                                        ->where("speed", ">=", 5)

                                        ->whereBetween("event_date_time", [

                                            $rowTime,

                                            $aboveTime,

                                        ])

                                        // ->whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])

                                        ->orderBy("event_date_time", "asc")

                                        ->get();

                                    if ($vehicleLog) {

                                        foreach ($vehicleLog as $data) {

                                            $rowDriveTime =

                                                $data->event_date_time;

                                            if ($rowDriveTime < $create) {

                                                $rowDriveTime = $create;
                                            }

                                            $rowDriveId = $data->id;

                                            $aboveDriveRow = VehicleLogHistory::where(

                                                "id",

                                                ">",

                                                $rowDriveId

                                            )

                                                ->where(

                                                    "identifier",

                                                    $device->serial_number

                                                )

                                                ->orderBy("id", "asc")

                                                ->first();

                                            if ($aboveDriveRow) {

                                                $aboveVehicleTime =

                                                    $aboveDriveRow->event_date_time;
                                            } else {

                                                $aboveVehicleTime = $rowDriveTime;
                                            }

                                            if ($aboveVehicleTime > $last) {

                                                $aboveVehicleTime = $last;
                                            }

                                            $timeInSeconds = 0;

                                            if (

                                                !is_null($aboveVehicleTime) &&

                                                !is_null($rowDriveTime)

                                            ) {

                                                $aboveVehicleTime = Carbon::parse(

                                                    $aboveVehicleTime

                                                );

                                                $rowDriveTime = Carbon::parse(

                                                    $rowDriveTime

                                                );

                                                $timeInSeconds = $aboveVehicleTime->diffInSeconds(

                                                    $rowDriveTime

                                                );

                                                $driveTime += $timeInSeconds;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    if ($ruleAssgn) {

        foreach ($ruleAssgn as $data) {

            $rule = Rules::find($data->rule_id);

            if ($rule->reason == 1) {

                $maxHr = $rule->max_hour_limit;

                $maxShiftSec = $maxHr * 3600;

                if ($maxShiftSec > $shiftTime) {

                    $shiftViolTime = $maxShiftSec - $shiftTime;

                    $ViolShift = secondsToTime($shiftViolTime);
                } else {

                    $ViolShift = "00:00:00";
                }
            } elseif ($rule->reason == 5) {

                $maxHr = $rule->max_hour_limit;

                $maxCycleSec = $maxHr * 3600;

                if ($maxCycleSec > $cycleTime) {

                    $violCycleTime = $maxCycleSec - $cycleTime;

                    $violCycleTime = secondsToTime($violCycleTime);
                } else {

                    $violCycleTime = "00:00:00";
                }
            } elseif ($rule->reason == 2) {

                $maxHr = $rule->max_hour_limit;

                $maxCycleSec = $maxHr * 3600;

                if ($maxCycleSec > $cycleTime) {

                    $violCycleTime = $maxCycleSec - $cycleTime;

                    $violCycleTime = secondsToTime($violCycleTime);
                } else {

                    $violCycleTime = "00:00:00";
                }
            } elseif ($rule->reason == 3) {

                $maxHr = $rule->max_hour_limit;

                $maxDriveSec = $maxHr * 3600;

                if ($maxDriveSec > $driveTime) {

                    $violDriveTimes = $maxDriveSec - $driveTime;

                    $violDriveTime = secondsToTime($violDriveTimes);
                } else {

                    $violDriveTime = "00:00:00";
                }
            } elseif ($rule->reason == 4) {

                $maxHr = $rule->max_hour_limit;

                $maxDriveBreakSec = $maxHr * 3600;

                if ($maxDriveBreakSec > $driveTime) {

                    $violBreakTimes = $maxDriveBreakSec - $driveTime;

                    $violBreakTime = secondsToTime($violBreakTimes);
                } else {

                    $violBreakTime = "00:00:00";
                }
            }
        }
    }

    $data = [
        $driver,
        $vehicle,
        $latestDiffTime,
        $userInfo,
        $ViolShift,
        $curretLog,
        $violCycleTime,
        $violDriveTime,
        $violBreakTime,
        $logsData,
    ];

    return $data;
}

//Function for calculating safety score
function safety_score_calculation($userId, $start, $end)
{

    $totalSafetyScore = 0;

    $totalOdometer = 0;

    $timezone = User::find($userId)->timezone;

    $currentTime = Carbon::parse()
        ->setTimezone($timezone)
        ->toDateTimeLocalString();

    $currentTime = Carbon::parse($currentTime);

    $data = safety_driver_score_calculation($userId, $start, $end);

    $filteredData = collect($data);

    // Total number of considered drivers
    $totalDrivers = $filteredData->count();

    $categories = [

        "excellent" => $filteredData
            ->filter(fn($item) => $item[1] >= 90 && $item[1] <= 100)
            ->count(),

        "good" => $filteredData
            ->filter(fn($item) => $item[1] >= 77 && $item[1] <= 89)
            ->count(),

        "fair" => $filteredData
            ->filter(fn($item) => $item[1] >= 50 && $item[1] <= 76)
            ->count(),

        "poor" => $filteredData
            ->filter(fn($item) => $item[1] >= 0 && $item[1] <= 49)
            ->count(),

    ];

    // Calculate percentages
    $percentages = collect($categories)->map(
        fn($count) => $totalDrivers > 0
        ? round(($count / $totalDrivers) * 100, 2)
        : 0
    );

    if ($data && count($data) > 0) {

        foreach ($data as $val) {

            $totalSafetyScore += $val[1];
        }
    }

    $speedingCount = VehicleLogHistory::where("message_reason", "SPEEDING")
        ->whereBetween("event_date_time", [$start, $end])
        ->count();

    $vehicleLogs = VehicleLogHistory::where("speed", ">", 5)
        // ->whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])
        ->whereBetween("event_date_time", [$start, $end])
        ->get();

    $previousOdometer = null;

    foreach ($vehicleLogs as $logData) {

        if ($previousOdometer !== null) {

            $odometerDiff = $logData->odometer - $previousOdometer;

            if ($odometerDiff > 0) {

                $totalOdometer += $odometerDiff;
            }
        }

        $previousOdometer = $logData->odometer;
    }

    $avgOdo = count($vehicleLogs) == 0 ? 0 : $totalOdometer / count($vehicleLogs);

    $avgSafetyScore = count($data) > 0 ? $totalSafetyScore / count($data) : 0;

    return [$avgSafetyScore, $avgOdo, $speedingCount, $percentages];
}

//Function to calculate event per miles
function event_per_miles($userId, $start, $end)
{

    $events_per_1000_miles = [];

    $timezone = User::find($userId)->timezone;

    $currentTime = Carbon::parse()

        ->setTimezone($timezone)

        ->toDateTimeLocalString();

    $currentTime = Carbon::parse($currentTime);

    if ($start == null && $end == null) {

        $start = Carbon::parse($currentTime)->startOfDay();

        $end = Carbon::parse($currentTime)->endOfDay();
    } else {

        $start = Carbon::parse($start)->startOfDay();

        $end = Carbon::parse($end)->endOfDay();
    }

    $device = Device::where("created_by", $userId)->get();

    foreach ($device as $data) {

        $identifier = $data->serial_number;

        $eventLog = VehicleLogHistory::where("identifier", $identifier)

            ->whereIn("message_reason", [

                "HARDBRAKE",

                "HARDACCEL",

                "HARDSTOP",

                "HARDTURN",

                "SPEEDING",

            ])

            ->whereBetween("event_date_time", [$start, $end])

            ->get();

        $previousOdometer = null;

        $totalOdometer = 0;

        foreach ($eventLog as $logData) {

            if ($previousOdometer !== null) {

                $odometerDiff = $logData->odometer - $previousOdometer;

                if ($odometerDiff > 0) {

                    $totalOdometer += $odometerDiff;
                }
            }

            $previousOdometer = $logData->odometer;
        }

        $logCount = Count($eventLog);

        if ($totalOdometer > 0) {

            $eventsPerThousandMiles = ($logCount / $totalOdometer) * 1000;

            $events_per_1000_miles[] = $eventsPerThousandMiles;
        }
    }

    $avgEventPerMiles = 0;

    if ($events_per_1000_miles && count($events_per_1000_miles) > 0) {

        $countEventPerMiles = count($events_per_1000_miles);

        $totalEventPerMiles = 0;

        foreach ($events_per_1000_miles as $miles) {

            $totalEventPerMiles += $miles;
        }

        $avgEventPerMiles = $totalEventPerMiles / $countEventPerMiles;
    }

    return $avgEventPerMiles;
}

//Single function to calculate the violation for both 60 and 70 hours violation
function cycle_calculation_dual(
    $cycleRow,
    $logsArray,
    $firstLog,
    $startTime,
    $lastRow,
    $endTime,
    $currentTime,
    $driver_id,
    $maxHour,
    $totalCycleData,
    $totalCycleTime,
    $firstCycle,
    $rule,
    $lastCycle,
    $lastData
) {

    $logCount = 0;

    $maxHour = $rule->max_hour_limit;

    if (count($cycleRow) == 0) {

        if (count($logsArray) > 0) {

            //The log first row
            $rowTime = $firstLog->start_log_time;

            $timeData = create_end_time(
                $firstLog,
                $startTime,
                $lastRow,
                $endTime,
                $currentTime
            );

            $create = $timeData[0];

            $last = $timeData[1];

            $rowData = find_shift_above_time(
                $driver_id,
                $rowTime,
                $create,
                $last,
                $currentTime
            );

            $cycleabv = $rowData[0];

            $data = cycle_violation_time(
                $cycleabv,
                $startTime,
                $endTime,
                $currentTime,
                $driver_id,
                $maxHour,
                $firstLog,
                $lastRow,
                $totalCycleData,
                1
            );

            $totalCycleTime += $data[1];

            $logCount += $data[2];

            if (count($data[0]) > 0) {

                $totalCycleData = array_merge($totalCycleData, $data[0]);
            }
        }
    }

    $cycleCount = 0;

    foreach ($cycleRow as $log) {

        if ($cycleCount == 0) {

            if ($firstLog && $firstCycle) {

                //if first row is not where shift start

                if ($firstLog->id != $firstCycle->id) {

                    $cycleCount = 1;

                    $belowRow = DriverShiftLog::where(

                        "start_log_time",

                        "<",

                        $firstCycle->start_log_time

                    )

                        ->where("is_add_approved", 1)

                        ->where("driver_id", $driver_id)

                        ->orderBy("start_log_time", "desc")

                        ->first();

                    $timeData = create_end_time(

                        $firstLog,

                        $startTime,

                        $belowRow,

                        $endTime,

                        $currentTime

                    );

                    $create = $timeData[0];

                    $last = $timeData[1];

                    if ($belowRow) {

                        $rowTime = $firstLog->start_log_time;

                        $rowData = find_shift_above_time(

                            $driver_id,

                            $rowTime,

                            $create,

                            $last,

                            $currentTime

                        );

                        $cycleabv = $rowData[0];

                        $data = cycle_violation_time(

                            $cycleabv,

                            $startTime,

                            $endTime,

                            $currentTime,

                            $driver_id,

                            $maxHour,

                            $firstLog,

                            $belowRow,

                            $totalCycleData

                        );

                        $totalCycleTime += $data[1];

                        $logCount += $data[2];

                        if (count($data[0]) > 0) {

                            $totalCycleData = array_merge($totalCycleData, $data[0]);
                        }
                    }
                }
            }
        }

        $rowTime = Carbon::parse($log->start_log_time);

        $aboveRow = DriverShiftLog::where("start_log_time", ">", $rowTime)

            ->where("is_add_approved", 1)

            ->where("driver_id", $driver_id)

            ->where("cycle_start", 1)

            ->orderBy("start_log_time", "asc")

            ->first();

        if ($log->id == $lastCycle->id) {

            if ($lastCycle && $lastRow) {

                if ($lastCycle->id != $lastRow->id) {

                    if ($lastCycle->id != $lastData->id) {

                        $timeData = create_end_time(

                            $lastCycle,

                            $startTime,

                            $lastRow,

                            $endTime,

                            $currentTime

                        );

                        $create = $timeData[0];

                        $last = $timeData[1];

                        $cycleabv = DriverShiftLog::where(

                            "driver_id",

                            $driver_id

                        )

                            ->where("is_add_approved", 1)

                            ->whereNotIn("current_shift_status", [1, 2, 5])

                            ->where(function ($query) use ($create, $last, $currentTime) {

                                $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                                    // Check if there's overlap between the time range and log times, excluding exact matches with $create and $last
    
                                    $subQuery->where(function ($q) use ($create, $last, $currentTime) {

                                        $q->where(

                                            "start_log_time",

                                            ">=",

                                            $create

                                        )

                                            ->where(

                                                "start_log_time",

                                                "<=",

                                                $last

                                            )

                                            // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                                            ->whereRaw("? != start_log_time", [

                                                $last,

                                            ])

                                            ->whereRaw(

                                                "IFNULL(end_log_time, ?) != ?",

                                                [$currentTime, $create]

                                            )

                                            ->orWhere(function ($query) use ($create, $last, $currentTime) {

                                                $query

                                                    ->whereRaw(

                                                        "IFNULL(end_log_time, ?) >= ?",

                                                        [$currentTime, $create]

                                                    )

                                                    ->whereRaw(

                                                        "IFNULL(end_log_time, ?) <= ?",

                                                        [$currentTime, $last]

                                                    )

                                                    // Exclude cases where $create equals end_log_time or $last equals start_log_time
        
                                                    ->whereRaw(

                                                        "? != start_log_time",

                                                        [$last]

                                                    )

                                                    ->whereRaw(

                                                        "IFNULL(end_log_time, ?) != ?",

                                                        [$currentTime, $create]

                                                    );
                                            });
                                    });
                                });
                            })

                            ->orderBy("start_log_time", "asc")

                            ->first();

                        $data = cycle_violation_time(

                            $cycleabv,

                            $startTime,

                            $endTime,

                            $currentTime,

                            $driver_id,

                            $maxHour,

                            $firstLog,

                            $lastRow,

                            $totalCycleData

                        );

                        $totalCycleTime += $data[1];

                        $logCount += $data[2];

                        if (count($data[0]) > 0) {

                            $totalCycleData = array_merge($totalCycleData, $data[0]);
                        }
                    } else {

                        $timeData = create_end_time(

                            $lastCycle,

                            $startTime,

                            $lastData,

                            $endTime,

                            $currentTime

                        );

                        $create = $timeData[0];

                        $last = $timeData[1];

                        $cycleabv = DriverShiftLog::where(

                            "driver_id",

                            $driver_id

                        )

                            ->where("is_add_approved", 1)

                            ->whereNotIn("current_shift_status", [1, 2, 5])

                            ->where(function ($query) use ($create, $last, $currentTime) {

                                $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                                    // Check if there's overlap between the time range and log times, excluding exact matches with $create and $last
    
                                    $subQuery->where(function ($q) use ($create, $last, $currentTime) {

                                        $q->where(

                                            "start_log_time",

                                            ">=",

                                            $create

                                        )

                                            ->where(

                                                "start_log_time",

                                                "<=",

                                                $last

                                            )

                                            // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                                            ->whereRaw("? != start_log_time", [

                                                $last,

                                            ])

                                            ->whereRaw(

                                                "IFNULL(end_log_time, ?) != ?",

                                                [$currentTime, $create]

                                            )

                                            ->orWhere(function ($query) use ($create, $last, $currentTime) {

                                                $query

                                                    ->whereRaw(

                                                        "IFNULL(end_log_time, ?) >= ?",

                                                        [$currentTime, $create]

                                                    )

                                                    ->whereRaw(

                                                        "IFNULL(end_log_time, ?) <= ?",

                                                        [$currentTime, $last]

                                                    )

                                                    // Exclude cases where $create equals end_log_time or $last equals start_log_time
        
                                                    ->whereRaw(

                                                        "? != start_log_time",

                                                        [$last]

                                                    )

                                                    ->whereRaw(

                                                        "IFNULL(end_log_time, ?) != ?",

                                                        [$currentTime, $create]

                                                    );
                                            });
                                    });
                                });
                            })

                            ->orderBy("start_log_time", "asc")

                            ->first();

                        $data = cycle_violation_time(

                            $cycleabv,

                            $startTime,

                            $endTime,

                            $currentTime,

                            $driver_id,

                            $maxHour,

                            $firstLog,

                            $lastData,

                            $totalCycleData

                        );

                        $totalCycleTime += $data[1];

                        $logCount += $data[2];

                        if (count($data[0]) > 0) {

                            $totalCycleData = array_merge($totalCycleData, $data[0]);
                        }
                    }
                } else {

                    $timeData = create_end_time(

                        $lastCycle,

                        $startTime,

                        $lastRow,

                        $endTime,

                        $currentTime

                    );

                    $create = Carbon::parse($timeData[0]);

                    $last = Carbon::parse($timeData[1]);

                    $cycleabv = DriverShiftLog::where("driver_id", $driver_id)

                        ->where("is_add_approved", 1)

                        ->whereNotIn("current_shift_status", [1, 2, 5])

                        ->where(function ($query) use ($create, $last, $currentTime) {

                            $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                                $subQuery

                                    // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
    
                                    ->where(function ($overlapQuery) use ($create, $last, $currentTime) {

                                        $overlapQuery

                                            ->where(

                                                "start_log_time",

                                                "<=",

                                                $last

                                            )

                                            ->whereRaw(

                                                "IFNULL(end_log_time, ?) >= ?",

                                                [$currentTime, $create]

                                            );
                                    })

                                    // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                                    ->whereRaw("IFNULL(end_log_time, ?) != ?", [

                                        $currentTime,

                                        $create,

                                    ])

                                    ->whereRaw("? != start_log_time", [$last]);
                            });
                        })

                        ->orderBy("start_log_time", "asc")

                        ->first();

                    $data = cycle_violation_time(

                        $cycleabv,

                        $startTime,

                        $endTime,

                        $currentTime,

                        $driver_id,

                        $maxHour,

                        $firstLog,

                        $lastRow,

                        $totalCycleData

                    );

                    $totalCycleTime += $data[1];

                    $logCount += $data[2];

                    if (count($data[0]) > 0) {

                        $totalCycleData = array_merge($totalCycleData, $data[0]);
                    }
                }
            }
        } else {

            if ($aboveRow) {

                $belowRow = DriverShiftLog::where(

                    "start_log_time",

                    "<",

                    $aboveRow->start_log_time

                )

                    ->where("is_add_approved", 1)

                    ->where("driver_id", $driver_id)

                    ->orderBy("start_log_time", "desc")

                    ->first();

                $timeData = create_end_time(

                    $log,

                    $startTime,

                    $belowRow,

                    $endTime,

                    $currentTime

                );

                $create = $timeData[0];

                $last = $timeData[1];

                $cycleabv = DriverShiftLog::where("driver_id", $driver_id)

                    ->where("is_add_approved", 1)

                    ->whereNotIn("current_shift_status", [1, 2, 5])

                    ->where(function ($query) use ($create, $last, $currentTime) {

                        $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                            // Check if there's overlap between the time range and log times, excluding exact matches with $create and $last
    
                            $subQuery->where(function ($q) use ($create, $last, $currentTime) {

                                $q->where("start_log_time", ">=", $create)

                                    ->where("start_log_time", "<=", $last)

                                    // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                                    ->whereRaw("? != start_log_time", [$last])

                                    ->whereRaw("IFNULL(end_log_time, ?) != ?", [

                                        $currentTime,

                                        $create,

                                    ])

                                    ->orWhere(function ($query) use ($create, $last, $currentTime) {

                                        $query

                                            ->whereRaw(

                                                "IFNULL(end_log_time, ?) >= ?",

                                                [$currentTime, $create]

                                            )

                                            ->whereRaw(

                                                "IFNULL(end_log_time, ?) <= ?",

                                                [$currentTime, $last]

                                            )

                                            // Exclude cases where $create equals end_log_time or $last equals start_log_time
        
                                            ->whereRaw("? != start_log_time", [

                                                $last,

                                            ])

                                            ->whereRaw(

                                                "IFNULL(end_log_time, ?) != ?",

                                                [$currentTime, $create]

                                            );
                                    });
                            });
                        });
                    })

                    ->orderBy("start_log_time", "asc")

                    ->first();

                $data = cycle_violation_time(

                    $cycleabv,

                    $startTime,

                    $endTime,

                    $currentTime,

                    $driver_id,

                    $maxHour,

                    $firstLog,

                    $belowRow,

                    $totalCycleData

                );

                $totalCycleTime += $data[1];

                $logCount += $data[2];

                if (count($data[0]) > 0) {

                    $totalCycleData = array_merge($totalCycleData, $data[0]);

                }
            }
        }
    }

    return [$totalCycleData, $totalCycleTime, $logCount];
}

function calculating_safety_score_factor_report($userId, $start, $end)
{

    $startDay = null;

    $endDay = null;

    $haCount = 0;

    $hbCount = 0;

    $hsCount = 0;

    $huCount = 0;

    $spdCount = 0;

    $hbPoint = Config("app.weight_hard_braking");

    $haPoint = Config("app.weight_hard_accel");

    $hsPoint = Config("app.weight_hard_stop");

    $huPoint = Config("app.weight_hard_turn");

    $spdPoint = Config("app.weight_speeding");

    $events_per_1000_miles_hb = [];

    $events_per_1000_miles_ha = [];

    $events_per_1000_miles_hs = [];

    $events_per_1000_miles_hu = [];

    $events_per_1000_miles_spd = [];

    $hbCountArray = [];

    $haCountArray = [];

    $hsCountArray = [];

    $huCountArray = [];

    $spdCountArray = [];

    $timezone = User::find($userId)->timezone;

    // Get current time in the user's timezone

    $currentTime = Carbon::now($timezone)->toDateTimeLocalString();

    $currentTime = Carbon::parse($currentTime);

    if (

        (isset($start) || $start == null || $start == "null") &&

        (!isset($end) || $end == null || $end == "null")

    ) {

        $startDay = Carbon::parse($currentTime)->startOfDay();

        $endDay = Carbon::parse($currentTime)->endOfDay();
    } else {

        $startDay = Carbon::parse($start);

        $endDay = Carbon::parse($end);
    }

    $device = Device::where("created_by", $userId)

        ->with("vehicle:id,name")

        ->get();

    foreach ($device as $data) {

        $deviceIdentifiers = $data->serial_number;

        // Fetch all logs for the identifiers at once

        $hardBrakeLogs = VehicleLogHistory::where(

            "identifier",

            $deviceIdentifiers

        )

            ->whereBetween("event_date_time", [$startDay, $endDay])

            ->where("message_reason", "HARDBRAKE")

            ->orderBy("event_date_time", "ASC")

            ->get();

        $hardAccelLogs = VehicleLogHistory::where(

            "identifier",

            $deviceIdentifiers

        )

            ->whereBetween("event_date_time", [$startDay, $endDay])

            ->where("message_reason", "HARDACCEL")

            ->orderBy("event_date_time", "ASC")

            ->get();

        $hardStopLogs = VehicleLogHistory::where(

            "identifier",

            $deviceIdentifiers

        )

            ->whereBetween("event_date_time", [$startDay, $endDay])

            ->where("message_reason", "HARDSTOP")

            ->orderBy("event_date_time", "ASC")

            ->get();

        $hardUTurnLogs = VehicleLogHistory::where(

            "identifier",

            $deviceIdentifiers

        )

            ->whereBetween("event_date_time", [$startDay, $endDay])

            ->where("message_reason", "HARDTURN")

            ->orderBy("event_date_time", "ASC")

            ->get();

        $speedingLogs = VehicleLogHistory::where(

            "identifier",

            $deviceIdentifiers

        )

            ->whereBetween("event_date_time", [$startDay, $endDay])

            ->where("message_reason", "SPEEDING")

            ->orderBy("event_date_time", "ASC")

            ->get();

        $hbCount += count($hardBrakeLogs);

        $haCount += count($hardAccelLogs);

        $hsCount += count($hardStopLogs);

        $huCount += count($hardUTurnLogs);

        $spdCount += count($speedingLogs);

        $spdCountArray[] = $spdCount * $spdPoint;

        $huCountArray[] = $huCount * $huPoint;

        $hsCountArray[] = $hsCount * $hsPoint;

        $haCountArray[] = $haCount * $haPoint;

        $hbCountArray[] = $hbCount * $hbPoint;

        if ($hardBrakeLogs->isNotEmpty()) {

            // Calculate distance driven (based on odometer readings)

            $minOdometer = $hardBrakeLogs->min("odometer");

            $maxOdometer = $hardBrakeLogs->max("odometer");

            if (

                $minOdometer !== null &&

                $maxOdometer !== null &&

                $maxOdometer > $minOdometer

            ) {

                $distanceDriven = $maxOdometer - $minOdometer;

                if ($distanceDriven > 0) {

                    // Count the events of interest

                    $eventCount = $hardBrakeLogs->count();

                    // Calculate the number of events per 1000 miles

                    $eventsPerThousandMiles =

                        ($eventCount / $distanceDriven) * 1000;

                    // Store the result for this device

                    $events_per_1000_miles_hb[] = $eventsPerThousandMiles;
                }
            }
        }

        if ($hardAccelLogs->isNotEmpty()) {

            // Calculate distance driven (based on odometer readings)

            $minOdometer = $hardAccelLogs->min("odometer");

            $maxOdometer = $hardAccelLogs->max("odometer");

            if (

                $minOdometer !== null &&

                $maxOdometer !== null &&

                $maxOdometer > $minOdometer

            ) {

                $distanceDriven = $maxOdometer - $minOdometer;

                if ($distanceDriven > 0) {

                    // Count the events of interest

                    $eventCount = $hardAccelLogs->count();

                    // Calculate the number of events per 1000 miles

                    $eventsPerThousandMilesHA =

                        ($eventCount / $distanceDriven) * 1000;

                    // Store the result for this device

                    $events_per_1000_miles_ha[] = $eventsPerThousandMilesHA;
                }
            }
        }

        if ($hardStopLogs->isNotEmpty()) {

            // Calculate distance driven (based on odometer readings)

            $minOdometer = $hardStopLogs->min("odometer");

            $maxOdometer = $hardStopLogs->max("odometer");

            if (

                $minOdometer !== null &&

                $maxOdometer !== null &&

                $maxOdometer > $minOdometer

            ) {

                $distanceDriven = $maxOdometer - $minOdometer;

                if ($distanceDriven > 0) {

                    // Count the events of interest

                    $eventCount = $hardStopLogs->count();

                    // Calculate the number of events per 1000 miles

                    $eventsPerThousandMilesHS =

                        ($eventCount / $distanceDriven) * 1000;

                    // Store the result for this device

                    $events_per_1000_miles_hs[] = $eventsPerThousandMilesHS;
                }
            }
        }

        if ($hardUTurnLogs->isNotEmpty()) {

            // Calculate distance driven (based on odometer readings)

            $minOdometer = $hardUTurnLogs->min("odometer");

            $maxOdometer = $hardUTurnLogs->max("odometer");

            if (

                $minOdometer !== null &&

                $maxOdometer !== null &&

                $maxOdometer > $minOdometer

            ) {

                $distanceDriven = $maxOdometer - $minOdometer;

                if ($distanceDriven > 0) {

                    // Count the events of interest

                    $eventCount = $hardUTurnLogs->count();

                    // Calculate the number of events per 1000 miles

                    $eventsPerThousandMilesHU =

                        ($eventCount / $distanceDriven) * 1000;

                    // Store the result for this device

                    $events_per_1000_miles_hu[] = $eventsPerThousandMilesHU;
                }
            }
        }

        if ($speedingLogs->isNotEmpty()) {

            // Calculate distance driven (based on odometer readings)

            $minOdometer = $speedingLogs->min("odometer");

            $maxOdometer = $speedingLogs->max("odometer");

            if (

                $minOdometer !== null &&

                $maxOdometer !== null &&

                $maxOdometer > $minOdometer

            ) {

                $distanceDriven = $maxOdometer - $minOdometer;

                if ($distanceDriven > 0) {

                    // Count the events of interest

                    $eventCount = $speedingLogs->count();

                    // Calculate the number of events per 1000 miles

                    $eventsPerThousandMilesSpd =

                        ($eventCount / $distanceDriven) * 1000;

                    // Store the result for this device

                    $events_per_1000_miles_spd[] = $eventsPerThousandMilesSpd;
                }
            }
        }
    }

    $avgEventPerMilesHB = 0;

    if ($events_per_1000_miles_hb && count($events_per_1000_miles_hb) > 0) {

        $countEventPerMilesHB = count($events_per_1000_miles_hb);

        $totalEventPerMilesHB = 0;

        foreach ($events_per_1000_miles_hb as $miles) {

            $totalEventPerMilesHB += $miles;
        }

        $avgEventPerMilesHB = $totalEventPerMilesHB / $countEventPerMilesHB;
    }

    $avgEventPerMilesHA = 0;

    if ($events_per_1000_miles_ha && count($events_per_1000_miles_ha) > 0) {

        $countEventPerMilesHA = count($events_per_1000_miles_ha);

        $totalEventPerMilesHA = 0;

        foreach ($events_per_1000_miles_hs as $miles) {

            $totalEventPerMilesHA += $miles;
        }

        $avgEventPerMilesHA = $totalEventPerMilesHA / $countEventPerMilesHA;
    }

    $avgEventPerMilesHS = 0;

    if ($events_per_1000_miles_hs && count($events_per_1000_miles_hs) > 0) {

        $countEventPerMilesHS = count($events_per_1000_miles_hs);

        $totalEventPerMilesHS = 0;

        foreach ($events_per_1000_miles_hs as $miles) {

            $totalEventPerMilesHS += $miles;
        }

        $avgEventPerMilesHS = $totalEventPerMilesHS / $countEventPerMilesHS;
    }

    $avgEventPerMilesHU = 0;

    if ($events_per_1000_miles_hu && count($events_per_1000_miles_hu) > 0) {

        $countEventPerMilesHU = count($events_per_1000_miles_hu);

        $totalEventPerMilesHU = 0;

        foreach ($events_per_1000_miles_hu as $miles) {

            $totalEventPerMilesHU += $miles;
        }

        $avgEventPerMilesHU = $totalEventPerMilesHU / $countEventPerMilesHU;
    }

    $avgEventPerMilesSPD = 0;

    if ($events_per_1000_miles_spd && count($events_per_1000_miles_spd) > 0) {

        $countEventPerMilesSPD = count($events_per_1000_miles_spd);

        $totalEventPerMilesSPD = 0;

        foreach ($events_per_1000_miles_spd as $miles) {

            $totalEventPerMilesSPD += $miles;
        }

        $avgEventPerMilesSPD = $totalEventPerMilesSPD / $countEventPerMilesSPD;
    }

    $avgHa = 0;

    if ($haCountArray && count($haCountArray) > 0) {

        $countha = count($haCountArray);

        $totalha = 0;

        foreach ($haCountArray as $miles) {

            $totalha += $miles;
        }

        $avgHa = $totalha / $countha;
    }

    $avgHb = 0;

    if ($hbCountArray && count($hbCountArray) > 0) {

        $counthb = count($hbCountArray);

        $totalhb = 0;

        foreach ($hbCountArray as $miles) {

            $totalhb += $miles;
        }

        $avgHb = $totalhb / $counthb;
    }

    $avgHs = 0;

    if ($hsCountArray && count($hsCountArray) > 0) {

        $counths = count($hsCountArray);

        $totalhs = 0;

        foreach ($hsCountArray as $miles) {

            $totalhs += $miles;
        }

        $avgHs = $totalhs / $counths;
    }

    $avgHu = 0;

    if ($huCountArray && count($huCountArray) > 0) {

        $counthu = count($huCountArray);

        $totalhu = 0;

        foreach ($huCountArray as $miles) {

            $totalhu += $miles;
        }

        $avgHu = $totalhu / $counthu;
    }

    $avgSpd = 0;

    if ($spdCountArray && count($spdCountArray) > 0) {

        $countspd = count($spdCountArray);

        $totalspd = 0;

        foreach ($spdCountArray as $miles) {

            $totalspd += $miles;
        }

        $avgSpd = $totalspd / $countspd;
    }

    return [

        $startDay,

        $endDay,

        $avgEventPerMilesHB,

        $avgEventPerMilesHA,

        $avgEventPerMilesHS,

        $avgEventPerMilesHU,

        $avgEventPerMilesSPD,

        $avgHa,

        $avgHb,

        $avgHs,

        $avgHu,

        $avgSpd,

    ];
}

function calculating_safety_score_factor($userId, $start, $end)
{

    $startDay = null;

    $endDay = null;

    $HAImpact = 0;

    $HBImpact = 0;

    $HSImpact = 0;

    $HTImpact = 0;

    $SPDImpact = 0;

    $HAEPM = 0;

    $HBEPM = 0;

    $HSEPM = 0;

    $HTEPM = 0;

    $SPDEPM = 0;

    if ($start != null && $end != null) {

        $startDay = Carbon::parse($start)->startOfDay();

        $endDay = Carbon::parse($end)->endOfDay();
    }

    $timezone = User::find($userId)->timezone;

    // Get current time in the user's timezone

    $currentTime = Carbon::now($timezone)->toDateTimeLocalString();

    $currentTime = Carbon::parse($currentTime);

    if ($start == null && $end == null) {

        $startDay = Carbon::parse($currentTime)->startOfDay();

        $endDay = Carbon::parse($currentTime)->endOfDay();
    }

    $safetyData = safety_driver_score_calculation($userId, $startDay, $endDay);

    if ($safetyData && count($safetyData) > 0) {

        foreach ($safetyData as $value) {

            $dataSet = $value[2];

            $driverId = $value[3];

            $HAEPM += avg_type_event_per_miles(

                $driverId,

                $startDay,

                $endDay,

                "HARDACCEL"

            );

            $HBEPM += avg_type_event_per_miles(

                $driverId,

                $startDay,

                $endDay,

                "HARDBRAKE"

            );

            $HSEPM += avg_type_event_per_miles(

                $driverId,

                $startDay,

                $endDay,

                "HARDSTOP"

            );

            $HTEPM += avg_type_event_per_miles(

                $driverId,

                $startDay,

                $endDay,

                "HARDTURN"

            );

            $SPDEPM += avg_type_event_per_miles(

                $driverId,

                $startDay,

                $endDay,

                "SPEEDING"

            );

            // $eventPerMileHA = avg_ha_event_per_miles($driverId, $startDay, $endD);

            $HAImpact += $dataSet["hard_accel_impact"];

            $HBImpact += $dataSet["hard_brake_impact"];

            $HSImpact += $dataSet["hard_stop_impact"];

            $HTImpact += $dataSet["hard_turn_impact"];

            $SPDImpact += $dataSet["speed_impact"];
        }

        $HAImpact = $HAImpact / count($safetyData);

        $HBImpact = $HBImpact / count($safetyData);

        $HSImpact = $HSImpact / count($safetyData);

        $HTImpact = $HTImpact / count($safetyData);

        $SPDImpact = $SPDImpact / count($safetyData);

        $HAEPM = $HAEPM / count($safetyData);

        $HBEPM = $HBEPM / count($safetyData);

        $HSEPM = $HSEPM / count($safetyData);

        $HTEPM = $HTEPM / count($safetyData);

        $SPDEPM = $SPDEPM / count($safetyData);
    }

    return [

        $startDay,

        $endDay,

        $HBEPM,

        $HAEPM,

        $HSEPM,

        $HTEPM,

        $SPDEPM,

        $HAImpact,

        $HBImpact,

        $HSImpact,

        $HTImpact,

        $SPDImpact,

    ];
}

function safety_driver_score_calculation($userId, $start, $end)
{

    $safetyScoreArray = [];

    $finalData = [];

    $percentWeight = ListOption::where("list_id", "safety_event_percent")

        ->orderBy("seq", "ASC")

        ->get();

    // Retrieve event percentage points

    $eventPoints = [];

    foreach (

        ([

            1 => "speeding",

            2 => "hardAccel",

            3 => "hardBrake",

            4 => "hardStop",

            5 => "hardTurn",

        ]) as $id => $key

    ) {

        $eventPoints[$key] = intval(

            optional($percentWeight->where("option_id", $id)->first())

                ->short_name ?? 0

        );
    }

    // Get weights for different violation types

    $weights = [

        "speeding" => config("app.weight_speeding", 1),

        "hardAccel" => config("app.weight_hard_accel", 1),

        "hardBrake" => config("app.weight_hard_braking", 1),

        "hardStop" => config("app.weight_hard_stop", 1),

        "hardTurn" => config("app.weight_hard_turn", 1),

    ];

    $spdPercent = $eventPoints["speeding"];

    $HAPercent = $eventPoints["hardAccel"];

    $HBPercent = $eventPoints["hardBrake"];

    $HSPercent = $eventPoints["hardStop"];

    $HTPercent = $eventPoints["hardTurn"];

    $spdPoints = $weights["speeding"];

    $HAPoints = $weights["hardAccel"];

    $HBPoints = $weights["hardBrake"];

    $HSPoints = $weights["hardStop"];

    $HTPoints = $weights["hardTurn"];

    // Get all drivers under the user

    $driversData = User::where("user_type", "U")

        ->where("master_id", $userId)

        ->select("id", "first_name", "last_name")

        ->get();

    $user = User::find($userId);

    $timezone = $user->timezone;

    $currentTime = Carbon::parse()

        ->setTimezone($timezone)

        ->toDateTimeLocalString();

    $currentTime = Carbon::parse($currentTime);

    $create =

        $start == null || $start == "null"

        ? Carbon::parse($currentTime)->startOfDay()

        : Carbon::parse($start)->startOfDay();

    $last =

        $end == null || $end == "null"

        ? Carbon::parse($currentTime)->endOfDay()

        : Carbon::parse($end)->endOfDay();

    $LogsData = DriverShiftLog::where("is_add_approved", 1)

        ->where(function ($query) use ($create, $last, $currentTime) {

            $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                $subQuery->where(function ($q) use ($create, $last, $currentTime) {

                    $q->whereBetween("start_log_time", [$create, $last])

                        ->whereRaw("? != start_log_time", [$last])

                        ->whereRaw("IFNULL(end_log_time, ?) != ?", [

                            $currentTime,

                            $create,

                        ])

                        ->orWhere(function ($query) use ($create, $last, $currentTime) {

                            $query

                                ->whereRaw(

                                    "IFNULL(end_log_time, ?) BETWEEN ? AND ?",

                                    [$currentTime, $create, $last]

                                )

                                ->whereRaw("? != start_log_time", [$last])

                                ->whereRaw("IFNULL(end_log_time, ?) != ?", [

                                    $currentTime,

                                    $create,

                                ]);
                        });
                });
            });
        })

        ->select(

            "id",

            "driver_id",

            "vehicle_id",

            "current_shift_status",

            "start_log_time",

            "end_log_time"

        )

        ->with(

            "driver:id,first_name,last_name",

            "vehicle:id,name",

            "vehicle.devices:id,vehicle_id,serial_number",

            "driver.userInfo:id,user_id,driver_id,home_terminal_timezone"

        )

        ->orderBy("start_log_time", "ASC")

        ->get();

    // Get unique driver IDs

    $distinctDriverId = $LogsData

        ->pluck("driver_id")

        ->unique()

        ->values();

    if ($distinctDriverId->isNotEmpty()) {

        foreach ($distinctDriverId as $idDriver) {

            $driverData = User::where("id", $idDriver)

                ->select("id", "first_name", "last_name")

                ->first();

            $driverName =

                $driverData->first_name . " " . $driverData->last_name;

            $dataStructure = calculation_of_safety_events(

                $LogsData,

                $idDriver,

                $create,

                $last,

                $currentTime,

                $driverName

            );

            $finalData[] = $dataStructure;
        }
    }

    $driversArray = $driversData->toArray(); // Convert Eloquent Collection to array

    // Extract all driver IDs present in finalData

    $finalDataIds = array_map("array_keys", $finalData);

    $finalDataIds = array_merge(...$finalDataIds); // Flatten the array

    // Filter out drivers whose ID is in finalData

    $filteredDrivers = array_filter($driversArray, function ($driver) use ($finalDataIds) {

        return !in_array($driver["id"], $finalDataIds); // Negate condition

    });

    if ($filteredDrivers && count($filteredDrivers) > 0) {

        foreach ($filteredDrivers as $values) {

            $name = $values["first_name"] . " " . $values["last_name"];

            $dataSet = [

                "hard_accel_point" => $HAPoints,

                "max_hard_accel_point" => $HAPercent,

                "count_hard_accel" => 0,

                "hard_accel_impact" => 0,

                "hard_brake_point" => $HBPoints,

                "max_hard_brake_point" => $HBPercent,

                "count_hard_brake" => 0,

                "hard_brake_impact" => 0,

                "hard_stop_point" => $HSPoints,

                "max_hard_stop_point" => $HSPercent,

                "count_hard_stop" => 0,

                "hard_stop_impact" => 0,

                "hard_turn_point" => $HTPoints,

                "max_hard_turn_points" => $HTPercent,

                "count_hard_turn" => 0,

                "hard_turn_impact" => 0,

                "speed_point" => $spdPoints,

                "max_speed_points" => $spdPercent,

                "count_speed" => 0,

                "speed_impact" => 0,

                "total_mile" => 0,

            ];

            $safetyScoreArray[] = [

                $name,

                100,

                $dataSet,

                $values["id"],

                $create,

                $last,

            ];
        }
    }

    if ($finalData && count($finalData) > 0) {

        foreach ($finalData as $drivers) {

            foreach ($drivers as $driverID => $driverDetails) {

                foreach ($driverDetails as $driveName => $driverData) {

                    $countHA = $driverData["hardAccelCount"];

                    $countHB = $driverData["hardBrakeCount"];

                    $countHS = $driverData["hardStopCount"];

                    $countHT = $driverData["hardTurnCount"];

                    $countSPD = $driverData["speedingCount"];

                    $countNOExist = $driverData["NoEventCount"];

                    $totalDriveMile = $driverData["total_miles"];

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

                    $impactHA = $impactHA * ($HAPercent / 100);

                    $impactHB = $impactHB * ($HBPercent / 100);

                    $impactHS = $impactHS * ($HSPercent / 100);

                    $impactHT = $impactHT * ($HTPercent / 100);

                    $impactSPD = $impactSPD * ($spdPercent / 100);

                    $earnedHA = $earnedHA * ($HAPercent / 100);

                    $earnedHB = $earnedHB * ($HBPercent / 100);

                    $earnedHS = $earnedHS * ($HSPercent / 100);

                    $earnedHT = $earnedHT * ($HTPercent / 100);

                    $earnedSPD = $earnedSPD * ($spdPercent / 100);

                    $HAEarned = max(

                        0,

                        min($HAPercent, $HAPercent - $impactHA + $earnedHA)

                    );

                    $HAEarned = $HAPercent - $HAEarned;

                    $HBEarned = max(

                        0,

                        min($HBPercent, $HBPercent - $impactHB + $earnedHB)

                    );

                    $HBEarned = $HBPercent - $HBEarned;

                    $HSEarned = max(

                        0,

                        min($HSPercent, $HSPercent - $impactHS + $earnedHS)

                    );

                    $HSEarned = $HSPercent - $HSEarned;

                    $HTEarned = max(

                        0,

                        min($HTPercent, $HTPercent - $impactHT + $earnedHT)

                    );

                    $HTEarned = $HTPercent - $HTEarned;

                    $SPDEarned = max(

                        0,

                        min($spdPercent, $spdPercent - $impactSPD + $earnedSPD)

                    );

                    $SPDEarned = $spdPercent - $spdPercent;

                    $safetyScore = max(

                        0,

                        min(

                            100,

                            100 -

                            ($HAEarned +

                                $HBEarned +

                                $HSEarned +

                                $HTEarned +

                                $SPDEarned)

                        )

                    );

                    $dataSet = [

                        "hard_accel_point" => $HAPoints,

                        "max_hard_accel_point" => $HAPercent,

                        "count_hard_accel" => $countHA,

                        "hard_accel_impact" => $HAEarned,

                        "hard_brake_point" => $HBPoints,

                        "max_hard_brake_point" => $HBPercent,

                        "count_hard_brake" => $countHB,

                        "hard_brake_impact" => $HBEarned,

                        "hard_stop_point" => $HSPoints,

                        "max_hard_stop_point" => $HSPercent,

                        "count_hard_stop" => $countHS,

                        "hard_stop_impact" => $HSEarned,

                        "hard_turn_point" => $HTPoints,

                        "max_hard_turn_points" => $HTPercent,

                        "count_hard_turn" => $countHT,

                        "hard_turn_impact" => $HTEarned,

                        "speed_point" => $spdPoints,

                        "max_speed_points" => $spdPercent,

                        "count_speed" => $countSPD,

                        "speed_impact" => $SPDEarned,

                        "total_mile" => $totalDriveMile,

                    ];

                    $safetyScoreArray[] = [

                        $driveName,

                        $safetyScore,

                        $dataSet,

                        $driverID,

                        $create,

                        $last,

                    ];
                }
            }
        }
    }

    return $safetyScoreArray;
}

function calculation_of_safety_events(

    $LogsData,

    $idDriver,

    $create,

    $last,

    $currentTime,

    $driverName

) {

    $hardAccelCount = 0;

    $hardBrakeCount = 0;

    $hardStopCount = 0;

    $hardTurnCount = 0;

    $speedingCount = 0;

    $NOCount = 0;

    $totalMilesTravel = 0;

    $countDataSystem = 0;

    $eventExist = 0;

    $driverSpecificLogs = $LogsData

        ->where("driver_id", $idDriver)

        ->sortBy("start_log_time");

    foreach ($driverSpecificLogs as $data) {

        $timeLogData = create_end_time(

            $data,

            $create,

            $data,

            $last,

            $currentTime

        );

        $logStartTime = $timeLogData[0];

        $logEndTime = $timeLogData[1];

        $logStartTime = Carbon::parse($logStartTime);

        $logEndTime = Carbon::parse($logEndTime);

        $device = $data->vehicle->devices;

        if ($device && count($device) > 0) {

            $serialNumber = $device[0]->serial_number;

            $logVehicle = VehicleLogHistory::where("identifier", $serialNumber)

                ->whereBetween("event_date_time", [$logStartTime, $logEndTime])

                ->orderBy("event_date_time", "ASC")

                ->select(

                    "id",

                    "device_id",

                    "identifier",

                    "message_reason",

                    "event_time",

                    "event_date_time",

                    "location",

                    "duration",

                    "speed",

                    "odometer",

                    "direction_alpha",

                    "obd_odometer",

                    "obd_speed",

                    "obd_coolant"

                )

                ->get();

            if ($logVehicle->isNotEmpty()) {

                $firstLog = $logVehicle->first();

                $lastLog = $logVehicle->last();

                $firstLogTime = $firstLog->event_date_time;

                $firstLogTime = Carbon::parse($firstLogTime);

                $belowRow = VehicleLogHistory::where(

                    "identifier",

                    $serialNumber

                )

                    ->where("event_date_time", "<", $firstLogTime)

                    ->select(

                        "id",

                        "device_id",

                        "identifier",

                        "message_reason",

                        "event_time",

                        "event_date_time",

                        "location",

                        "duration",

                        "speed",

                        "odometer",

                        "direction_alpha",

                        "obd_odometer",

                        "obd_speed",

                        "obd_coolant"

                    )

                    ->orderBy("event_date_time", "DESC")

                    ->first();

                $firstOdoMeter = 0;

                if ($belowRow) {

                    $belowOdometer = $belowRow->odometer;

                    $firstOdoMeter = $belowOdometer;

                    $totalMiles = $lastLog->odometer - $belowOdometer;

                    $totalMilesTravel += $totalMiles;

                    $startOdometer = $belowOdometer;
                } else {

                    $firstOdoMeter = $firstLog->odometer;

                    $totalMiles = $lastLog->odometer - $firstLog->odometer;

                    $totalMilesTravel += $totalMiles;

                    $startOdometer = $firstLog->odometer;
                }

                $interval = 100;

                $segments = ceil($totalMiles / $interval);

                $currentSegment = 1;

                foreach ($logVehicle as $log) {

                    $messageReason = $log->message_reason;

                    $currentOdoMeter = $log->odometer;

                    if (

                        $currentOdoMeter &&

                        !is_null($currentOdoMeter) &&

                        !is_null($firstOdoMeter)

                    ) {

                        $totalMilesTravel += $currentOdoMeter - $firstOdoMeter;
                    }

                    $firstOdoMeter = $currentOdoMeter;

                    $countDataSystem = $countDataSystem + 1;

                    switch ($messageReason) {

                        case "HARDBRAKE":

                            $eventExist += 1;

                            $hardBrakeCount += 1;

                            break;

                        case "HARDACCEL":

                            $eventExist += 1;

                            $hardAccelCount += 1;

                            break;

                        case "HARDSTOP":

                            $eventExist += 1;

                            $hardStopCount += 1;

                            break;

                        case "HARDTURN":

                            $eventExist += 1;

                            $hardTurnCount += 1;

                            break;

                        case "SPEEDING":

                            $eventExist += 1;

                            $speedingCount += 1;

                            break;
                    }

                    if ($currentSegment == $segments) {

                        if (

                            $messageReason != "HARDBRAKE" &&

                            $messageReason != "HARDACCEL" &&

                            $messageReason != "HARDSTOP" &&

                            $messageReason != "HARDTURN" &&

                            $messageReason != "SPEEDING"

                        ) {

                            $NOCount += 5;
                        }

                        $currentSegment++;

                        $eventExist = 0;
                    } else {

                        if ($log->odometer - $startOdometer >= $interval) {

                            if ($eventExist == 0) {

                                if (

                                    $messageReason != "HARDBRAKE" &&

                                    $messageReason != "HARDACCEL" &&

                                    $messageReason != "HARDSTOP" &&

                                    $messageReason != "HARDTURN" &&

                                    $messageReason != "SPEEDING"

                                ) {

                                    $NOCount += 5;
                                }
                            }

                            $currentSegment++;

                            $eventExist = 0;

                            $startOdometer = $log->odometer;
                        }
                    }
                }
            }
        }
    }

    $finalData[$idDriver][$driverName] = [

        "count" => $countDataSystem,

        "hardAccelCount" => $hardAccelCount,

        "hardBrakeCount" => $hardBrakeCount,

        "hardStopCount" => $hardStopCount,

        "hardTurnCount" => $hardTurnCount,

        "speedingCount" => $speedingCount,

        "NoEventCount" => $NOCount,

        "total_miles" => $totalMilesTravel,

    ];

    return $finalData;
}

function avg_type_event_per_miles($userId, $start, $end, $type)
{

    $startDay = $start ? Carbon::parse($start)->startOfDay() : null;

    $endDay = $end ? Carbon::parse($end)->endOfDay() : null;

    $eventsPerThousandMiles = [];

    // Get the user's timezone

    $timezone = UserInfo::where("user_id", $userId)

        ->pluck("home_terminal_timezone")

        ->first();

    $currentTime = Carbon::parse()

        ->setTimezone($timezone)

        ->toDateTimeLocalString();

    $currentTime = Carbon::parse($currentTime);

    if (is_null($start) && is_null($end)) {

        $startDay = $currentTime->copy()->startOfDay();

        $endDay = $currentTime->copy()->endOfDay();
    }

    $create = $startDay;

    $last = $endDay;

    $LogsData = DriverShiftLog::where("driver_id", $userId)

        ->where("is_add_approved", 1)

        ->where(function ($query) use ($create, $last, $currentTime) {

            $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                $subQuery->where(function ($q) use ($create, $last, $currentTime) {

                    $q->whereBetween("start_log_time", [$create, $last])

                        ->whereRaw("? != start_log_time", [$last])

                        ->whereRaw("IFNULL(end_log_time, ?) != ?", [

                            $currentTime,

                            $create,

                        ])

                        ->orWhere(function ($query) use ($create, $last, $currentTime) {

                            $query

                                ->whereRaw(

                                    "IFNULL(end_log_time, ?) BETWEEN ? AND ?",

                                    [$currentTime, $create, $last]

                                )

                                ->whereRaw("? != start_log_time", [$last])

                                ->whereRaw("IFNULL(end_log_time, ?) != ?", [

                                    $currentTime,

                                    $create,

                                ]);
                        });
                });
            });
        })

        ->select(

            "id",

            "driver_id",

            "vehicle_id",

            "current_shift_status",

            "start_log_time",

            "end_log_time"

        )

        ->with(

            "driver:id,first_name,last_name",

            "vehicle:id,name",

            "vehicle.devices:id,vehicle_id,serial_number",

            "driver.userInfo:id,user_id,driver_id,home_terminal_timezone"

        )

        ->orderBy("start_log_time", "ASC")

        ->get();

    if ($LogsData && count($LogsData) > 0) {

        foreach ($LogsData as $value) {

            $timeData = create_end_time(

                $value,

                $create,

                $value,

                $last,

                $currentTime

            );

            $startLogTime = $timeData[0];

            $endLogTime = $timeData[1];

            $device = $value->vehicle->devices;

            if ($device && count($device) > 0) {

                $serialNumber = $device[0]->serial_number;

                $logVehicle = VehicleLogHistory::where(

                    "identifier",

                    $serialNumber

                )

                    ->where("message_reason", $type)

                    ->whereBetween("event_date_time", [

                        $startLogTime,

                        $endLogTime,

                    ])

                    ->orderBy("event_date_time", "ASC")

                    ->select(

                        "id",

                        "device_id",

                        "identifier",

                        "message_reason",

                        "event_time",

                        "event_date_time",

                        "location",

                        "duration",

                        "speed",

                        "odometer",

                        "direction_alpha",

                        "obd_odometer",

                        "obd_speed",

                        "obd_coolant"

                    )

                    ->get();

                if ($logVehicle->isNotEmpty()) {

                    $minOdometer = $logVehicle->min("odometer");

                    $maxOdometer = $logVehicle->max("odometer");

                    $distanceDriven = $maxOdometer - $minOdometer;

                    if ($distanceDriven > 0) {

                        // Calculate events per 1000 miles

                        $eventCount = $logVehicle->count();

                        $eventsPerThousandMiles[] =

                            ($eventCount / $distanceDriven) * 1000;
                    }
                }
            }
        }
    }

    // Calculate the total events per 1000 miles

    $totalEventPerMiles = array_sum($eventsPerThousandMiles);

    return $totalEventPerMiles;
}

function timeToSeconds($time)
{

    $parts = explode(":", $time);

    // Ensure the time has at least hours, minutes, and seconds

    $partsCount = count($parts);

    if ($partsCount == 3) {

        list($hours, $minutes, $seconds) = $parts;
    } elseif ($partsCount == 2) {

        // If only minutes and seconds are provided

        $hours = 0;

        list($minutes, $seconds) = $parts;
    } elseif ($partsCount == 1) {

        // If only seconds are provided

        $hours = 0;

        $minutes = 0;

        $seconds = $parts[0];
    } else {

        // If input is invalid

        throw new \InvalidArgumentException("Invalid time format: $time");
    }

    // Ensure all parts are numeric

    if (!is_numeric($hours) || !is_numeric($minutes) || !is_numeric($seconds)) {

        throw new \InvalidArgumentException("Invalid time values: $time");
    }

    return $hours * 3600 + $minutes * 60 + $seconds;
}

function safety_score_driver_calculation($id, $start = null, $end = null)
{

    // Fetch weight values from the config

    $weightHOS = config("app.weight_hos_violation");

    $weightSpeeding = config("app.weight_speeding");

    $weightHarshDriving = config("app.weight_harsh_driving");

    // Get the user's timezone

    $timezone = UserInfo::where("user_id", $id)

        ->pluck("home_terminal_timezone")

        ->first();

    // Get the current time in the user's timezone

    $currentTime = Carbon::parse()

        ->setTimezone($timezone)

        ->toDateTimeLocalString();

    $currentTime = Carbon::parse($currentTime);

    // If no start and end time is provided, calculate for the current day

    if ($start === null && $end === null) {

        $startDay = Carbon::parse($currentTime)->startOfDay();

        $endDay = Carbon::parse($currentTime)->endOfDay();
    } else {

        // Parse the provided start and end times

        $startDay = Carbon::parse($start)->startOfDay();

        $endDay = Carbon::parse($end)->endOfDay();
    }

    $create = $startDay;

    $last = $endDay;

    $hos = check_eld_rules($id, $startDay, $endDay);

    // Initialize violation counters

    $totalSpeedingCount = 0;

    $totalHarshDrivingCount = 0;

    $totalOdometer = 0;

    // Calculate the number of HOS violations

    $hosViolationCount =

        count($hos["Shift_data"]) +

        count($hos["cycle_data"]) +

        count($hos["eight_hour_break_violation"]) +

        count($hos["driver_eleven_viol_data"]);

    $driverLog = DriverShiftLog::where("driver_id", $id)

        ->where("is_add_approved", 1)

        ->where(function ($query) use ($create, $last, $currentTime) {

            $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                $subQuery

                    // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
    
                    ->where(function ($overlapQuery) use ($create, $last, $currentTime) {

                        $overlapQuery

                            ->where("start_log_time", "<=", $last)

                            ->whereRaw("IFNULL(end_log_time, ?) >= ?", [

                                $currentTime,

                                $create,

                            ]);
                    })

                    // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                    ->whereRaw("IFNULL(end_log_time, ?) != ?", [

                        $currentTime,

                        $create,

                    ])

                    ->whereRaw("? != start_log_time", [$last]);
            });
        })

        ->orderBy("start_log_time", "asc")

        ->get();

    foreach ($driverLog as $log) {

        $vehicle = $log->vehicle_id;

        $device = Device::where("vehicle_id", $vehicle)->first();

        $startTime = Carbon::parse($log->start_log_time);

        $endTime = $log->end_log_time;

        $endTime =

            $endTime == null || $endTime == "null"

            ? $currentTime

            : Carbon::parse($endTime);

        if ($device) {

            $identifier = $device->serial_number;

            $harshDriveLog = VehicleLogHistory::where("identifier", $identifier)

                ->whereBetween("event_date_time", [$startTime, $endTime])

                ->whereIn("message_reason", [

                    "HARDBRAKE",

                    "HARDACCEL",

                    "HARDSTOP",

                    "HARDTURN",

                ])

                ->get();

            $speedingLog = VehicleLogHistory::where("identifier", $identifier)

                ->whereBetween("event_date_time", [$startTime, $endTime])

                ->where("message_reason", "SPEEDING")

                ->get();

            $vehicleLogs = VehicleLogHistory::where("identifier", $identifier)

                ->whereBetween("event_date_time", [$startTime, $endTime])

                ->where("speed", ">", 5)

                // ->whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])

                ->get();

            $previousOdometer = null;

            foreach ($vehicleLogs as $logData) {

                if ($previousOdometer !== null) {

                    $odometerDiff = $logData->odometer - $previousOdometer;

                    if ($odometerDiff > 0) {

                        $totalOdometer += $odometerDiff;
                    }
                }

                $previousOdometer = $logData->odometer;
            }

            $totalHarshDrivingCount += count($harshDriveLog);

            $totalSpeedingCount += count($speedingLog);
        }
    }

    // Calculate weighted violations

    $hW = $hosViolationCount * $weightHOS;

    $sW = $totalSpeedingCount * $weightSpeeding;

    $dW = $totalHarshDrivingCount * $weightHarshDriving;

    // Calculate the safety score, ensuring it does not go below 0

    $safetyScore = max(100 - ($hW + $sW + $dW), 0);

    return $safetyScore;
}

function event_per_driver_miles($id, $start, $end)
{

    $events_per_1000_miles = []; // Initialize the array to store events per 1000 miles

    $timezone = UserInfo::where("user_id", $id)

        ->pluck("home_terminal_timezone")

        ->first();

    // Get current time in the user's timezone

    $currentTime = Carbon::now($timezone)->toDateTimeLocalString();

    $currentTime = Carbon::parse($currentTime);

    $startDay = $start;

    $endDay = $end;

    if ($start == null && $end == null) {

        $startDay = Carbon::parse($currentTime)->startOfDay();

        $endDay = Carbon::parse($currentTime)->endOfDay();
    } else {

        $startDay = Carbon::parse($start)->startOfDay();

        $endDay = Carbon::parse($end)->endOfDay();
    }

    $create = $startDay;

    $last = $endDay;

    $driverLog = DriverShiftLog::where("driver_id", $id)

        ->where("is_add_approved", 1)

        ->where(function ($query) use ($create, $last, $currentTime) {

            $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                $subQuery

                    // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
    
                    ->where(function ($overlapQuery) use ($create, $last, $currentTime) {

                        $overlapQuery

                            ->where("start_log_time", "<=", $last)

                            ->whereRaw("IFNULL(end_log_time, ?) >= ?", [

                                $currentTime,

                                $create,

                            ]);
                    })

                    // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                    ->whereRaw("IFNULL(end_log_time, ?) != ?", [

                        $currentTime,

                        $create,

                    ])

                    ->whereRaw("? != start_log_time", [$last]);
            });
        })

        ->orderBy("start_log_time", "asc")

        ->get();

    foreach ($driverLog as $log) {

        $totalOdometer = 0;

        $vehicle = $log->vehicle_id;

        $device = Device::where("vehicle_id", $vehicle)->first();

        $startTime = Carbon::parse($log->start_log_time);

        $endTime = $log->end_log_time;

        $endTime =

            $endTime == null || $endTime == "null"

            ? $currentTime

            : Carbon::parse($endTime);

        if ($device) {

            $identifier = $device->serial_number;

            $vehicleLogs = VehicleLogHistory::where("identifier", $identifier)

                ->whereBetween("event_date_time", [$startDay, $endDay])

                ->whereIn("message_reason", [

                    "HARDBRAKE",

                    "HARDACCEL",

                    "HARDSTOP",

                    "HARDTURN",

                    "SPEEDING",

                ])

                ->get();

            $previousOdometer = null;

            foreach ($vehicleLogs as $logData) {

                if ($previousOdometer !== null) {

                    $odometerDiff = $logData->odometer - $previousOdometer;

                    if ($odometerDiff > 0) {

                        $totalOdometer += $odometerDiff;
                    }
                }

                $previousOdometer = $logData->odometer;
            }
        }

        if ($totalOdometer > 0) {

            // Count the events of interest

            $eventCount = count($vehicleLogs);

            // Calculate the number of events per 1000 miles

            $eventsPerThousandMiles = ($eventCount / $totalOdometer) * 1000;

            // Store the result for this device

            $events_per_1000_miles[] = $eventsPerThousandMiles;
        }
    }

    $avgEventPerMiles = 0;

    if (count($events_per_1000_miles) > 0) {

        $totalEventPerMiles = array_sum($events_per_1000_miles);

        $avgEventPerMiles = $totalEventPerMiles / count($events_per_1000_miles);
    }

    return $avgEventPerMiles;
}

function driver_total_drive($id, $start, $end)
{

    $totalOdometer = 0;

    $timezone = UserInfo::where("user_id", $id)

        ->pluck("home_terminal_timezone")

        ->first();

    // Get current time in the user's timezone

    $currentTime = Carbon::now($timezone)->toDateTimeLocalString();

    $currentTime = Carbon::parse($currentTime);

    if ($start == null && $end == null) {

        $startDay = Carbon::parse($currentTime)->startOfDay();

        $endDay = Carbon::parse($currentTime)->endOfDay();
    } else {

        $startDay = Carbon::parse($start)->startOfDay();

        $endDay = Carbon::parse($end)->endOfDay();
    }

    $create = $startDay;

    $last = $endDay;

    $driverLog = DriverShiftLog::where("driver_id", $id)

        ->where("is_add_approved", 1)

        ->where(function ($query) use ($create, $last, $currentTime) {

            $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                $subQuery

                    // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
    
                    ->where(function ($overlapQuery) use ($create, $last, $currentTime) {

                        $overlapQuery

                            ->where("start_log_time", "<=", $last)

                            ->whereRaw("IFNULL(end_log_time, ?) >= ?", [

                                $currentTime,

                                $create,

                            ]);
                    })

                    // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                    ->whereRaw("IFNULL(end_log_time, ?) != ?", [

                        $currentTime,

                        $create,

                    ])

                    ->whereRaw("? != start_log_time", [$last]);
            });
        })

        ->orderBy("start_log_time", "asc")

        ->get();

    foreach ($driverLog as $log) {

        $vehicle = $log->vehicle_id;

        $device = Device::where("vehicle_id", $vehicle)->first();

        $startTime = Carbon::parse($log->start_log_time);

        $endTime = $log->end_log_time;

        $endTime =

            $endTime == null || $endTime == "null"

            ? $currentTime

            : Carbon::parse($endTime);

        if ($device) {

            $identifier = $device->serial_number;

            $vehicleLogs = VehicleLogHistory::where("identifier", $identifier)

                ->where("speed", ">=", 5)

                ->whereBetween("event_date_time", [$startTime, $endTime])

                // ->whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])

                ->get();

            $previousOdometer = null;

            foreach ($vehicleLogs as $logData) {

                if ($previousOdometer !== null) {

                    $odometerDiff = $logData->odometer - $previousOdometer;

                    if ($odometerDiff > 0) {

                        $totalOdometer += $odometerDiff;
                    }
                }

                $previousOdometer = $logData->odometer;
            }
        }
    }

    return $totalOdometer;
}

function calculating_event_safety_score_factor($id, $start, $end)
{

    // Initialize necessary variables

    $startDay = null;

    $endDay = null;

    $timezone = UserInfo::where("user_id", $id)

        ->pluck("home_terminal_timezone")

        ->first();

    $currentTime = Carbon::parse($timezone)

        ->setTimezone($timezone)

        ->toDateTimeLocalString();

    $currentTime = Carbon::parse($currentTime);

    // Event count variables

    $hbCount = 0;

    $haCount = 0;

    $hsCount = 0;

    $huCount = 0;

    $spdCount = 0;

    // Event score weights from configuration

    $hbPoint = Config("app.weight_hard_braking");

    $haPoint = Config("app.weight_hard_accel");

    $hsPoint = Config("app.weight_hard_stop");

    $huPoint = Config("app.weight_hard_turn");

    $spdPoint = Config("app.weight_speeding");

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

    $driverLog = DriverShiftLog::where("driver_id", $id)

        ->where("is_add_approved", 1)

        ->where(function ($query) use ($create, $last, $currentTime) {

            $query->where(function ($subQuery) use ($create, $last, $currentTime) {

                $subQuery

                    // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
    
                    ->where(function ($overlapQuery) use ($create, $last, $currentTime) {

                        $overlapQuery

                            ->where("start_log_time", "<=", $last)

                            ->whereRaw("IFNULL(end_log_time, ?) >= ?", [

                                $currentTime,

                                $create,

                            ]);
                    })

                    // Exclude cases where $create equals end_log_time or $last equals start_log_time
    
                    ->whereRaw("IFNULL(end_log_time, ?) != ?", [

                        $currentTime,

                        $create,

                    ])

                    ->whereRaw("? != start_log_time", [$last]);
            });
        })

        ->orderBy("start_log_time", "asc")

        ->get();

    foreach ($driverLog as $log) {

        $vehicle = $log->vehicle_id;

        $device = Device::where("vehicle_id", $vehicle)->first();

        $startTime = Carbon::parse($log->start_log_time);

        $endTime = $log->end_log_time;

        $endTime =

            $endTime == null || $endTime == "null"

            ? $currentTime

            : Carbon::parse($endTime);

        if ($device) {

            $identifier = $device->serial_number;

            $hardBrakeLogs = VehicleLogHistory::where("identifier", $identifier)

                ->whereBetween("event_date_time", [$startTime, $endTime])

                ->where("message_reason", "HARDBRAKE")

                ->get();

            $hardAccelLogs = VehicleLogHistory::where("identifier", $identifier)

                ->whereBetween("event_date_time", [$startDay, $endDay])

                ->where("message_reason", "HARDACCEL")

                ->get();

            $hardStopLogs = VehicleLogHistory::where("identifier", $identifier)

                ->whereBetween("event_date_time", [$startTime, $endTime])

                ->where("message_reason", "HARDSTOP")

                ->get();

            $hardUTurnLogs = VehicleLogHistory::where("identifier", $identifier)

                ->whereBetween("event_date_time", [$startTime, $endTime])

                ->where("message_reason", "HARDTURN")

                ->get();

            $speedingLogs = VehicleLogHistory::where("identifier", $identifier)

                ->whereBetween("event_date_time", [$startTime, $endTime])

                ->where("message_reason", "SPEEDING")

                ->get();

            $hbCount += count($hardBrakeLogs);

            $haCount += count($hardAccelLogs);

            $hsCount += count($hardStopLogs);

            $huCount += count($hardUTurnLogs);

            $spdCount += count($speedingLogs);
        }
    }

    // Calculate average score for each event type

    $avgHa = $hbCount > 0 ? ($haCount * $haPoint) / $hbCount : 0;

    $avgHb = $hbCount > 0 ? ($hbCount * $hbPoint) / $hbCount : 0;

    $avgHs = $hsCount > 0 ? ($hsCount * $hsPoint) / $hsCount : 0;

    $avgHu = $huCount > 0 ? ($huCount * $huPoint) / $huCount : 0;

    $avgSpd = $spdCount > 0 ? ($spdCount * $spdPoint) / $spdCount : 0;

    return [

        "hard_acceleration" => $avgHa,

        "hard_brake" => $avgHb,

        "hard_stop" => $avgHs,

        "hard_turn" => $avgHu,

        "speeding" => $avgSpd,

    ];
}

function log_time_left_data($id)
{

    $shiftTime = 0;

    $cycleTime = 0;

    $driveTime = 0;

    $shiftViolTime = 0;

    $ViolShift = null;

    $curretLog = null;

    $vehicle = null;

    $driver = null;

    $userInfo = null;

    $violCycleTime = null;

    $latestDiffTime = null;

    $violDriveTime = null;

    $violBreakTime = null;

    $user = User::where("user_type", "U")

        ->select("id", "first_name", "last_name", "email", "timezone")

        ->where("id", $id)

        ->first();

    $driver = $user;

    $vehicle = null;

    $userInfo = UserInfo::select(

        "id",

        "user_id",

        "licenseNumber",

        "username",

        "note",

        "home_terminal_timezone",

        "main_office_address",

        "carrer_us_dot_number",

        "home_terminal_name"

    )

        ->where("user_id", $id)

        ->first();

    $timeZone = $userInfo->home_terminal_timezone;

    //Current time of today

    $currTime = Carbon::now()->setTimezone($timeZone);

    $currTimes = Carbon::parse($currTime->toDateTimeString());

    $currentTime = $currTimes;

    $latestLog = DriverShiftLog::where("driver_id", $id)
        ->where("is_add_approved", 1)
        ->latest("start_log_time")
        ->first();

    $startTime = $latestLog->start_log_time;

    $startTime = Carbon::parse($startTime); // Start time of the day

    $endTime = Carbon::parse($currentTime);

    $datas = check_eld_rules($id, $startTime, $endTime);

    $ruleAssgn = RuleAssign::where("user_id", $id)->get();

    if ($latestLog) {

        $vehicleId = $latestLog->vehicle_id;

        $vehicle = Vehicle::select("id", "name", "vin")

            ->where("id", $vehicleId)

            ->first();

        $log = ListOption::where("list_id", "driving_status")

            ->where("option_id", $latestLog->current_shift_status)

            ->pluck("title")

            ->first();

        $curretLog = $log;

        $rowTime = $latestLog->start_log_time;

        $aboveTime = $latestLog->end_log_time;

        if ($rowTime < $startTime) {

            $rowTime = $startTime;
        }

        if ($aboveTime == null) {

            $aboveTime = $currTimes;

            if ($aboveTime > $endTime) {

                $aboveTime = $endTime;
            }
        } else {

            if ($aboveTime > $endTime) {

                $aboveTime = $endTime;
            }
        }

        $latestInSec = 0;

        if (!is_null($aboveTime) && !is_null($rowTime)) {

            $aboveTime = Carbon::parse($aboveTime);

            $rowTime = Carbon::parse($rowTime);

            $latestInSec = $aboveTime->diffInSeconds($rowTime);
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

                $shiftTime = $datas["total_shift_time"]; // "06:18:27"

                // Convert $shiftTime to seconds

                $shiftTimeSeconds = timeToSeconds($shiftTime);

                if ($maxHrSeconds > $shiftTimeSeconds) {

                    $shiftViolTime = $maxHrSeconds - $shiftTimeSeconds;

                    $ViolShift = secondsToTime($shiftViolTime);
                } else {

                    $ViolShift = "00:00:00";
                }
            } elseif ($rule->reason == 5) {

                $maxHr = $rule->max_hour_limit;

                // Convert $maxHr to seconds

                $maxHrSeconds = $maxHr * 3600;

                $cycleTime = $datas["total_cycle_time"];

                // Convert $shiftTime to seconds

                $cycleTimeSeconds = timeToSeconds($cycleTime);

                if ($maxHrSeconds > $cycleTimeSeconds) {

                    $violCycleTime = $maxHrSeconds - $cycleTimeSeconds;

                    $violCycleTime = secondsToTime($violCycleTime);
                } else {

                    $violCycleTime = "00:00:00";
                }
            } elseif ($rule->reason == 2) {

                $maxHr = $rule->max_hour_limit;

                // Convert $maxHr to seconds

                $maxHrSeconds = $maxHr * 3600;

                $cycleTime = $datas["total_cycle_time"];

                // Convert $shiftTime to seconds

                $cycleTimeSeconds = timeToSeconds($cycleTime);

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

                $driveTime = $datas["total_drive_time"];

                // Convert $shiftTime to seconds

                $driveTimeSeconds = timeToSeconds($driveTime);

                if ($maxHrSeconds > $driveTimeSeconds) {

                    $violDriveTimes = $maxHrSeconds - $driveTimeSeconds;

                    $violDriveTime = secondsToTime($violDriveTimes);
                } else {

                    $violDriveTime = "00:00:00";
                }
            } elseif ($rule->reason == 4) {

                $create = $startTime;

                $last = $endTime;

                $onLog = DriverShiftLog::where("driver_id", $id)

                    ->where("is_add_approved", 1)

                    ->where("current_shift_status", 4)

                    ->where(function ($query) use ($create, $last, $currentTime) {

                        // Check if $create falls between start_log_time and end_log_time, or matches start_log_time
    
                        $query

                            ->where(function ($subQuery) use ($create, $last, $currentTime) {

                                $subQuery

                                    ->where(function ($q) use ($create, $currentTime) {

                                        $q->where(

                                            "start_log_time",

                                            "<=",

                                            $create

                                        )

                                            ->where(

                                                DB::raw(

                                                    'COALESCE(end_log_time, "' .

                                                    $currentTime .

                                                    '")'

                                                ),

                                                ">=",

                                                $create

                                            );
                                    })

                                    ->orWhere("start_log_time", "=", $create);
                            })

                            // Check if $last falls between start_log_time and end_log_time, or matches end_log_time
    
                            ->orWhere(function ($subQuery) use ($last, $currentTime) {

                                $subQuery

                                    ->where(function ($q) use ($last, $currentTime) {

                                        $q->where("start_log_time", "<=", $last)

                                            ->where(

                                                DB::raw(

                                                    'COALESCE(end_log_time, "' .

                                                    $currentTime .

                                                    '")'

                                                ),

                                                ">=",

                                                $last

                                            );
                                    })

                                    ->orWhere(

                                        DB::raw(

                                            'COALESCE(end_log_time, "' .

                                            $currentTime .

                                            '")'

                                        ),

                                        "=",

                                        $last

                                    );
                            });
                    })

                    ->latest("start_log_time") // This will order by 'id' in descending order, no need for both orderBy and latest

                    ->first();

                $logDriver = DriverShiftLog::where("driver_id", $id)

                    ->where("is_add_approved", 1)

                    ->where("current_shift_status", 3)

                    ->where(function ($query) use ($create, $last, $currentTime) {

                        // Check if $create falls between start_log_time and end_log_time, or matches start_log_time
    
                        $query

                            ->where(function ($subQuery) use ($create, $last, $currentTime) {

                                $subQuery

                                    ->where(function ($q) use ($create, $currentTime) {

                                        $q->where(

                                            "start_log_time",

                                            "<=",

                                            $create

                                        )

                                            ->where(

                                                DB::raw(

                                                    'COALESCE(end_log_time, "' .

                                                    $currentTime .

                                                    '")'

                                                ),

                                                ">=",

                                                $create

                                            );
                                    })

                                    ->orWhere("start_log_time", "=", $create);
                            })

                            // Check if $last falls between start_log_time and end_log_time, or matches end_log_time
    
                            ->orWhere(function ($subQuery) use ($last, $currentTime) {

                                $subQuery

                                    ->where(function ($q) use ($last, $currentTime) {

                                        $q->where("start_log_time", "<=", $last)

                                            ->where(

                                                DB::raw(

                                                    'COALESCE(end_log_time, "' .

                                                    $currentTime .

                                                    '")'

                                                ),

                                                ">=",

                                                $last

                                            );
                                    })

                                    ->orWhere(

                                        DB::raw(

                                            'COALESCE(end_log_time, "' .

                                            $currentTime .

                                            '")'

                                        ),

                                        "=",

                                        $last

                                    );
                            });
                    })

                    ->orderBy("start_log_time", "asc") // This will order by 'id' in descending order, no need for both orderBy and latest

                    ->get();

                $totalCountDrive = 0;

                if ($onLog) {

                    $onLogTime = $onLog->start_log_time;

                    if ($onLogTime) {

                        $drivingLog = DriverShiftLog::where("driver_id", $id)

                            ->where("is_add_approved", 1)

                            ->where("start_log_time", ">", $onLogTime)

                            ->where("current_shift_status", 3) // Assuming 3 is the correct status for 'driving'

                            ->where(function ($query) use ($create, $last, $currentTime) {

                                // Check if $create falls between start_log_time and end_log_time, or matches start_log_time
    
                                $query

                                    ->where(function ($subQuery) use ($create, $last, $currentTime) {

                                        $subQuery

                                            ->where(function ($q) use ($create, $currentTime) {

                                                $q->where(

                                                    "start_log_time",

                                                    "<=",

                                                    $create

                                                )

                                                    ->where(

                                                        DB::raw(

                                                            'COALESCE(end_log_time, "' .

                                                            $currentTime .

                                                            '")'

                                                        ),

                                                        ">=",

                                                        $create

                                                    );
                                            })

                                            ->orWhere(

                                                "start_log_time",

                                                "=",

                                                $create

                                            );
                                    })

                                    // Check if $last falls between start_log_time and end_log_time, or matches end_log_time
    
                                    ->orWhere(function ($subQuery) use ($last, $currentTime) {

                                        $subQuery

                                            ->where(function ($q) use ($last, $currentTime) {

                                                $q->where(

                                                    "start_log_time",

                                                    "<=",

                                                    $last

                                                )

                                                    ->where(

                                                        DB::raw(

                                                            'COALESCE(end_log_time, "' .

                                                            $currentTime .

                                                            '")'

                                                        ),

                                                        ">=",

                                                        $last

                                                    );
                                            })

                                            ->orWhere(

                                                DB::raw(

                                                    'COALESCE(end_log_time, "' .

                                                    $currentTime .

                                                    '")'

                                                ),

                                                "=",

                                                $last

                                            );
                                    });
                            })

                            ->orderBy("start_log_time", "asc")

                            ->get();

                        if ($drivingLog && count($drivingLog) > 0) {

                            foreach ($drivingLog as $logsa) {

                                $logStartTime = Carbon::parse(

                                    $logsa->start_log_time

                                );

                                $logsEndTime = Carbon::parse(

                                    $logsa->end_log_time

                                );

                                if ($logStartTime < $create) {

                                    $logStartTime = $create;
                                }

                                if ($logsEndTime == null) {

                                    $logsEndTime = $currTimes;

                                    if ($logsEndTime > $last) {

                                        $logsEndTime = $last;
                                    }
                                } else {

                                    if ($logsEndTime > $last) {

                                        $logsEndTime = $last;
                                    }
                                }

                                $aboveTime = null;

                                $vehicle = $logsa->vehicle_id;

                                $device = Device::where(

                                    "vehicle_id",

                                    $vehicle

                                )->first();

                                if ($device) {

                                    $LogVeh = VehicleLogHistory::where(

                                        "identifier",

                                        $device->serial_number

                                    )

                                        ->where("speed", ">=", 5)

                                        ->whereBetween("event_date_time", [

                                            $logStartTime,

                                            $$logsEndTime,

                                        ])

                                        // ->whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])

                                        ->orderBy("event_date_time", "asc")

                                        ->get();

                                    if ($LogVeh && count($LogVeh) > 0) {

                                        foreach ($LogVeh as $veh) {

                                            $vehStartTime =

                                                $veh->event_date_time;

                                            $aboveVehRow = VehicleLogHistory::where(

                                                "identifier",

                                                $device->serial_number

                                            )

                                                ->where("id", ">", $veh->id)

                                                ->orderBy("id", "asc")

                                                ->first();

                                            $timeAbove = null;

                                            if ($aboveVehRow) {

                                                $timeAbove = Carbon::parse(

                                                    $aboveVehRow->event_date_time

                                                );

                                                if ($timeAbove > $endTime) {

                                                    $timeAbove = $endTime;
                                                }
                                            } else {

                                                $timeAbove = $currTimes;

                                                if ($timeAbove > $endTime) {

                                                    $timeAbove = $endTime;
                                                }
                                            }

                                            $timSec = Carbon::parse(

                                                $timeAbove

                                            )->diffInSeconds(

                                                    Carbon::parse($vehStartTime)

                                                );

                                            $totalCountDrive += $timSec;
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {

                    if ($logDriver && count($logDriver) > 0) {

                        foreach ($logDriver as $logsa) {

                            $logStartTime = Carbon::parse(

                                $logsa->start_log_time

                            );

                            if ($logsa->end_log_time == null) {

                                $logsEndTime = $currTimes;
                            } else {

                                $logsEndTime = Carbon::parse(

                                    $logsa->end_log_time

                                );
                            }

                            $vehicle = $logsa->vehicle_id;

                            $device = Device::where(

                                "vehicle_id",

                                $vehicle

                            )->first();

                            if ($device) {

                                $LogVeh = VehicleLogHistory::where(

                                    "identifier",

                                    $device->serial_number

                                )

                                    ->where("speed", ">=", 5)

                                    ->whereBetween("event_date_time", [

                                        $logStartTime,

                                        $logsEndTime,

                                    ])

                                    // ->whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])

                                    ->orderBy("event_date_time", "asc")

                                    ->get();

                                if ($LogVeh && count($LogVeh) > 0) {

                                    foreach ($LogVeh as $veh) {

                                        $vehStartTime = $veh->event_date_time;

                                        $aboveVehRow = VehicleLogHistory::where(

                                            "identifier",

                                            $device->serial_number

                                        )

                                            ->where("id", ">", $veh->id)

                                            ->orderBy("id", "asc")

                                            ->first();

                                        $timeAbove = null;

                                        if ($aboveVehRow) {

                                            $timeAbove = Carbon::parse(

                                                $aboveVehRow->event_date_time

                                            );

                                            if ($timeAbove > $endTime) {

                                                $timeAbove = $endTime;
                                            }
                                        } else {

                                            $timeAbove = $currTimes;

                                            if ($timeAbove > $endTime) {

                                                $timeAbove = $endTime;
                                            }
                                        }

                                        $timSec = Carbon::parse(

                                            $timeAbove

                                        )->diffInSeconds(

                                                Carbon::parse($vehStartTime)

                                            );

                                        $totalCountDrive += $timSec;
                                    }
                                }
                            }
                        }
                    }
                }

                $maxHr = $rule->max_hour_limit;

                // Convert $maxHr to seconds

                $maxHrSeconds = $maxHr * 3600;

                // $driveTime = $datas['total_drive_time'];

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

        "driver" => $driver,

        "vehicle" => $vehicle,

        "driver_info" => $userInfo,

        "current_log" => $curretLog,

        "current_log_time" => $latestDiffTime,

        "shift_time_left" => $ViolShift,

        "cycle_left_time" => $violCycleTime,

        "driver_left_time" => $violDriveTime,

        "break_left_time" => $violBreakTime,

    ];

    return $data;
}

function fetchProvinceName($latitude, $longitude)
{

    $key = Config::get("app.Map_key");

    $cacheKey = "province_{$latitude}_{$longitude}"; // Unique cache key for the coordinates

    // Check if the result is already in cache

    $provinceName = Cache::get($cacheKey);

    if ($provinceName) {

        return $provinceName;
    }

    // If not in cache, make the API call

    $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [

        "latlng" => $latitude . "," . $longitude,

        "key" => $key,

    ]);

    // Check if the response is successful

    if ($response->successful()) {

        $geocodeData = $response->json();

        if (!empty($geocodeData["results"])) {

            // Loop through the address components to find the province
            foreach (
                ($geocodeData["results"][0]["address_components"]) as $component
            ) {

                if (
                    in_array("administrative_area_level_1", $component["types"])
                ) {

                    $provinceName = $component["long_name"];

                    break;
                }
            }

            // Store the result in cache for future use
            Cache::put($cacheKey, $provinceName, now()->addMinutes(10)); // Cache for 10 minutes

        }
    }

    return $provinceName;
}

function fetchFullAddressName($latitude, $longitude)
{

    $key = Config::get("app.Map_key");

    $cacheKey = "full_address_{$latitude}_{$longitude}"; // Unique cache key for the coordinates

    // Check if the result is already in cache

    $fullAddress = Cache::get($cacheKey);

    if ($fullAddress) {

        return $fullAddress;
    }

    // If not in cache, make the API call

    $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [

        "latlng" => $latitude . "," . $longitude,

        "key" => $key,

    ]);

    // Check if the response is successful

    if ($response->successful()) {

        $geocodeData = $response->json();

        if (!empty($geocodeData["results"])) {

            // Fetch the full formatted address from the first result

            $fullAddress = $geocodeData["results"][0]["formatted_address"];

            // Store the result in cache for future use

            Cache::put($cacheKey, $fullAddress, now()->addMinutes(10)); // Cache for 10 minutes

        }
    }

    return $fullAddress ?? "Address not found";
}

function getStateFromCoordinates($latitude, $longitude)
{

    $states = [

        "Alabama" => [

            "lat_min" => 30.2236,

            "lat_max" => 35.008,

            "lon_min" => -88.4732,

            "lon_max" => -84.8891,

        ],

        "Alaska" => [

            "lat_min" => 51.2137,

            "lat_max" => 71.5387,

            "lon_min" => -179.1489,

            "lon_max" => -129.9937,

        ],

        "Arizona" => [

            "lat_min" => 31.3322,

            "lat_max" => 37.0044,

            "lon_min" => -114.8183,

            "lon_max" => -109.0452,

        ],

        "Arkansas" => [

            "lat_min" => 33.0041,

            "lat_max" => 36.5018,

            "lon_min" => -94.0417,

            "lon_max" => -89.0623,

        ],

        "California" => [

            "lat_min" => 32.5343,

            "lat_max" => 42.0095,

            "lon_min" => -124.5664,

            "lon_max" => -113.6911,

        ],

        "Colorado" => [

            "lat_min" => 36.9931,

            "lat_max" => 41.0034,

            "lon_min" => -109.0452,

            "lon_max" => -102.0416,

        ],

        "Connecticut" => [

            "lat_min" => 40.9664,

            "lat_max" => 42.0501,

            "lon_min" => -73.7277,

            "lon_max" => -71.0854,

        ],

        "Delaware" => [

            "lat_min" => 38.4512,

            "lat_max" => 39.8392,

            "lon_min" => -75.7902,

            "lon_max" => -75.0486,

        ],

        "Florida" => [

            "lat_min" => 24.3963,

            "lat_max" => 31.0008,

            "lon_min" => -87.6349,

            "lon_max" => -80.0314,

        ],

        "Georgia" => [

            "lat_min" => 30.3557,

            "lat_max" => 34.9846,

            "lon_min" => -84.3195,

            "lon_max" => -81.227,

        ],

        "Hawaii" => [

            "lat_min" => 18.54,

            "lat_max" => 28.6849,

            "lon_min" => -156.306,

            "lon_max" => -154.4297,

        ],

        "Idaho" => [

            "lat_min" => 41.988,

            "lat_max" => 49.003,

            "lon_min" => -117.244,

            "lon_max" => -111.044,

        ],

        "Illinois" => [

            "lat_min" => 36.9703,

            "lat_max" => 42.5087,

            "lon_min" => -89.4164,

            "lon_max" => -84.7849,

        ],

        "Indiana" => [

            "lat_min" => 37.7733,

            "lat_max" => 41.7611,

            "lon_min" => -88.0973,

            "lon_max" => -84.7846,

        ],

        "Iowa" => [

            "lat_min" => 40.3755,

            "lat_max" => 43.5012,

            "lon_min" => -96.638,

            "lon_max" => -90.14,

        ],

        "Kansas" => [

            "lat_min" => 36.9931,

            "lat_max" => 40.0036,

            "lon_min" => -102.0416,

            "lon_max" => -94.588,

        ],

        "Kentucky" => [

            "lat_min" => 36.497,

            "lat_max" => 39.147,

            "lon_min" => -89.5712,

            "lon_max" => -81.9647,

        ],

        "Louisiana" => [

            "lat_min" => 28.9231,

            "lat_max" => 33.019,

            "lon_min" => -94.0437,

            "lon_max" => -89.0985,

        ],

        "Maine" => [

            "lat_min" => 43.0655,

            "lat_max" => 47.4595,

            "lon_min" => -71.0842,

            "lon_max" => -66.461,

        ],

        "Maryland" => [

            "lat_min" => 37.8853,

            "lat_max" => 39.463,

            "lon_min" => -79.4877,

            "lon_max" => -75.051,

        ],

        "Massachusetts" => [

            "lat_min" => 41.1848,

            "lat_max" => 42.8861,

            "lon_min" => -73.508,

            "lon_max" => -69.9287,

        ],

        "Michigan" => [

            "lat_min" => 41.6957,

            "lat_max" => 48.3135,

            "lon_min" => -90.4181,

            "lon_max" => -82.4109,

        ],

        "Minnesota" => [

            "lat_min" => 43.499,

            "lat_max" => 49.384,

            "lon_min" => -97.239,

            "lon_max" => -89.415,

        ],

        "Mississippi" => [

            "lat_min" => 30.2137,

            "lat_max" => 34.991,

            "lon_min" => -91.687,

            "lon_max" => -88.097,

        ],

        "Missouri" => [

            "lat_min" => 36.5811,

            "lat_max" => 40.613,

            "lon_min" => -95.759,

            "lon_max" => -89.097,

        ],

        "Montana" => [

            "lat_min" => 44.3582,

            "lat_max" => 49.3458,

            "lon_min" => -116.047,

            "lon_max" => -104.039,

        ],

        "Nebraska" => [

            "lat_min" => 40.0017,

            "lat_max" => 43.0025,

            "lon_min" => -104.0534,

            "lon_max" => -95.3083,

        ],

        "Nevada" => [

            "lat_min" => 35.0018,

            "lat_max" => 42.0022,

            "lon_min" => -120.0057,

            "lon_max" => -114.0396,

        ],

        "New Hampshire" => [

            "lat_min" => 42.6975,

            "lat_max" => 45.3054,

            "lon_min" => -71.3584,

            "lon_max" => -69.2277,

        ],

        "New Jersey" => [

            "lat_min" => 38.9285,

            "lat_max" => 41.3576,

            "lon_min" => -75.5601,

            "lon_max" => -73.8942,

        ],

        "New Mexico" => [

            "lat_min" => 31.3322,

            "lat_max" => 37.0044,

            "lon_min" => -109.0452,

            "lon_max" => -103.0019,

        ],

        "New York" => [

            "lat_min" => 40.4774,

            "lat_max" => 45.0158,

            "lon_min" => -79.76259,

            "lon_max" => -71.1857,

        ],

        "North Carolina" => [

            "lat_min" => 33.8435,

            "lat_max" => 36.5897,

            "lon_min" => -83.6756,

            "lon_max" => -75.4689,

        ],

        "North Dakota" => [

            "lat_min" => 46.125,

            "lat_max" => 49.3451,

            "lon_min" => -104.053,

            "lon_max" => -96.437,

        ],

        "Ohio" => [

            "lat_min" => 38.4034,

            "lat_max" => 41.977,

            "lon_min" => -84.818,

            "lon_max" => -80.518,

        ],

        "Oklahoma" => [

            "lat_min" => 33.6304,

            "lat_max" => 37.002,

            "lon_min" => -103.002,

            "lon_max" => -94.436,

        ],

        "Oregon" => [

            "lat_min" => 41.991794,

            "lat_max" => 46.292035,

            "lon_min" => -124.566,

            "lon_max" => -116.463,

        ],

        "Pennsylvania" => [

            "lat_min" => 39.464,

            "lat_max" => 42.269,

            "lon_min" => -80.519,

            "lon_max" => -74.689,

        ],

        "Rhode Island" => [

            "lat_min" => 41.1463,

            "lat_max" => 41.743,

            "lon_min" => -71.857,

            "lon_max" => -71.12,

        ],

        "South Carolina" => [

            "lat_min" => 32.0334,

            "lat_max" => 35.215,

            "lon_min" => -83.3535,

            "lon_max" => -78.537,

        ],

        "South Dakota" => [

            "lat_min" => 43.234,

            "lat_max" => 45.944,

            "lon_min" => -104.043,

            "lon_max" => -96.436,

        ],

        "Tennessee" => [

            "lat_min" => 35.0007,

            "lat_max" => 36.591,

            "lon_min" => -90.3105,

            "lon_max" => -81.646,

        ],

        "Texas" => [

            "lat_min" => 25.837,

            "lat_max" => 36.5007,

            "lon_min" => -106.6456,

            "lon_max" => -93.5076,

        ],

        "Utah" => [

            "lat_min" => 36.9931,

            "lat_max" => 42.001,

            "lon_min" => -114.043,

            "lon_max" => -109.045,

        ],

        "Vermont" => [

            "lat_min" => 42.726,

            "lat_max" => 45.0109,

            "lon_min" => -73.4373,

            "lon_max" => -71.464,

        ],

        "Virginia" => [

            "lat_min" => 36.5407,

            "lat_max" => 39.468,

            "lon_min" => -83.6758,

            "lon_max" => -75.2405,

        ],

        "Washington" => [

            "lat_min" => 45.5435,

            "lat_max" => 49.002,

            "lon_min" => -124.566,

            "lon_max" => -116.463,

        ],

        "West Virginia" => [

            "lat_min" => 37.2015,

            "lat_max" => 40.638,

            "lon_min" => -80.519,

            "lon_max" => -77.42,

        ],

        "Wisconsin" => [

            "lat_min" => 42.491,

            "lat_max" => 47.085,

            "lon_min" => -92.742,

            "lon_max" => -86.462,

        ],

        "Wyoming" => [

            "lat_min" => 41.0034,

            "lat_max" => 44.535,

            "lon_min" => -111.052,

            "lon_max" => -104.056,

        ],

    ];

    foreach ($states as $state => $boundary) {

        if (

            $latitude >= $boundary["lat_min"] &&

            $latitude <= $boundary["lat_max"] &&

            $longitude >= $boundary["lon_min"] &&

            $longitude <= $boundary["lon_max"]

        ) {

            return $state; // Return the state name if the coordinates are within the boundary

        }
    }

    return "Unknown"; // Return 'Unknown' if no state is found

}

function ha_event_per_miles($userId, $serialNumber, $start, $end)
{

    $startDay = $start ? Carbon::parse($start)->startOfDay() : null;

    $endDay = $end ? Carbon::parse($end)->endOfDay() : null;

    $eventsPerThousandMiles = [];

    // Get the user's timezone

    $timezone = UserInfo::where("user_id", $userId)

        ->pluck("home_terminal_timezone")

        ->first();

    $currentTime = Carbon::now($timezone);

    if (is_null($start) && is_null($end)) {

        $startDay = $currentTime->copy()->startOfDay();

        $endDay = $currentTime->copy()->endOfDay();
    }

    // Fetch logs grouped by identifier

    $logs = VehicleLogHistory::whereIn("identifier", (array) $serialNumber)

        ->whereBetween("event_date_time", [$startDay, $endDay])

        ->where("message_reason", "HARDACCEL")

        ->get()

        ->groupBy("identifier");

    $device = Device::where("serial_number", $serialNumber)->first();

    if ($device) {

        // Fetch logs specific to the current device

        $deviceLogs = $logs->get($device->serial_number, collect());

        if ($deviceLogs->isNotEmpty()) {

            // Calculate distance driven

            $minOdometer = $deviceLogs->min("odometer");

            $maxOdometer = $deviceLogs->max("odometer");

            $distanceDriven = $maxOdometer - $minOdometer;

            if ($distanceDriven > 0) {

                // Calculate events per 1000 miles

                $eventCount = $deviceLogs->count();

                $eventsPerThousandMiles[] =

                    ($eventCount / $distanceDriven) * 1000;
            }
        }
    }

    // Calculate the total events per 1000 miles

    $totalEventPerMiles = array_sum($eventsPerThousandMiles);

    return $totalEventPerMiles;
}

function hb_event_per_miles($userId, $serialNumber, $start, $end)
{

    $startDay = $start ? Carbon::parse($start)->startOfDay() : null;

    $endDay = $end ? Carbon::parse($end)->endOfDay() : null;

    $eventsPerThousandMiles = [];

    // Get the user's timezone

    $timezone = UserInfo::where("user_id", $userId)

        ->pluck("home_terminal_timezone")

        ->first();

    $currentTime = Carbon::now($timezone);

    if (is_null($start) && is_null($end)) {

        $startDay = $currentTime->copy()->startOfDay();

        $endDay = $currentTime->copy()->endOfDay();
    }

    // Fetch logs grouped by identifier

    $logs = VehicleLogHistory::whereIn("identifier", (array) $serialNumber)

        ->whereBetween("event_date_time", [$startDay, $endDay])

        ->where("message_reason", "HARDBRAKE")

        ->get()

        ->groupBy("identifier");

    $device = Device::where("serial_number", $serialNumber)->first();

    if ($device) {

        // Fetch logs specific to the current device

        $deviceLogs = $logs->get($device->serial_number, collect());

        if ($deviceLogs->isNotEmpty()) {

            // Calculate distance driven

            $minOdometer = $deviceLogs->min("odometer");

            $maxOdometer = $deviceLogs->max("odometer");

            $distanceDriven = $maxOdometer - $minOdometer;

            if ($distanceDriven > 0) {

                // Calculate events per 1000 miles

                $eventCount = $deviceLogs->count();

                $eventsPerThousandMiles[] =

                    ($eventCount / $distanceDriven) * 1000;
            }
        }
    }

    // Calculate the total events per 1000 miles

    $totalEventPerMiles = array_sum($eventsPerThousandMiles);

    return $totalEventPerMiles;
}

function hu_event_per_miles($userId, $serialNumber, $start, $end)
{

    $startDay = $start ? Carbon::parse($start)->startOfDay() : null;

    $endDay = $end ? Carbon::parse($end)->endOfDay() : null;

    $eventsPerThousandMiles = [];

    // Get the user's timezone

    $timezone = UserInfo::where("user_id", $userId)

        ->pluck("home_terminal_timezone")

        ->first();

    $currentTime = Carbon::now($timezone);

    if (is_null($start) && is_null($end)) {

        $startDay = $currentTime->copy()->startOfDay();

        $endDay = $currentTime->copy()->endOfDay();
    }

    // Fetch logs grouped by identifier

    $logs = VehicleLogHistory::whereIn("identifier", (array) $serialNumber)

        ->whereBetween("event_date_time", [$startDay, $endDay])

        ->where("message_reason", "HARDTURN")

        ->get()

        ->groupBy("identifier");

    $device = Device::where("serial_number", $serialNumber)->first();

    if ($device) {

        // Fetch logs specific to the current device

        $deviceLogs = $logs->get($device->serial_number, collect());

        if ($deviceLogs->isNotEmpty()) {

            // Calculate distance driven

            $minOdometer = $deviceLogs->min("odometer");

            $maxOdometer = $deviceLogs->max("odometer");

            $distanceDriven = $maxOdometer - $minOdometer;

            if ($distanceDriven > 0) {

                // Calculate events per 1000 miles

                $eventCount = $deviceLogs->count();

                $eventsPerThousandMiles[] =

                    ($eventCount / $distanceDriven) * 1000;
            }
        }
    }

    // Calculate the total events per 1000 miles

    $totalEventPerMiles = array_sum($eventsPerThousandMiles);

    return $totalEventPerMiles;
}

function getScoreCategory($score)
{

    if ($score >= 90 && $score <= 100) {

        return "Excellent";
    } elseif ($score >= 77 && $score <= 89) {

        return "Good";
    } elseif ($score >= 50 && $score <= 76) {

        return "Fair";
    } else {

        return "Poor"; // Default category for scores below 50

    }
}

function aboveLogEventData($log, $currentTime, $startTime, $endTime)
{

    $start = $log->event_date_time;

    if ($startTime > $start) {

        $start = $startTime;
    }

    $serial_number = $log->identifier;

    $abv = VehicleLogHistory::where("id", ">", $log->id)

        ->where("identifier", $serial_number)

        ->orderBy("id", "asc")

        ->first();

    $tmeAbv = null;

    if ($abv) {

        $tmeAbv = Carbon::parse($abv->event_date_time);

        if ($tmeAbv > $endTime) {

            $tmeAbv = Carbon::parse($endTime);
        }
    } else {

        $tmeAbv = $currentTime;

        if ($currentTime > $endTime) {

            $tmeAbv = Carbon::parse($endTime);
        }
    }

    $tot = $tmeAbv->diffInSeconds($start);

    return [$abv, $start, $tmeAbv, $tot];
}

function getDriverCurrentTime($driverId)
{

    $userInfo = UserInfo::where("user_id", $driverId)->first();

    $timezone = $userInfo->home_terminal_timezone;

    $currentTime = Carbon::parse()

        ->setTimezone($timezone)

        ->toDateTimeLocalString();

    return $currentTime;
}

function graph_hos_chart($id, $startTime, $endTime, $currentTime)
{

    $startTime = Carbon::parse($startTime)->startOfDay();

    $endTime = Carbon::parse($endTime)->endOfDay();

    $viol = check_eld_rules($id, $startTime, $endTime);

    $datass = [];

    $create = $startTime;

    $last = $endTime;

    $viol = check_eld_rules($id, $startTime, $endTime);

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

            $vehicle = Vehicle::select("name")

                ->where("id", $data->vehicle_id)

                ->first();

            $log = $data->current_shift_status;

            $logs = ListOption::where("list_id", "driving_status")

                ->where("option_id", $log)

                ->pluck("title")

                ->first();

            $startTimeFormatted = Carbon::parse($create)->format("H:i:s");

            $endTimeFormatted = Carbon::parse($last)->format("H:i:s");

            if ($startTimeFormatted != $endTimeFormatted) {

                $datass[] = [

                    $data->id,

                    $log,

                    $logs,

                    $startTimeFormatted,

                    $endTimeFormatted,

                    $vehicle ? $vehicle->name : '',

                ];
            }
        }

        $datass = insertMissingLogs($datass);

        $arrayLen = count($datass);

        if ($arrayLen > 0) {

            $startTime = Carbon::parse($startTime)->startOfDay();

            $currentTimeStart = Carbon::parse($currentTime)->startOfDay();

            $startTimeHI = Carbon::parse($startTime)->format("H:i:s");

            $currentHI = Carbon::parse($currentTime)->format("H:i:s");

            $endTimeHI = Carbon::parse($endTime)->format("H:i:s");

            $startTimeLogData = $datass[0][3];

            if ($startTimeLogData != $startTimeHI) {

                $newLog = [
                    116111,
                    1,
                    "Off duty",
                    $startTimeHI,
                    $startTimeLogData,
                    $datass[$arrayLen - 1][5],
                ];

                array_unshift($datass, $newLog);
            }

            $arrayLogLength = count($datass);

            $lastLogData = $datass[$arrayLogLength - 1][4];

            if ($startTime == $currentTimeStart) {

                if ($lastLogData != $currentHI) {

                    $datass[] = [

                        116,

                        1,

                        "Off duty",

                        $datass[$arrayLen - 1][4],

                        $currentHI,

                        $datass[$arrayLen - 1][5],

                    ];
                }
            } else {

                if ($lastLogData != "23:59:59") {

                    $datass[] = [

                        116,

                        1,

                        "Off duty",

                        $datass[$arrayLen - 1][4],

                        "23:59:59",

                        $datass[$arrayLen - 1][5],

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

                $datass[] = [1, 1, "Off duty", $startTimeHI, $currentHI, ""];
            } elseif ($startTime < $currentTime) {

                $datass[] = [1, 1, "Off duty", $startTimeHI, $endTimeHI, ""];
            } else {

                $datass[] = [1, 1, "Off duty", $startTimeHI, $startTimeHI, ""];
            }
        }
    } else {

        $currentStartTime = Carbon::parse($currentTime)->startOfDay();

        $startTime = Carbon::parse($startTime);

        if ($startTime == $currentStartTime) {

            $currentStartTime = Carbon::parse($currentTime)->format("H:i:s");

            $startTime = Carbon::parse($startTime)->format("H:i:s");

            $datass[] = [

                1,

                1,

                "Off duty",

                $startTime,

                $currentStartTime,

                "abc",

            ];
        } else {

            $startTime = Carbon::parse($startTime)->format("H:i:s");

            $endTime = Carbon::parse($endTime)->format("H:i:s");

            $datass[] = [1, 1, "Off duty", $startTime, $endTime, "abc"];
        }
    }

    if ($distinctVehicles && count($distinctVehicles) == 0) {

        $distinctVehicles[] = [

            "id" => 1,

            "name" => "abc",

        ];
    }

    return [$datass, $distinctVehicles, $viol];
}

function hos_date_data($id, $startTime, $endTime)
{

    $rowTimess = null;

    $datass = [];

    $datas = [];

    $user = User::find($id);

    $userDriver = User::where("user_type", "U")
        ->where("id", "!=", $id)
        ->where("master_id", $user->master_id)
        ->select("id", "first_name", "last_name")
        ->get();

    $userInfo = UserInfo::where("user_id", $id)->first();

    $timeZone = $userInfo->home_terminal_timezone;

    $currentTime = Carbon::now()
        ->setTimezone($timeZone)
        ->toDateTimeLocalString();

    $currentTime = Carbon::parse($currentTime);

    $startTime = $startTime ? $startTime : $currentTime;

    $endTime = $endTime ? $endTime : $currentTime;

    $timeZone = $userInfo->home_terminal_timezone;

    //Current time of today
    $currTime = Carbon::now()->setTimezone($timeZone);

    $currentTimea = conTimezone($timeZone, $currTime);

    $currentTime = Carbon::parse($currentTimea);

    $start = Carbon::parse($startTime)->startOfDay();

    $end = Carbon::parse($endTime)->endOfDay(); // Ensure the end time includes the whole day

    // Create a CarbonPeriod with 1-day intervals, inclusive of both start and end dates
    $period = CarbonPeriod::create($start, "1 day", $end);

    // Collect the dates into an array
    $dates = [];

    $locationStart = [];

    $odoMeter = null;

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

            $aboveTimess = null;

            $startDataArr = [];

            $start = Carbon::parse($data)->startOfDay();

            $end = Carbon::parse($data)->endOfDay();

            $create = $start;

            $last = $end;

            $logsData = DriverShiftLog::where("driver_id", $id)
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
                                    ->whereRaw(
                                        "IFNULL(end_log_time, ?) >= ?",
                                        [$currentTime, $create]
                                    )
                                    ->whereRaw(
                                        "IFNULL(end_log_time, ?) <= ?",
                                        [$currentTime, $last]
                                    );
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
                                $q3->whereColumn(
                                    "end_log_time",
                                    "start_log_time"
                                )
                                    ->orWhereRaw("end_log_time = ?", [$create]);
                            });
                        });
                    });
                })
                ->orderBy("start_log_time", "asc")
                ->get();

            $lastTimeData = null;

            $locationName = null;
            $odometer = null;
            $engineHour = 0;

            if ($logsData && count($logsData) > 0) {

                $lastLog = $logsData->last();

                foreach ($logsData as $datai) {

                    $timeData = create_end_time(
                        $datai,
                        $start,
                        $datai,
                        $end,
                        $currentTime
                    );

                    $rowTimess = Carbon::parse($timeData[0]);

                    $aboveTimess = Carbon::parse($timeData[1]);

                    $lastTimeData = $aboveTimess;

                    $time_start = $rowTimess->format("h:i A");

                    $listLog = ListOption::where(
                        "option_id",
                        $datai->current_shift_status
                    )
                        ->where("list_id", "driving_status")
                        ->first();

                    $status = $listLog->title;

                    $message = $datai->notes;

                    $vehicle_id = $datai->vehicle_id;

                    $odometer = $datai->odometer;

                    $locationName = $datai->location_name;

                    $engineHour = $datai->engineHour;

                    $engineHour = $engineHour > 0 ? ($engineHour / 3600) : 0;

                    $vehicle = Vehicle::find($vehicle_id);

                    $vehicleName = $vehicle ? $vehicle->name : '';

                    $startLog = $datai;

                    $time_end = Carbon::parse($aboveTimess)->format("h:i A");

                    $timeInSeconds = Carbon::parse($aboveTimess)->diffInSeconds(
                        Carbon::parse($rowTimess)
                    );

                    $duration = secondsToTime($timeInSeconds);

                    $datass[] = [
                        $duration,
                        $status,
                        $message,
                        $vehicleName,
                        $time_start,
                        $time_end,
                        $locationStart,
                        $odometer,
                        $locationName,
                        $engineHour,
                    ];
                }
            } else {

                $engineHour = 0;

                $currentStartTime = Carbon::parse($currentTime)->startOfDay();

                if ($create == $currentStartTime) {

                    $creates = Carbon::parse($create)->format("h:i A");
                    $currentStartTime = Carbon::parse($currentTime)->format(
                        "h:i A"
                    );

                    $durationStart = $currentTime->diffInSeconds($create);

                    $startDataArr[] = [
                        secondsToTime($durationStart),
                        "Off duty",
                        null,
                        "",
                        $creates,
                        $currentStartTime,
                        $locationStart,
                        $odoMeter,
                        $locationName,
                        $engineHour,
                    ];
                } else {

                    $creates = Carbon::parse($create)->format("h:i A");

                    $lasts = Carbon::parse($last)->format("h:i A");

                    $durationStart = $last->diffInSeconds($create);

                    $startDataArr[] = [
                        secondsToTime($durationStart),
                        "Off duty",
                        null,
                        "",
                        $creates,
                        $lasts,
                        $locationStart,
                        $odoMeter,
                        $locationName,
                        $engineHour,
                    ];
                }
            }

            $datass = insertHOSMissingLogs($datass);

            $arraylen = count($datass);

            if ($arraylen > 0) {

                $firstDataLog = $datass[0][4];
                $notesLog = $datass[0][2];
                $vehicleLog = $datass[0][3];
                $locationLog = $datass[0][6];
                $odometerLog = $datass[0][7];
                $locationName = $datass[0][8];
                $engineHour = $datass[0][9];

                $startLogTime = Carbon::parse($firstDataLog)->format("h:i A") != "12:00 AM";

                if ($startLogTime) {

                    $firstLogData = $logsData->first();

                    $firstLogTime = create_end_time(
                        $firstLogData,
                        $start,
                        $firstLogData,
                        $end,
                        $currentTime
                    );

                    $firstDataLog = Carbon::parse($firstLogTime[0]);

                    $startDuration = $firstDataLog->diffInSeconds($start);

                    if ($startDuration > 0) {

                        $startDuration = secondsToTime($startDuration);

                        $startDurationTime = Carbon::parse($start)->format(
                            "h:i A"
                        );

                        $endTimeDuration = Carbon::parse($firstDataLog)->format(
                            "h:i A"
                        );

                        $newLog = [
                            $startDuration,
                            "Off duty",
                            $notesLog,
                            $vehicleLog,
                            $startDurationTime,
                            $endTimeDuration,
                            $locationLog,
                            $odometerLog,
                            $locationName,
                            $engineHour
                        ];

                        array_unshift($datass, $newLog);
                    }
                }

                $startDayTime = Carbon::parse($start)->startOfDay();

                $currentTimeStart = Carbon::parse($currentTime)->startOfDay();

                $currentHI = Carbon::parse($currentTime)->format("h:i A");

                $arrayLogLen = count($datass);

                if ($startDayTime == $currentTimeStart) {

                    $startLogData = $logsData->last();

                    $startLogTimeData = create_end_time(
                        $startLogData,
                        $start,
                        $startLogData,
                        $end,
                        $currentTime
                    );

                    $startLogTimeStart = $startLogTimeData[1];

                    $currentAddressData = $datass[$arrayLogLen - 1][6];

                    $addressName = null;

                    if (count($currentAddressData) > 0) {

                        $addressName = fetchFullAddressName(

                            $currentAddressData[0],

                            $currentAddressData[1]

                        );
                    }

                    if ($datass[$arrayLogLen - 1][5] != $currentHI) {

                        $currentDuration = $currentTime->diffInSeconds(
                            $startLogTimeStart
                        );

                        $currentDuration = secondsToTime($currentDuration);

                        $datass[] = [
                            $currentDuration, // Correct duration
                            "Off duty", // Status
                            $datass[$arraylen - 1][2], // Same null value
                            $datass[$arraylen - 1][3], // Same location
                            $datass[$arraylen - 1][5], // Start time of the new log
                            $currentHI, // End time of the new log
                            $datass[$arraylen - 1][6], // Same GPS coordinates
                            $datass[$arraylen - 1][7], // Same distance
                            $datass[$arraylen - 1][8],
                            $datass[$arraylen - 1][9],
                        ];
                    }
                } else {

                    if ($datass[$arrayLogLen - 1][5] != "12:59 PM") {
                        $start = Carbon::parse($start);
                        $end = Carbon::parse($end);
                        $lastTimeData = Carbon::parse($lastTimeData);
                        $duration = $end->diffInSeconds($lastTimeData); // Ensure correct duration format

                        if ($duration > 0) {

                            $duration = secondsToTime($duration);

                            // Insert the missing log with the calculated duration
                            $datass[] = [
                                $duration, // Correct duration
                                "Off duty", // Status
                                $datass[$arraylen - 1][2], // Same null value
                                $datass[$arraylen - 1][3], // Same location
                                $datass[$arraylen - 1][4], // Start time of the new log
                                "12:59 PM", // End time of the new log
                                [], // Same GPS coordinates
                                $datass[$arraylen - 1][7], // Same distance
                                $datass[$arraylen - 1][8],
                                $datass[$arraylen - 1][9],
                            ];
                        }
                    }
                }


            } else {

                $datass = $startDataArr;
            }

            $startlocation = [];

            $endlocation = [];

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

                    $startlocation = [];
                    $endlocation = [];

                    $startLogOdometer = $startLog[7];
                    $lastLogOdometer = $lastLog[7];

                    if ($startLogOdometer > 0 && $lastLogOdometer > 0) {
                        $diffDistance = $lastLogOdometer - $startLogOdometer;
                    }

                    $engineHourFinal = $lastLog[9];
                    $startDateLocationName = $startLog[8];
                    $endDateLocationName = $lastLog[8];

                    $engineHourFinal = $engineHourFinal > 0 ? $engineHourFinal / 3600 : 0;
                }
            }

            $dataViol = check_eld_rules($id, $start, $end);

            $dataLog = driver_log_time_data($id, $start);

            $datas[] = [

                $data => [
                    $dataViol,
                    $dataLog,
                    $datass,
                    $startlocation,
                    $endlocation,
                    $userDriver,
                    $coDriver,
                    $diffDistance,
                    $lastLogOdometer,
                    $startDateLocationName,
                    $endDateLocationName,
                    $engineHourFinal,
                ],

            ];

            $datass = [];
        }
    }

    return $datas;
}

function hos_date_data_test($id, $startTime, $endTime)
{

    $rowTimess = null;

    $datass = [];

    $datas = [];

    $user = User::find($id);

    $userDriver = User::where("user_type", "U")

        ->where("id", "!=", $id)

        ->where("master_id", $user->master_id)

        ->select("id", "first_name", "last_name")

        ->get();

    $userInfo = UserInfo::where("user_id", $id)->first();

    $timeZone = $userInfo->home_terminal_timezone;

    $currentTime = Carbon::now()

        ->setTimezone($timeZone)

        ->toDateTimeLocalString();

    $currentTime = Carbon::parse($currentTime);

    $startTime = $startTime ? $startTime : $currentTime;

    $endTime = $endTime ? $endTime : $currentTime;

    $timeZone = $userInfo->home_terminal_timezone;

    //Current time of today

    $currTime = Carbon::now()->setTimezone($timeZone);

    $currentTimea = conTimezone($timeZone, $currTime);

    $currentTime = Carbon::parse($currentTimea);

    $start = Carbon::parse($startTime)->startOfDay();

    $end = Carbon::parse($endTime)->endOfDay(); // Ensure the end time includes the whole day

    // Create a CarbonPeriod with 1-day intervals, inclusive of both start and end dates

    $period = CarbonPeriod::create($start, "1 day", $end);

    // Collect the dates into an array

    $dates = [];

    $locationStart = [];

    $odoMeter = null;

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

            $aboveTimess = null;

            $startDataArr = [];

            $start = Carbon::parse($data)->startOfDay();

            $end = Carbon::parse($data)->endOfDay();

            $create = $start;

            $last = $end;

            $logsData = DriverShiftLog::where("driver_id", $id)

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

                                        ->whereRaw(

                                            "IFNULL(end_log_time, ?) >= ?",

                                            [$currentTime, $create]

                                        )

                                        ->whereRaw(

                                            "IFNULL(end_log_time, ?) <= ?",

                                            [$currentTime, $last]

                                        );
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

                                    $q3->whereColumn(

                                        "end_log_time",

                                        "start_log_time"

                                    )

                                        ->orWhereRaw("end_log_time = ?", [$create]);
                                });
                        });
                    });
                })

                ->orderBy("start_log_time", "asc")

                ->get();

            $lastTimeData = null;

            if ($logsData && count($logsData) > 0) {

                foreach ($logsData as $datai) {

                    $timeData = create_end_time(

                        $datai,

                        $start,

                        $datai,

                        $end,

                        $currentTime

                    );

                    $rowTimess = Carbon::parse($timeData[0]);

                    $aboveTimess = Carbon::parse($timeData[1]);

                    $lastTimeData = $aboveTimess;

                    $odoMeter = null;

                    $time_start = $rowTimess->format("h:i A");

                    $listLog = ListOption::where(

                        "option_id",

                        $datai->current_shift_status

                    )

                        ->where("list_id", "driving_status")

                        ->first();

                    $status = $listLog->title;

                    $message = $datai->message_reason;

                    $vehicle_id = $datai->vehicle_id;

                    $vehicle = Vehicle::find($vehicle_id)->name;

                    $timeStartLog = $rowTimess;

                    $timeEndLog = $aboveTimess;

                    $startLog = $datai;

                    $lastLog = $logsData->last();

                    if ($startLog) {

                        $startVehicle = $startLog->vehicle_id;

                        $startDevice = Device::where(

                            "vehicle_id",

                            $startVehicle

                        )->first();

                        if ($startDevice) {

                            $startVehicleLog = VehicleLogHistory::where(

                                "identifier",

                                $startDevice->serial_number

                            )

                                ->whereBetween("event_date_time", [

                                    $timeStartLog,

                                    $timeEndLog,

                                ])

                                ->first();

                            if (!$startVehicleLog) {

                                $startVehicleLog = VehicleLogHistory::where(

                                    "identifier",

                                    $startDevice->serial_number

                                )

                                    ->orderBy("event_date_time", "desc")

                                    ->where(

                                        "event_date_time",

                                        "<",

                                        $timeStartLog

                                    )

                                    ->first();
                            }

                            if (

                                $startVehicleLog &&

                                isset($startVehicleLog->location)

                            ) {

                                $odoMeter = $startVehicleLog->odometer;

                                $location = json_decode(

                                    $startVehicleLog->location

                                );

                                if (

                                    isset($location->GeoLocation->Latitude) &&

                                    isset($location->GeoLocation->Longitude)

                                ) {

                                    $latitude =

                                        $location->GeoLocation->Latitude;

                                    $longitude =

                                        $location->GeoLocation->Longitude;

                                    $locationStart = [$latitude, $longitude];
                                }
                            }
                        }
                    }

                    $addressName = null;

                    if (count($locationStart) > 0) {

                        $addressName = fetchFullAddressName(

                            $locationStart[0],

                            $locationStart[1]

                        );
                    }

                    $time_end = Carbon::parse($aboveTimess)->format("h:i A");

                    $timeInSeconds = Carbon::parse($aboveTimess)->diffInSeconds(

                        Carbon::parse($rowTimess)

                    );

                    $duration = secondsToTime($timeInSeconds);

                    $datass[] = [

                        $duration,

                        $status,

                        $message,

                        $vehicle,

                        $time_start,

                        $time_end,

                        $locationStart,

                        $odoMeter,

                        $addressName,

                    ];
                }
            } else {

                $currentStartTime = Carbon::parse($currentTime)->startOfDay();

                $addressName = null;

                if (count($locationStart) > 0) {

                    $addressName = fetchFullAddressName(

                        $locationStart[0],

                        $locationStart[1]

                    );
                }

                if ($create == $currentStartTime) {

                    $creates = Carbon::parse($create)->format("h:i A");

                    $currentStartTime = Carbon::parse($currentTime)->format(

                        "h:i A"

                    );

                    $durationStart = $currentTime->diffInSeconds($create);

                    $startDataArr[] = [

                        secondsToTime($durationStart),

                        "Off duty",

                        null,

                        "",

                        $creates,

                        $currentStartTime,

                        $locationStart,

                        $odoMeter,

                        $addressName,

                    ];
                } else {

                    $creates = Carbon::parse($create)->format("h:i A");

                    $lasts = Carbon::parse($last)->format("h:i A");

                    $durationStart = $last->diffInSeconds($create);

                    $startDataArr[] = [

                        secondsToTime($durationStart),

                        "Off duty",

                        null,

                        "",

                        $creates,

                        $lasts,

                        $locationStart,

                        $odoMeter,

                        $addressName,

                    ];
                }
            }

            $datass = insertHOSMissingLogs($datass);

            $arraylen = count($datass);

            if ($arraylen > 0) {

                $firstDataLog = $datass[0][4];

                $notesLog = $datass[0][2];

                $vehicleLog = $datass[0][3];

                $locationLog = $datass[0][6];

                $odometerLog = $datass[0][7];

                $startLogTime =

                    Carbon::parse($firstDataLog)->format("h:i A") != "12:00 AM";

                if ($startLogTime) {

                    $firstLogData = $logsData->first();

                    $firstLogTime = create_end_time(

                        $firstLogData,

                        $start,

                        $firstLogData,

                        $end,

                        $currentTime

                    );

                    $firstDataLog = Carbon::parse($firstLogTime[0]);

                    $startDuration = $firstDataLog->diffInSeconds($start);

                    if ($startDuration > 0) {

                        $startDuration = secondsToTime($startDuration);

                        $startDurationTime = Carbon::parse($start)->format(

                            "h:i A"

                        );

                        $endTimeDuration = Carbon::parse($firstDataLog)->format(

                            "h:i A"

                        );

                        $addressName = null;

                        if (count($locationLog) > 0) {

                            $addressName = fetchFullAddressName(

                                $locationLog[0],

                                $locationLog[1]

                            );
                        }

                        $newLog = [

                            $startDuration,

                            "Off duty",

                            $notesLog,

                            $vehicleLog,

                            $startDurationTime,

                            $endTimeDuration,

                            $locationLog,

                            $odometerLog,

                        ];

                        array_unshift($datass, $newLog);
                    }
                }

                $startDayTime = Carbon::parse($start)->startOfDay();

                $currentTimeStart = Carbon::parse($currentTime)->startOfDay();

                $currentHI = Carbon::parse($currentTime)->format("h:i A");

                if ($startDayTime == $currentTimeStart) {

                    $startLogData = $logsData->last();

                    $startLogTimeData = create_end_time(

                        $startLogData,

                        $start,

                        $startLogData,

                        $end,

                        $currentTime

                    );

                    $startLogTimeStart = $startLogTimeData[1];

                    $currentAdaressData = $datass[$arraylen - 1][6];

                    $addressName = null;

                    if (count($currentAdaressData) > 0) {

                        $addressName = fetchFullAddressName(

                            $currentAdaressData[0],

                            $currentAdaressData[1]

                        );
                    }

                    if ($datass[$arraylen - 1][5] != $currentHI) {

                        $currentDuration = $currentTime->diffInSeconds(

                            $startLogTimeStart

                        );

                        $currentDuration = secondsToTime($currentDuration);

                        $datass[] = [

                            $currentDuration, // Correct duration

                            "Off duty", // Status

                            $datass[$arraylen - 1][2], // Same null value

                            $datass[$arraylen - 1][3], // Same location

                            $datass[$arraylen - 1][5], // Start time of the new log

                            $currentHI, // End time of the new log

                            $datass[$arraylen - 1][6], // Same GPS coordinates

                            $datass[$arraylen - 1][7], // Same distance

                            $addressName,

                        ];
                    }
                } else {

                    if ($datass[$arraylen - 1][5] != "12:59 PM") {

                        $start = Carbon::parse($start);

                        $end = Carbon::parse($end);

                        $lastTimeData = Carbon::parse($lastTimeData);

                        $duration = $end->diffInSeconds($lastTimeData); // Ensure correct duration format

                        if ($duration > 0) {

                            $duration = secondsToTime($duration);

                            // Insert the missing log with the calculated duration

                            $datass[] = [

                                $duration, // Correct duration

                                "Off duty", // Status

                                $datass[$arraylen - 1][2], // Same null value

                                $datass[$arraylen - 1][3], // Same location

                                $datass[$arraylen - 1][5], // Start time of the new log

                                "12:59 PM", // End time of the new log

                                $datass[$arraylen - 1][6], // Same GPS coordinates

                                $datass[$arraylen - 1][7], // Same distance

                                $addressName,

                            ];
                        }
                    }
                }
            } else {

                $datass = $startDataArr;
            }

            $startlocation = [];

            $endlocation = [];

            $lastLogOdometer = null;

            $startLogOdometer = null;

            $diffDistance = 0;

            if ($logsData && count($logsData) > 0) {

                $startLog = $logsData->first();

                $lastLog = $logsData->last();

                if ($startLog && $lastLog) {

                    $startVehicle = $startLog->vehicle_id;

                    $lastVehicle = $lastLog->vehicle_id;

                    $startDevice = Device::where(

                        "vehicle_id",

                        $startVehicle

                    )->first();

                    $lastDevice = Device::where(

                        "vehicle_id",

                        $lastVehicle

                    )->first();

                    if ($startDevice) {

                        $startVehicleLog = VehicleLogHistory::where(

                            "identifier",

                            $startDevice->serial_number

                        )

                            ->where("event_date_time", $start)

                            ->first();

                        if (!$startVehicleLog) {

                            $startVehicleLog = VehicleLogHistory::where(

                                "identifier",

                                $startDevice->serial_number

                            )

                                ->orderBy("event_date_time", "desc")

                                ->where("event_date_time", ">", $start)

                                ->first();
                        }

                        if (

                            $startVehicleLog &&

                            isset($startVehicleLog->location)

                        ) {

                            $startLogOdometer = $startVehicleLog->odometer;

                            $location = json_decode($startVehicleLog->location);

                            if (

                                isset($location->GeoLocation->Latitude) &&

                                isset($location->GeoLocation->Longitude)

                            ) {

                                $latitude = $location->GeoLocation->Latitude;

                                $longitude = $location->GeoLocation->Longitude;

                                $startlocation = [$latitude, $longitude];
                            }
                        }
                    }

                    if ($lastDevice) {

                        $lastVehicleLog = VehicleLogHistory::where(

                            "identifier",

                            $lastDevice->serial_number

                        )

                            ->where("event_date_time", $end)

                            ->first();

                        if (!$lastVehicleLog) {

                            $lastVehicleLog = VehicleLogHistory::where(

                                "identifier",

                                $lastDevice->serial_number

                            )

                                ->orderBy("event_date_time", "desc")

                                ->where("event_date_time", "<", $end)

                                ->first();
                        }

                        if (

                            $lastVehicleLog &&

                            isset($lastVehicleLog->location)

                        ) {

                            $lastLogOdometer = $lastVehicleLog->odometer;

                            $location = json_decode($lastVehicleLog->location);

                            if (

                                isset($location->GeoLocation->Latitude) &&

                                isset($location->GeoLocation->Longitude)

                            ) {

                                $latitude = $location->GeoLocation->Latitude;

                                $longitude = $location->GeoLocation->Longitude;

                                $endlocation = [$latitude, $longitude];
                            }
                        }
                    }
                }
            }

            $dataViol = check_eld_rules($id, $start, $end);

            $dataLog = driver_log_time_data($id, $start);

            if (

                !(

                    $lastLogOdometer == null ||

                    $startLogOdometer == null ||

                    $startLogOdometer == "null" ||

                    $lastLogOdometer == "null" ||

                    $lastLogOdometer == $startLogOdometer

                )

            ) {

                if ($lastLogOdometer > $startLogOdometer) {

                    $diffDistance = $lastLogOdometer - $startLogOdometer;
                } elseif ($startLogOdometer > $lastLogOdometer) {

                    $diffDistance = $startLogOdometer - $lastLogOdometer;
                }
            }

            $startDateLocationName = null;

            // if (Count($startlocation) > 0) {

            //     $startDateLocationName = fetchFullAddressName($startlocation[0], $startlocation[1]);

            // }

            $endDateLocationName = null;

            // if (count($endlocation) > 0) {

            //     $endDateLocationName = fetchFullAddressName($endlocation[0], $endlocation[1]);

            // }

            $datas[] = [

                $data => [

                    $dataViol,

                    $dataLog,

                    // $datass,

                    $startlocation,

                    $endlocation,

                    // $userDriver,

                    $coDriver,

                    $diffDistance,

                    $lastLogOdometer,

                    $startDateLocationName,

                    $endDateLocationName,

                ],

            ];

            $datass = [];
        }
    }

    return $datas;
}

function insertMissingLogs($data)
{

    $result = [];

    $idCounter = 200; // Start new IDs from 200 or any unique number

    for ($i = 0; $i < count($data) - 1; $i++) {

        // Add the current log to the result

        $result[] = $data[$i];

        // Check if the end_time of the current log matches the start_time of the next log

        $currentEndTime = $data[$i][4];

        $nextStartTime = $data[$i + 1][3];

        // If there's a mismatch, insert a new log

        if ($currentEndTime !== $nextStartTime) {

            $newLog = [

                $data[$i][0], // Unique ID

                1, // Same status

                "Off duty", // Same status description

                $currentEndTime, // Start time is the end time of the current log

                $nextStartTime, // End time is the start time of the next log

                $data[$i][5], // Same location (A02)

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

function insertHOSMissingLogs($data)
{

    $result = [];

    for ($i = 0; $i < count($data) - 1; $i++) {

        // Add the current log to the result

        $result[] = $data[$i];

        // Check if the end_time of the current log matches the start_time of the next log

        $currentEndTime = $data[$i][5]; // End time at index 5

        $nextStartTime = $data[$i + 1][4]; // Start time at index 4

        // If there's a mismatch, insert a new log
        if ($currentEndTime !== $nextStartTime) {

            // Calculate the duration between currentEndTime and nextStartTime

            $start = DateTime::createFromFormat("h:i A", $currentEndTime);

            $end = DateTime::createFromFormat("h:i A", $nextStartTime);

            // Handle cases where the end time is on the next day
            if ($end < $start) {

                $end->modify("+1 day");
            }

            // Calculate the duration
            $interval = $start->diff($end);

            $duration = $interval->format("%H:%I:%S");

            // Create the new log with the calculated duration
            $newLog = [

                $duration, // Calculated duration

                "Off duty", // Same status

                null, // Same null value

                $data[$i][3], // Same location (A02)

                $currentEndTime, // Start time is the end time of the current log

                $nextStartTime, // End time is the start time of the next log

                $data[$i][6], // Same GPS coordinates

                $data[$i][7], // Same distance

                $data[$i][8], // Same distance

                $data[$i][9], // Same distance

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
function check_log_driver_exist($driverId, $create, $last, $logId)
{

    $data = [];

    $create = Carbon::parse($create);

    $last = Carbon::parse($last);

    // Get user's timezone
    $userInfo = UserInfo::where("user_id", $driverId)->first();

    $timezone = $userInfo->home_terminal_timezone;

    $currentTime = Carbon::parse()
        ->setTimezone($timezone)
        ->toDateTimeLocalString();

    $currentTime = Carbon::parse($currentTime);

    $checkLog = DriverShiftLog::where("driver_id", $driverId)
        ->where("id", "!=", $logId)
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

function reAssign_log_exist($id, $create, $last, $type, $dId)
{

    $data = [];

    $create = Carbon::parse($create);

    $last = Carbon::parse($last);

    // Get user's timezone
    $userInfo = UserInfo::where("user_id", $id)->first();

    $timezone = $userInfo->home_terminal_timezone;

    $currentTime = Carbon::parse()
        ->setTimezone($timezone)
        ->toDateTimeLocalString();

    $currentTime = Carbon::parse($currentTime);

    if ($type == 1) {

        $checkLog = DriverShiftLog::where("driver_id", $id)
            ->where("id", "!=", $dId)
            ->where("is_add_approved", 1)
            ->where(function ($query) use ($create, $last, $currentTime) {
                $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                    $subQuery
                        // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
                        ->where(function ($overlapQuery) use ($create, $last, $currentTime) {
                            $overlapQuery
                                ->where("start_log_time", "<=", $last)
                                ->whereRaw("IFNULL(end_log_time, ?) >= ?", [
                                    $currentTime,
                                    $create,
                                ]);
                        })
                        // Exclude cases where $create equals end_log_time or $last equals start_log_time
                        ->whereRaw("IFNULL(end_log_time, ?) != ?", [
                            $currentTime,
                            $create,
                        ])
                        ->whereRaw("? != start_log_time", [$last]);
                });
            })
            ->orderBy("start_log_time", "DESC")
            ->get();
    } else {

        $checkLog = DriverShiftLog::where("driver_id", $id)
            ->where("is_add_approved", 1)
            ->where(function ($query) use ($create, $last, $currentTime) {
                $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                    $subQuery
                        // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
                        ->where(function ($overlapQuery) use ($create, $last, $currentTime) {
                            $overlapQuery
                                ->where("start_log_time", "<=", $last)
                                ->whereRaw("IFNULL(end_log_time, ?) >= ?", [
                                    $currentTime,
                                    $create,
                                ]);
                        })
                        // Exclude cases where $create equals end_log_time or $last equals start_log_time
                        ->whereRaw("IFNULL(end_log_time, ?) != ?", [
                            $currentTime,
                            $create,
                        ])
                        ->whereRaw("? != start_log_time", [$last]);
                });
            })
            ->orderBy("start_log_time", "DESC")
            ->get();
    }

    // Find the nearest log before the create time
    $beforeLog = DriverShiftLog::where("driver_id", $id)
        ->where("end_log_time", "<=", $create)
        ->orderBy("end_log_time", "DESC")
        ->first();

    // Find the nearest log after the last time

    $afterLog = DriverShiftLog::where("driver_id", $id)

        ->where("start_log_time", ">=", $last)

        ->orderBy("start_log_time", "ASC")

        ->first();

    if ($checkLog && count($checkLog) > 0) {

        $exists = $checkLog->contains("current_shift_status", 3);

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

function getRiskLevel($datas)
{

    $reason = $datas->message_reason;

    $speed = $datas->speed;

    $duration = $datas->duration;

    $odometer = $datas->odometer;

    $obd_fuel = $datas->obd_fuel;

    switch ($reason) {

        case "SPEEDING":

            if ($speed > 80) {

                return "high";
            }

            if ($speed > 60) {

                return "medium";
            }

            return "low";

        case "HARDBRAKE":

            if ($duration < 2) {

                return "high";
            }

            if ($duration < 4) {

                return "medium";
            }

            return "low";

        case "HARDACCEL":

            if ($speed > 70) {

                return "high";
            }

            if ($speed > 50) {

                return "medium";
            }

            return "low";

        case "HARDTURN":

            if ($speed > 50) {

                return "high";
            }

            if ($speed > 30) {

                return "medium";
            }

            return "low";

        case "HARDSTOP":

            if ($duration < 1) {

                return "high";
            }

            if ($duration < 3) {

                return "medium";
            }

            return "low";

        default:

            return "unknown";
    }
}

function find_shift_above_time(

    $driverId,

    $rowTime,

    $create,

    $last,

    $currentTime

) {

    $shiftabv = DriverShiftLog::where("driver_id", $driverId)

        ->where("is_add_approved", 1)

        ->where("shift_start", 1)

        ->where("start_log_time", "<", $rowTime)

        ->orderBy("start_log_time", "desc")

        ->first();

    if (!$shiftabv) {

        $shiftabv = DriverShiftLog::where("driver_id", $driverId)

            ->where("is_add_approved", 1)

            ->where("shift_start", 0)

            ->where("start_log_time", "<", $rowTime)

            ->orderBy("start_log_time", "asc")

            ->first();
    }

    if (!$shiftabv) {

        $shiftabv = DriverShiftLog::where("driver_id", $driverId)
            ->where("is_add_approved", 1)
            ->where("current_shift_status", 3)
            ->where(function ($query) use ($create, $last, $currentTime) {
                $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                    $subQuery
                        // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
                        ->where(function ($overlapQuery) use ($create, $last, $currentTime) {
                            $overlapQuery
                                ->where("start_log_time", "<=", $last)
                                ->whereRaw("IFNULL(end_log_time, ?) >= ?", [
                                    $currentTime,
                                    $create,
                                ]);
                        })
                        // Exclude cases where $create equals end_log_time or $last equals start_log_time
                        ->whereRaw("IFNULL(end_log_time, ?) != ?", [
                            $currentTime,
                            $create,
                        ])
                        ->whereRaw("? != start_log_time", [$last]);
                });
            })
            ->orderBy("start_log_time", "asc")
            ->first();
    }

    return [$shiftabv, $shiftabv->start_log_time];
}

function check_end_log_time($last, $currentTime, $endTime)
{

    $endTime = Carbon::parse($endTime);

    $currentTime = Carbon::parse($currentTime);

    if ($last == null || $last == "null") {

        $last = $endTime;

        if ($last > $currentTime) {

            $last = $currentTime;
        }
    } else {

        $last = Carbon::parse($last);

        if ($last > $endTime) {

            $last = $endTime;
        }

        if ($last > $currentTime) {

            $last = $currentTime;
        }
    }

    return $last;
}

function check_above_driver_log_exist(
    $driver_id,
    $startLogTime,
    $currentTime,
    $startTime,
    $endTime
) {

    $startLogTime = Carbon::parse($startLogTime);

    $driverLogExist = DriverShiftLog::where("driver_id", $driver_id)

        ->where("current_shift_status", 3)

        ->where("end_log_time", $startLogTime)

        ->exists();

    return $driverLogExist;
}

function check_lower_driving_log($driver_id, $endTime, $currentTime, $endTimes)
{
    if ($endTime == null || $endTime == "null") {
        return [];
    }

    $logEndTime = null;

    $totalTime = 0;

    $chainLogs = [];

    $currentTime = Carbon::parse($endTime);

    while (true) {

        // Find the log where start_log_time equals current time
        $log = DriverShiftLog::where("current_shift_status", 3)
            ->where("driver_id", $driver_id)
            ->where("is_add_approved", 1)
            ->where("start_log_time", $currentTime)
            ->first();

        if (!$log) {
            break; // End of chain
        }

        $chainLogs[] = $log;

        $currentTime = $log->end_log_time;
    }

    if ($chainLogs && count($chainLogs) > 0) {

        foreach ($chainLogs as $log) {

            $startLogTime = $log->start_log_time;

            $startLogTime = Carbon::parse($startLogTime);

            $endLogTime = $log->end_log_time;

            $endLogTime = check_end_log_time(

                $endLogTime,

                $currentTime,

                $endTimes

            );

            $endLogTime = Carbon::parse($endLogTime);

            if ($startLogTime > $endTimes) {

                $startLogTime = $endLogTime;
            }

            $timeDuration = $endLogTime->diffInSeconds($startLogTime);

            $logEndTime = $endLogTime;

            $totalTime += $timeDuration;
        }
    }

    if ($chainLogs && count($chainLogs) > 0) {
        return [$totalTime, true, $logEndTime];
    } else {
        return [$totalTime, false, $logEndTime];
    }
}

function check_above_driving_log($log, $driver_id)
{

    $currentTime = Carbon::parse($log->start_log_time);

    $chainLogs = [];

    while (true) {

        $log = DriverShiftLog::where("current_shift_status", 3)
            ->where("driver_id", $driver_id)
            ->where("end_log_time", $currentTime)
            ->first();

        if (!$log) {
            break;
        }

        $chainLogs[] = $log;

        $currentTime = $log->start_log_time;
    }

    // Sort the chain logs by start_log_time ascending
    usort($chainLogs, function ($a, $b) {
        return strtotime($a->start_log_time) <=> strtotime($b->start_log_time);
    });

    return $chainLogs;
}

function shift_cycle_start_check(
    $latestLog,
    $currentTime,
    $locationName,
    $ruleId,
    $type
) {

    $shift_start = 0;

    $cycle_start = 0;

    if ($latestLog) {

        $latestLogDriverId = $latestLog->driver_id_change;

        $latestLogDriverId = is_null($latestLogDriverId) ? $latestLog->driver_id : $latestLogDriverId;

        $currentTime = Carbon::parse($currentTime);

        $currentUnixTime = $currentTime->copy()->timestamp;

        if ($type == 1) {

            $latestLog->update([
                "end_log_time" => Carbon::parse($currentTime),
                "end_log_time_unix" => $currentUnixTime,
                "location_end" => $locationName,
            ]);
        }

        $allowedStatuses = [1, 2, 5];

        $logs = [];
        $currentLog = $latestLog;

        if (in_array($currentLog->current_shift_status, $allowedStatuses)) {
            $logs[] = $currentLog;
        }

        while (true) {

            if (empty($currentLog->start_log_time)) {
                break;
            }

            $nextLog = DriverShiftLog::where('driver_id', $latestLogDriverId)
                ->where('start_log_time', '<', $currentLog->start_log_time)
                ->where('is_add_approved', 1)
                ->orderBy('start_log_time', 'desc')
                ->first();

            if ($nextLog) {

                $emptyLog = DriverShiftLog::where('driver_id', $latestLogDriverId)
                    ->where('end_log_time', '=', $currentLog->start_log_time)
                    ->where('is_add_approved', 1)
                    ->orderBy('start_log_time', 'desc')
                    ->first();

                if (!$emptyLog) {
                    $gap = new \stdClass();
                    $gap->current_shift_status = 1;
                    $gap->start_log_time = $nextLog->end_log_time;
                    $gap->end_log_time = $currentLog->start_log_time;
                    $logs[] = $gap;
                }
            }

            if (
                !$nextLog || !in_array($nextLog->current_shift_status, $allowedStatuses)
            ) {
                break; // Stop if no log or status not allowed
            }

            $logs[] = $nextLog;
            $currentLog = $nextLog;
        }

        $logTotalTime = 0;

        if ($logs && count($logs) > 0) {
            foreach ($logs as $data) {
                if (!$data) {
                    continue;
                }
                $logStartTime = $data->start_log_time;
                $logEndTime = $data->end_log_time;

                $logEndTime = ($logEndTime == null || $logEndTime == 'null') ? $currentTime : $logEndTime;

                $logStartTime = Carbon::parse($logStartTime);
                $logEndTime = Carbon::parse($logEndTime);

                $logTotalTime += $logEndTime->diffInSeconds($logStartTime);
            }
        }

        if ($ruleId) {

            $cycleBreakRule = Rules::whereIn("id", $ruleId)
                ->where(function ($query) {
                    $query->where("reason", 8);
                })
                ->first();

            $shiftBreakRule = Rules::whereIn("id", $ruleId)
                ->where(function ($query) {
                    $query->where("reason", 7);
                })
                ->first();

            if ($shiftBreakRule) {

                $shiftMinHour = $shiftBreakRule->min_break_hour;

                $shiftMinSecond = $shiftMinHour * 3600;

                if ($logTotalTime > $shiftMinSecond) {

                    $shift_start = 1;
                }
            }

            if ($cycleBreakRule) {

                $cycleMinHour = $cycleBreakRule->min_break_hour;

                $cycleMinSeconds = $cycleMinHour * 3600;

                if ($logTotalTime > $cycleMinSeconds) {

                    $cycle_start = 1;
                }
            }
        }
        // }
    } else {

        $shift_start = 1;

        $cycle_start = 1;
    }

    return [$shift_start, $cycle_start];
}

function get_driver_activity_location($device, $key, $time)
{
    $locationName = null;

    if ($device) {
        $time = Carbon::parse($time);

        // Try to get the exact match first
        $vehicleLog = VehicleLogHistory::where('identifier', $device->serial_number)
            ->where('event_date_time', $time)
            ->first();

        // If exact match not found, get the closest before or after
        if (!$vehicleLog) {
            $beforeLog = VehicleLogHistory::where('identifier', $device->serial_number)
                ->where('event_date_time', '<', $time)
                ->orderBy('event_date_time', 'DESC')
                ->first();

            $afterLog = VehicleLogHistory::where('identifier', $device->serial_number)
                ->where('event_date_time', '>', $time)
                ->orderBy('event_date_time', 'ASC')
                ->first();

            if ($beforeLog && $afterLog) {
                $beforeDiff = abs($time->diffInSeconds($beforeLog->event_date_time));
                $afterDiff = abs($time->diffInSeconds($afterLog->event_date_time));
                $vehicleLog = $beforeDiff <= $afterDiff ? $beforeLog : $afterLog;
            } else {
                $vehicleLog = $beforeLog ?? $afterLog;
            }
        }

        if ($vehicleLog) {
            $locationData = json_decode($vehicleLog->location, true);

            if ($locationData && isset($locationData["GeoLocation"])) {
                $latitude = $locationData["GeoLocation"]["Latitude"];
                $longitude = $locationData["GeoLocation"]["Longitude"];

                $response = Http::get(
                    "https://maps.googleapis.com/maps/api/geocode/json",
                    [
                        "latlng" => $latitude . "," . $longitude,
                        "key" => $key,
                    ]
                );

                if ($response->successful()) {
                    $geocodeData = $response->json();

                    if (!empty($geocodeData["results"])) {
                        $locationName = $geocodeData["results"][0]["formatted_address"];
                    }
                }
            }
        }
    }

    return $locationName;
}

function vehicle_distance_odometer_data($startTime, $endTime, $vehicleAssign)
{

    $totalDistance = 0;

    $firstOdometer = 0;

    $lastOdometer = 0;

    $i = 0;

    if ($vehicleAssign) {

        foreach ($vehicleAssign as $data) {

            $serialNumber = $data->device->serial_number ?? null;

            // Retrieve logs within the time range
            $vehicleLogs = VehicleLogHistory::where("identifier", $serialNumber)
                ->whereBetween("event_date_time", [$startTime, $endTime])
                ->orderBy("event_date_time", "ASC")
                ->get();

            if ($vehicleLogs->isNotEmpty()) {

                $firstLog = $vehicleLogs->first();

                $lastLog = $vehicleLogs->last();

                if ($i == 1) {
                    $firstOdometer = $firstLog->odometer;
                }

                $lastOdometer = $lastLog->odometer;

                // Calculate the distance for logs before the start time
                $belowLog = VehicleLogHistory::where(
                    "identifier",
                    $serialNumber
                )
                    ->where("event_date_time", "<", $firstLog->event_date_time)
                    ->orderBy("event_date_time", "desc")
                    ->first();

                if ($belowLog) {

                    $totalDistance += $firstOdometer - $belowLog->odometer;
                }

                $i = 1;

                // Calculate the distances between logs in the range
                for ($i = 0; $i < $vehicleLogs->count() - 1; $i++) {

                    $currentOdometer = $vehicleLogs[$i]->odometer;

                    $nextOdometer = $vehicleLogs[$i + 1]->odometer;

                    if ($nextOdometer >= $currentOdometer) {

                        $totalDistance += $nextOdometer - $currentOdometer;
                    }
                }
            }
        }
    }

    return [
        "total_distance" => $totalDistance,
        "start_odometer" => $firstOdometer,
        "end_odometer" => $lastOdometer,
    ];
}

function malfunction_vehicle_check_data($vid, $date)
{

    $startDay = Carbon::parse($date)->startOfDay();

    $endDay = Carbon::parse($date)->endOfDay();

    $vehicle = Vehicle::whereIn("id", $vid)
        ->with("devices")
        ->get();

    $i = 0;

    $mailFunCheck = false;

    foreach ($vehicle as $val) {

        $serialNumber = $val['devices'][0]['serial_number'] ?? null;

        $malFunExist = VehicleLogHistory::where("identifier", $serialNumber)
            ->whereBetween("event_date_time", [$startDay, $endDay])
            ->where("message_reason", "MILON")
            ->exists();

        if ($i == 0) {

            if ($malFunExist) {

                $i = 1;

                $mailFunCheck = true;
            }
        }
    }

    return $mailFunCheck;
}

function getStateCode(string $stateName): ?string
{

    $states = [
        "Alabama" => "AL",
        "Alaska" => "AK",
        "Arizona" => "AZ",
        "Arkansas" => "AR",
        "California" => "CA",
        "Colorado" => "CO",
        "Connecticut" => "CT",
        "Delaware" => "DE",
        "Florida" => "FL",
        "Georgia" => "GA",
        "Hawaii" => "HI",
        "Idaho" => "ID",
        "Illinois" => "IL",
        "Indiana" => "IN",
        "Iowa" => "IA",
        "Kansas" => "KS",
        "Kentucky" => "KY",
        "Louisiana" => "LA",
        "Maine" => "ME",
        "Maryland" => "MD",
        "Massachusetts" => "MA",
        "Michigan" => "MI",
        "Minnesota" => "MN",
        "Mississippi" => "MS",
        "Missouri" => "MO",
        "Montana" => "MT",
        "Nebraska" => "NE",
        "Nevada" => "NV",
        "New Hampshire" => "NH",
        "New Jersey" => "NJ",
        "New Mexico" => "NM",
        "New York" => "NY",
        "North Carolina" => "NC",
        "North Dakota" => "ND",
        "Ohio" => "OH",
        "Oklahoma" => "OK",
        "Oregon" => "OR",
        "Pennsylvania" => "PA",
        "Rhode Island" => "RI",
        "South Carolina" => "SC",
        "South Dakota" => "SD",
        "Tennessee" => "TN",
        "Texas" => "TX",
        "Utah" => "UT",
        "Vermont" => "VT",
        "Virginia" => "VA",
        "Washington" => "WA",
        "West Virginia" => "WV",
        "Wisconsin" => "WI",
        "Wyoming" => "WY",
        "District of Columbia" => "DC",
        "American Samoa" => "AS",
        "Guam" => "GU",
        "Northern Mariana Islands" => "MP",
        "Puerto Rico" => "PR",
        "U.S. Virgin Islands" => "VI",
    ];

    return $states[trim($stateName)] ?? null;
}

function calculateFileCheckValue(array $lineCheckValues): string
{
    $sum = array_sum(array_map(fn($hex) => hexdec($hex), $lineCheckValues));

    $low16 = $sum & 0xffff;

    $highByte = ($low16 >> 8) & 0xff;

    $lowByte = $low16 & 0xff;

    // Circular left shift each byte 3 bits
    $highByteShifted = (($highByte << 3) | ($highByte >> 5)) & 0xff;

    $lowByteShifted = (($lowByte << 3) | ($lowByte >> 5)) & 0xff;

    // Combine shifted bytes
    $combined = ($highByteShifted << 8) | $lowByteShifted;

    $final = $combined ^ 0x969c; // XOR with 38556 decimal

    return strtoupper(str_pad(dechex($final), 4, "0", STR_PAD_LEFT));
}

function calculateCheckValue(string $line): string
{

    $sum = 0;

    for ($i = 0; $i < strlen($line); $i++) {

        $ord = ord($line[$i]);

        if (
            ($ord >= 65 && $ord <= 90) ||  // A-Z
            ($ord >= 97 && $ord <= 122) || // a-z
            ($ord >= 49 && $ord <= 57)     // 1-9
        ) {
            $value = $ord - 48;
        } else {
            $value = 0;
        }

        $sum += $value;
    }

    $low8 = $sum & 0xff;

    $shifted = (($low8 << 3) | ($low8 >> 5)) & 0xff;

    $xor = $shifted ^ 0x96;

    return strtoupper(str_pad(dechex($xor), 2, "0", STR_PAD_LEFT));
}

function computeLineCheckValue(string $line): string
{

    $sum = 0;

    // Convert characters using FMCSA Table 3 rules
    for ($i = 0; $i < strlen($line); $i++) {

        $char = $line[$i];
        $ord = ord($char);

        if (
            ($ord >= 65 && $ord <= 90) || // A-Z
            ($ord >= 97 && $ord <= 122) || // a-z
            ($ord >= 49 && $ord <= 57)
        ) {
            // 1-9
            $value = $ord - 48;
        } else {
            $value = 0;
        }

        $sum += $value;
    }

    // Get low 8 bits
    $low8 = $sum & 0xff;

    // Perform 3 circular left shifts
    $shifted = (($low8 << 3) | ($low8 >> 5)) & 0xff;

    // XOR with 150 (0x96)
    $xor = $shifted ^ 0x96;

    // Return the hex representation (uppercase, 2 chars)
    return strtoupper(str_pad(dechex($xor), 2, "0", STR_PAD_LEFT));
}

function driver_log_time_data_edit($userId, $create, $last, $currentTime, $log)
{

    if ($log) {

        $logId = $log->id;

        $create = Carbon::parse($create);

        $last = Carbon::parse($last);

        $driverLog = DriverShiftLog::where('driver_id', $userId)
            ->where('id', '!=', $logId)
            ->orderBy('start_log_time')
            ->get();

        foreach ($driverLog as $logs) {

            $start = Carbon::parse($logs->start_log_time);

            $end = $logs->end_log_time
                ? Carbon::parse($logs->end_log_time)
                : Carbon::parse($currentTime);

            // CASE 1: fully inside new range → delete
            if ($start >= $create && $end <= $last) {

                $logs->delete();
                continue;
            }

            // CASE 2: split required
            if ($start < $create && $end > $last) {

                // update first part
                $logs->update([
                    'end_log_time' => $create,
                    'end_log_time_unix' => $create->timestamp
                ]);

                // create second part
                DriverShiftLog::create([
                    'driver_id' => $logs->driver_id,
                    'vehicle_id' => $logs->vehicle_id,
                    'start_log_time' => $last,
                    'end_log_time' => $end,
                    'start_log_time_unix' => $last->timestamp,
                    'end_log_time_unix' => $end->timestamp,
                    'current_shift_status' => $logs->current_shift_status,
                    'location_name' => $logs->location_name,
                    'location_end' => $logs->location_end
                ]);

                continue;
            }

            // CASE 3: overlap start
            if ($start < $create && $end > $create) {

                $logs->update([
                    'end_log_time' => $create,
                    'end_log_time_unix' => $create->timestamp
                ]);
            }

            // CASE 4: overlap end
            if ($start < $last && $end > $last) {

                $logs->update([
                    'start_log_time' => $last,
                    'start_log_time_unix' => $last->timestamp
                ]);
            }
        }

        return true;

    }

    return false;

}

function shift_cycle_start_check_edit(
    $latestLog,
    $currentTime,
    $locationName,
    $ruleId,
    $type
) {

    $shift_start = 0;

    $cycle_start = 0;

    if ($latestLog) {

        $currentTime = Carbon::parse($currentTime);

        $currentUnixTime = $currentTime->copy()->timestamp;

        if ($type == 1) {

            $latestLog->update([

                "end_log_time" => Carbon::parse($currentTime),

                "end_log_time_unix" => $currentUnixTime,

                "location_end" => $locationName,

            ]);
        }

        $allowedStatuses = [1, 2, 5];

        $logs = [];
        $currentLog = $latestLog;

        if (in_array($currentLog->current_shift_status_change, $allowedStatuses)) {
            $logs[] = $currentLog;
        }

        while (true) {
            $nextLog = DriverShiftLog::where('driver_id', $currentLog->driver_id)
                ->where('start_log_time', '<', $currentLog->start_log_time_change)
                ->where('is_add_approved', 1)
                ->orderBy('start_log_time', 'desc')
                ->first();

            if (
                !$nextLog || !in_array($nextLog->current_shift_status_change, $allowedStatuses)
            ) {
                break; // Stop if no log or status not allowed
            }

            $logs[] = $nextLog;
            $currentLog = $nextLog;
        }

        $logTotalTime = 0;

        if ($logs && count($logs) > 0) {
            foreach ($logs as $data) {
                $logStartTime = $data->start_log_time_change;
                $logEndTime = $data->end_log_time_change;

                $logEndTime = ($logEndTime == null || $logEndTime == 'null') ? $currentTime : $logEndTime;

                $logStartTime = Carbon::parse($logStartTime);
                $logEndTime = Carbon::parse($logEndTime);

                $logTotalTime += $logEndTime->diffInSeconds($logStartTime);
            }
        }

        if ($ruleId) {

            $cycleBreakRule = Rules::whereIn("id", $ruleId)
                ->where(function ($query) {
                    $query->where("reason", 8);
                })
                ->first();

            $shiftBreakRule = Rules::whereIn("id", $ruleId)
                ->where(function ($query) {
                    $query->where("reason", 7);
                })
                ->first();

            if ($shiftBreakRule) {

                $shiftMinHour = $shiftBreakRule->min_break_hour;

                $shiftMinSecond = $shiftMinHour * 3600;

                if ($logTotalTime > $shiftMinSecond) {

                    $shift_start = 1;
                }
            }

            if ($cycleBreakRule) {

                $cycleMinHour = $cycleBreakRule->min_break_hour;

                $cycleMinSeconds = $cycleMinHour * 3600;

                if ($logTotalTime > $cycleMinSeconds) {

                    $cycle_start = 1;
                }
            }
        }
        // }
    } else {

        $shift_start = 1;

        $cycle_start = 1;
    }

    return [$shift_start, $cycle_start];
}

function get_current_time_driver($driverId)
{
    $user = User::find($driverId);
    $masterId = $user->master_id;
    $masterUser = User::find($masterId);
    $masterTimezone = $masterUser->timezone;
    $currentTime = Carbon::parse()->setTimezone($masterTimezone)->toDateTimeLocalString();
    $currentTime = Carbon::parse($currentTime);

    return $currentTime;
}

function get_driver_activity_odometer($device, $time)
{
    $time = Carbon::parse($time);
    $odometer = null;

    if ($device) {
        // Try to get the exact match first
        $vehicleLog = VehicleLogHistory::where('identifier', $device->serial_number)
            ->where('event_date_time', $time)
            ->first();

        // If exact match not found, get the closest before or after
        if (!$vehicleLog) {
            $beforeLog = VehicleLogHistory::where('identifier', $device->serial_number)
                ->where('event_date_time', '<', $time)
                ->orderBy('event_date_time', 'DESC')
                ->first();

            $afterLog = VehicleLogHistory::where('identifier', $device->serial_number)
                ->where('event_date_time', '>', $time)
                ->orderBy('event_date_time', 'ASC')
                ->first();

            // Choose the closest one
            if ($beforeLog && $afterLog) {
                $beforeDiff = abs($time->diffInSeconds($beforeLog->event_date_time));
                $afterDiff = abs($time->diffInSeconds($afterLog->event_date_time));
                $vehicleLog = $beforeDiff <= $afterDiff ? $beforeLog : $afterLog;
            } else {
                $vehicleLog = $beforeLog ?? $afterLog;
            }
        }

        if ($vehicleLog) {
            $odometer = $vehicleLog->obd_odometer;
        }
    }

    return $odometer;
}

function get_driver_activity_rpm($device, $time)
{
    $time = Carbon::parse($time);
    $engineHour = 0;

    if ($device) {
        // Try to get the exact match first
        $vehicleLog = VehicleLogHistory::where('identifier', $device->serial_number)
            ->where('event_date_time', $time)
            ->first();

        // If exact match not found, get the closest before or after
        if (!$vehicleLog) {
            $beforeLog = VehicleLogHistory::where('identifier', $device->serial_number)
                ->where('event_date_time', '<', $time)
                ->orderBy('event_date_time', 'DESC')
                ->first();

            $afterLog = VehicleLogHistory::where('identifier', $device->serial_number)
                ->where('event_date_time', '>', $time)
                ->orderBy('event_date_time', 'ASC')
                ->first();

            // Choose the closest one
            if ($beforeLog && $afterLog) {
                $beforeDiff = abs($time->diffInSeconds($beforeLog->event_date_time));
                $afterDiff = abs($time->diffInSeconds($afterLog->event_date_time));
                $vehicleLog = $beforeDiff <= $afterDiff ? $beforeLog : $afterLog;
            } else {
                $vehicleLog = $beforeLog ?? $afterLog;
            }
        }

        if ($vehicleLog) {
            $engineHour = $vehicleLog->obd_engine_rpm;
        }
    }

    return $engineHour;
}

function check_log_device_driver_exist($driverId, $create, $last, $logId)
{

    $data = [];

    $create = Carbon::parse($create);

    $last = Carbon::parse($last);

    // Get user's timezone
    $userInfo = UserInfo::where("user_id", $driverId)->first();

    $timezone = $userInfo->home_terminal_timezone;

    $currentTime = Carbon::parse()
        ->setTimezone($timezone)
        ->toDateTimeLocalString();

    $currentTime = Carbon::parse($currentTime);

    $checkLog = DriverShiftLog::where("driver_id", $driverId)
        ->where("id", "!=", $logId)
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
        $exists = false;

        // Handle when there's exactly one log
        if (count($checkLog) === 1) {
            $log = $checkLog[0];

            if ($log->end_log_time === null || $log->end_log_time === 'null') {
                $status = false;
            } else {
                $exists = $checkLog->contains(function ($log) {
                    return $log->current_shift_status == 3 &&
                        $log->system_entry == 1 &&
                        $log->is_add_approved == 1;
                });
                $status = $exists;
            }
        } else {
            // Multiple logs, check for matching condition
            $exists = $checkLog->contains(function ($log) {
                return $log->current_shift_status == 3 &&
                    $log->system_entry == 1 &&
                    $log->is_add_approved == 1;
            });
            $status = $exists;
        }

        $data = [
            "exists" => true,
            "status" => $status,
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

function log_violation_time_fix($driverId, $logId, $create, $last, $currentTime, $duration, $type)
{

    $logData = DriverShiftLog::where("driver_id", $driverId)
        ->where("is_add_approved", 1)
        ->whereNotIn("current_shift_status", [1, 2, 5])
        ->where(function ($query) use ($create, $last, $currentTime) {
            $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                $subQuery
                    // Check for logs where any part of the range between $create and $last overlaps with start_log_time and end_log_time
                    ->where(function ($overlapQuery) use ($create, $last, $currentTime) {
                        $overlapQuery
                            ->where("start_log_time", "<=", $last)
                            ->whereRaw("IFNULL(end_log_time, ?) >= ?", [
                                $currentTime,
                                $create,
                            ]);
                    })
                    // Exclude cases where $create equals end_log_time or $last equals start_log_time
                    ->whereRaw("IFNULL(end_log_time, ?) != ?", [
                        $currentTime,
                        $create,
                    ])
                    ->whereRaw("? != start_log_time", [$last]);
            });
        })
        ->orderBy("start_log_time", "DESC")
        ->get();

    $finalViolationTime = [];

    if ($logData && count($logData) > 0) {

        foreach ($logData as $log) {

            $startLogTime = $log->start_log_time;

            $endLogTime = $log->end_log_time;

            $startLogTime = Carbon::parse($startLogTime);

            $endLogTime = ($endLogTime == null || $endLogTime == 'null') ? Carbon::parse($currentTime) : Carbon::parse($endLogTime);

            if ($endLogTime < $create) {
                break;
            }

            if ($last < $startLogTime) {
                break;
            }

            $timeData = create_end_time(
                $log,
                $create,
                $log,
                $last,
                $currentTime
            );

            $finalStartTime = $timeData[0];
            $finalEndTime = $timeData[1];

            $finalStartTime = Carbon::parse($finalStartTime);
            $finalEndTime = Carbon::parse($finalEndTime);

            $finalTimeDiff = $finalEndTime->diffInSeconds($finalStartTime);

            if ($finalTimeDiff > 0) {

                if ($type == 1) {

                    $finalViolationTime[] = [
                        "shift_id" => $logId,
                        "violation_duration" => secondsToTime($finalTimeDiff),
                        "violation_startTime" => Carbon::parse($finalStartTime),
                        "violation_endTime" => Carbon::parse($finalEndTime),
                    ];
                } else if ($type == 2) {

                    $finalViolationTime[] = [
                        "shift_id" => $logId,
                        "violation_duration" => secondsToTime($finalTimeDiff),
                        "violation_startTime" => Carbon::parse($finalStartTime),
                        "violation_endTime" => Carbon::parse($finalEndTime),
                    ];
                } else if ($type == 3) {

                    $finalViolationTime[] = [
                        "driver_id" => $logId,
                        "drive_violate" => secondsToTime($finalTimeDiff),
                        "drive_start_time" => Carbon::parse($finalStartTime),
                        "drive_end_time" => Carbon::parse($finalEndTime),
                    ];
                }
            }

            if ($finalStartTime == $create) {
                break;
            }
        }
    }

    return $finalViolationTime;
}

function log_add_unidentified_approval($log, $userId, $currentTime, $type)
{
    if (!$log) {
        return;
    }

    $logId = $log->id;

    $currentTime = Carbon::parse($currentTime);
    $create = Carbon::parse($log->start_log_time);

    $existDriverLog = DriverShiftLog::where("driver_id", $userId)
        ->where("id", "!=", $logId)
        ->where(function ($q) use ($create) {
            $q->where("start_log_time", $create)
                ->orWhere("end_log_time", $create);
        })
        ->first();

    if ($existDriverLog) {
        $log->delete();
        return;
    }

    $logBtw = DriverShiftLog::where('driver_id', $userId)
        ->where('start_log_time', '<', $create)
        ->whereRaw('COALESCE(end_log_time, ?) > ?', [$currentTime, $create])
        ->first();

    if (!$logBtw) {
        return;
    }

    $endTime = $logBtw->end_log_time;

    $endLogTime = $endTime
        ? Carbon::parse($endTime)
        : $currentTime;

    if ($endTime) {
        $log->update([
            "end_log_time" => $endLogTime,
            "end_log_time_unix" => $endLogTime->timestamp
        ]);
    }

    $log->update([
        "is_unidentified" => 0,
        "is_add_approved" => 1,
        "is_edit" => 1,
        "is_active" => 1,
    ]);

    $logBtw->update([
        "end_log_time" => $create,
        "end_log_time_unix" => $create->timestamp
    ]);
}

function bluetooth_log_add($userId, $create, $last, $currentTime)
{

    $logs = DriverShiftLog::where('driver_id', $userId)
        ->orderBy('start_log_time')
        ->get();

    foreach ($logs as $log) {

        $start = Carbon::parse($log->start_log_time);
        $end = $log->end_log_time
            ? Carbon::parse($log->end_log_time)
            : Carbon::parse($currentTime);

        // CASE 1: fully inside new range → delete
        if ($start >= $create && $end <= $last) {
            $log->delete();
            continue;
        }

        // CASE 2: split required
        if ($start < $create && $end > $last) {

            // update first part
            $log->update([
                'end_log_time' => $create,
                'end_log_time_unix' => $create->timestamp
            ]);

            // create second part
            DriverShiftLog::create([
                'driver_id' => $log->driver_id,
                'vehicle_id' => $log->vehicle_id,
                'start_log_time' => $last,
                'end_log_time' => $end,
                'start_log_time_unix' => $last->timestamp,
                'end_log_time_unix' => $end->timestamp,
                'current_shift_status' => $log->current_shift_status,
                'location_name' => $log->location_name,
                'location_end' => $log->location_end
            ]);

            continue;
        }

        // CASE 3: overlap start
        if ($start < $create && $end > $create) {
            $log->update([
                'end_log_time' => $create,
                'end_log_time_unix' => $create->timestamp
            ]);
        }

        // CASE 4: overlap end
        if ($start < $last && $end > $last) {
            $log->update([
                'start_log_time' => $last,
                'start_log_time_unix' => $last->timestamp
            ]);
        }
    }

    return true;
}

function update_latest_end_time_log($userId)
{

    $latestLog = DriverShiftLog::where("driver_id", $userId)
        ->where("is_add_approved", 1)
        ->where('is_unidentified', 0)
        ->latest("start_log_time")
        ->first();

    if ($latestLog) {
        $latestLog->update([
            "end_log_time" => null,
            "end_log_time_unix" => null,
        ]);
    }
}