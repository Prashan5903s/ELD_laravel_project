<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Device;
use App\Jobs\AlertJob;
use App\Models\Document;
use App\Models\UserInfo;
use App\Models\UserAlert;
use App\Models\ListOption;
use App\Models\RuleAssign;
use App\Models\VehicleAssign;
use App\Models\DriverShiftLog;
use Illuminate\Console\Command;
use App\Models\VehicleLogHistory;
use Illuminate\Support\Facades\Log;

class AlertCommand extends Command
{

    /**

     * The name and signature of the console command.

     *

     * @var string

     */

    protected $signature = 'alert:user';

    /**

     * The console command description.

     *

     * @var string

     */

    protected $description = 'This is command is made to alert users for diffrent messages';



    /**

     * Execute the console command.

     *

     * @return int

     */

    public function handle()
    {

        try {

            $userAlerts = UserAlert::select(
                'type_id',
                'trigger_range',
                'trigger_week_day',
                'trigger_start_time',
                'trigger_end_time',
                'user_id',
                'vehicle_id',
                'method',
                'frequency',
                'schedule_time',
                'schedule_day_of_month',
                'schedule_day',
                'recipient_id',
                'created_by'
            )
                ->with(['ListOption:list_id,option_id,short_name,type', 'user'])
                ->where('status', 1)
                ->get();

            $triggerTimes = ListOption::where('list_id', 'trigger_time')->pluck('option_id');

            $datas = [];

            foreach ($userAlerts as $alert) {

                $timezone = $alert->user->timezone;

                $master_email = $alert->user->email;

                $master_id = $alert->user->id;

                $method = $alert->method;

                $shortName = $alert->listOption->short_name;

                $rangess = $alert->trigger_range;

                $weekss = $alert->trigger_week_day;

                $timeRangeCheck = false;

                $timeToday = Carbon::parse()->setTimezone($timezone)->toDateTimeLocalString();

                $timeToday = Carbon::parse($timeToday);

                $currentTime = $timeToday;

                $recipientIDs = explode(',', $alert->recipient_id);

                $recipientUserId = User::whereIn('id', $recipientIDs)->pluck('id')->toArray();

                $recipientEmail = User::whereIn('id', $recipientIDs)->pluck('email')->toArray();

                if ($alert->listOption->type == 1) { // Driver-based alerts

                    $userIds = explode(',', $alert->user_id);

                    $drivers = User::whereIn('id', $userIds)->get();

                    foreach ($drivers as $driver) {

                        $userInfo = UserInfo::where('user_id', $driver->id)->first();

                        if (!$userInfo)

                            continue;

                        $timezone = $userInfo->home_terminal_timezone;

                        $currentTime = Carbon::parse()->setTimezone($timezone)->toDateTimeLocalString();

                        $currentTime = Carbon::parse($currentTime);

                        $currentTimeDay = $currentTime->format('l'); // Get current weekday (e.g., Monday)

                        $currentTimeToday = $currentTime->format('H:i:s'); // Get current time in HH:MM:SS format

                        if ($rangess == 4) {

                            $week = explode(',', $weekss);

                            $startTime = $alert->trigger_start_time;

                            $endTime = $alert->trigger_end_time;

                            $startTime = Carbon::parse($startTime)->format('H:i:s');

                            $endTime = Carbon::parse($endTime)->format('H:i:s');

                            $isInWeek = in_array($currentTimeDay, $week);

                            // Check if the current time falls within the start and end time range
                            $isInTimeRange = ($currentTimeToday >= $startTime && $currentTimeToday <= $endTime);

                            $timeRangeCheck = ($isInWeek && $isInTimeRange);
                        } elseif ($rangess == 3) {

                            $week = [

                                "Sunday",

                                "Saturday",

                            ];

                            $isInWeek = in_array($currentTimeDay, $week);

                            $timeRangeCheck = ($isInWeek);
                        } else if ($rangess == 2) {

                            $week = [

                                "Monday",

                                "Tuesday",

                                "Wednesday",

                                "Thursday",

                                "Friday",

                            ];

                            $isInWeek = in_array($currentTimeDay, $week);

                            $timeRangeCheck = ($isInWeek);
                        } else {

                            $timeRangeCheck = true;
                        }

                        if ($timeRangeCheck) {

                            if ($shortName == "HOS") {

                                $logData = driver_log_time($driver->id, $currentTime);

                                $vName = ($logData[1] && !empty($logData[1])) ? $logData[1]['name'] : '';

                                $shift = timeToSeconds($logData[4]);

                                $cycle = timeToSeconds($logData[6]);

                                $drive = timeToSeconds($logData[7]);

                                $break = timeToSeconds($logData[8]);

                                $rules = RuleAssign::where('user_id', $driver->id)
                                    ->with('rule')
                                    ->get()
                                    ->keyBy('rule_id'); // Ensures you can access rules by their ID

                                // Validate rule presence

                                if (!isset($rules[1]) || !isset($rules[2]) || !isset($rules[5]) || (!isset($rules[3]) && !isset($rules[4]))) {

                                    continue;
                                }



                                // Compute limits safely

                                $limits = [

                                    'shift' => isset($rules[1]) ? ($rules[1]->rule->max_hour_limit * 3600 - $shift) / 60 : null,

                                    'cycle' => isset($rules[3]) ? ($rules[3]->rule->max_hour_limit * 3600 - $cycle) / 60 : (isset($rules[4]) ? $rules[4]->rule->max_hour_limit * 3600 - $cycle : null),

                                    'drive' => isset($rules[2]) ? ($rules[2]->rule->max_hour_limit * 3600 - $drive) / 60 : null,

                                    'break' => isset($rules[5]) ? ($rules[5]->rule->max_hour_limit * 3600 - $break) / 60 : null,

                                ];



                                foreach ($limits as $reason => $remainingTime) {

                                    if ($triggerTimes->contains($remainingTime)) {

                                        $datas[] = compact('method', 'currentTime') + [

                                            'type' => 1,

                                            'range' => $rangess,

                                            'reason' => $reason,

                                            'timess' => $remainingTime,

                                            'template_mail_id' => 3,

                                            'template_notify_id' => 5,

                                            'driver_id' => $driver->id,

                                            'masterId' => $driver->masterId,

                                            'vehicle_name' => $vName,

                                            'first_name' => $driver->first_name ?? "",

                                            'last_name' => $driver->last_name ?? "",

                                            'email' => $driver->email,

                                            'location' => '',

                                            'master_id' => $master_id,

                                            'master_email' => $master_email,

                                            'recipientId' => $recipientUserId,

                                            'recipientEmail' => $recipientEmail,

                                            'timeSlot' => $currentTime,

                                        ];
                                    }
                                }
                            } elseif ($shortName == "DOCS_UPLOAD") {

                                $documents = Document::where('is_notify', 0)->where('driver_id', $driver->id)->with('listOption')->get();

                                if ($documents && count($documents) > 0) {

                                    foreach ($documents as $docs) {

                                        $document_name = $docs->listOption->title;

                                        $datas[] = compact('method') + [

                                            'type' => 2,

                                            'image' => $docs->image,

                                            'range' => $rangess,

                                            'timess' => $docs->created_at,

                                            'document_name' => $document_name,

                                            'reason' => "document",

                                            'template_mail_id' => 9,

                                            'template_notify_id' => 10,

                                            'driver_id' => $driver->id,

                                            'masterId' => $driver->masterId,

                                            'first_name' => $driver->first_name ?? "",

                                            'last_name' => $driver->last_name ?? "",

                                            'email' => $driver->email,

                                            'location' => '',

                                            'master_id' => $master_id,

                                            'master_email' => $master_email,

                                            'recipientId' => $recipientUserId,

                                            'recipientEmail' => $recipientEmail,

                                            'timeSlot' => $currentTime,

                                        ];



                                        $docs->update(['is_notify' => 1]);
                                    }
                                }
                            }
                        }
                    }
                } else { // Vehicle-based alerts



                    $currentTimeDay = $timeToday->format('l'); // Get current weekday (e.g., Monday)

                    $currentTimeToday = $timeToday->format('H:i:s'); // Get current time in HH:MM:SS format



                    if ($rangess == 4) {

                        $week = explode(',', $weekss);



                        $startTime = $alert->trigger_start_time;

                        $endTime = $alert->trigger_end_time;



                        $startTime = Carbon::parse($startTime)->format('H:i:s');

                        $endTime = Carbon::parse($endTime)->format('H:i:s');



                        $isInWeek = in_array($currentTimeDay, $week);



                        // Check if the current time falls within the start and end time range

                        $isInTimeRange = ($currentTimeToday >= $startTime && $currentTimeToday <= $endTime);



                        $timeRangeCheck = ($isInWeek && $isInTimeRange);
                    } elseif ($rangess == 3) {

                        $week = [

                            "Sunday",

                            "Saturday",

                        ];



                        $isInWeek = in_array($currentTimeDay, $week);



                        $timeRangeCheck = ($isInWeek);
                    } else if ($rangess == 2) {

                        $week = [

                            "Monday",

                            "Tuesday",

                            "Wednesday",

                            "Thursday",

                            "Friday",

                        ];



                        $isInWeek = in_array($currentTimeDay, $week);



                        $timeRangeCheck = ($isInWeek);
                    } else {



                        $timeRangeCheck = true;
                    }



                    if ($timeRangeCheck) {



                        $vehicleIds = explode(',', $alert->vehicle_id);

                        $devices = Device::whereIn('vehicle_id', $vehicleIds)->with('vehicle')->get();



                        foreach ($devices as $device) {



                            $vId = $device->vehicle->id;



                            $logs = VehicleLogHistory::where('identifier', $device->serial_number)

                                ->where('message_reason', $shortName)->where('is_send', 0)->get();



                            foreach ($logs as $log) {



                                $timeData = Carbon::parse($log->event_date_time);



                                // Find the driver for this log entry

                                $driverLog = DriverShiftLog::where('vehicle_id', $vId)

                                    ->where(function ($query) use ($timeData, $currentTime) {

                                        $query->whereRaw('? BETWEEN start_log_time AND COALESCE(end_log_time, ?)', [$timeData, $currentTime]);
                                    })

                                    ->with('driver:id,first_name,last_name')

                                    ->first();



                                // Get the driver name, or mark as "Unidentified" if no driver found

                                $driverName = "Unidentified driver";

                                if ($driverLog && $driverLog->driver) {
                                    $driverName = trim("{$driverLog->driver->first_name} {$driverLog->driver->last_name}");
                                }

                                $datas[] = compact('method') + [

                                    'type' => 3,

                                    'method' => $method,

                                    'range' => $rangess,

                                    'reason' => $log->message_reason,

                                    'time' => '',

                                    'template_mail_id' => 4,

                                    'template_notify_id' => 6,

                                    'timess' => $log->event_date_time,

                                    'driver_name' => $driverName,

                                    'vehicle_name' => $device->vehicle->name,

                                    'location' => $log->location,

                                    'master_id' => $master_id,

                                    'master_email' => $master_email,

                                    'recipientId' => $recipientUserId,

                                    'recipientEmail' => $recipientEmail,

                                    'timeSlot' => $currentTime,

                                ];

                                $log->update(['is_send' => 1]);
                            }
                        }
                    }
                }
            }

            if ($datas && count($datas) > 0) {

                dispatch_now(new AlertJob($datas));
            }

            // your code
        } catch (\Exception $e) {

            Log::error('Error processing alerts ABCD: ' . $e->getMessage());
        }

        $this->info('Alert has been sent to user');
    }
}
