<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Device;
use App\Models\Template;
use App\Models\LogSession;
use App\Models\VehicleAssign;
use App\Models\DriverShiftLog;
use App\Models\RuleAssign;
use App\Models\VehicleLogHistory;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UnidentifiedNotification;


class UpdateDrivingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:driver-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update driver log to driving when distance miles have crossed 5 miles';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $template = Template::where('template_id', 11)->first();
        $devices = Device::with('vehicle')->get();
        $mapKey = config('app.Map_key');

        foreach ($devices as $device) {

            $vehicle = $device->vehicle;
            if (!$vehicle)
                continue;

            $masterUser = User::find($device->created_by);

            if (!$masterUser)
                continue;

            $timezone = $masterUser->timezone;

            $currentTime = Carbon::parse()->setTimezone($timezone)->toDateTimeLocalString();
            $currentTime = Carbon::parse($currentTime);

            $vehicleId = $device->vehicle_id;

            if (!$vehicleId || !is_numeric($vehicleId))
                continue;

            $driverIds = VehicleAssign::where('vechile_id', $vehicleId)
                ->pluck('driver_id')
                ->toArray();

            if (empty($driverIds))
                continue;

            $vehicleName = $vehicle->name;
            $vehicleVIN = $vehicle->vin;
            $serialNumber = $device->serial_number;

            // Get latest log per driver
            $logs = LogSession::whereIn('user_id', $driverIds)
                ->orderByDesc('created_at')
                ->get()
                ->groupBy('user_id')
                ->map(fn($group) => $group->first());

            $userId = null;
            $unidentified = true;

            if ($logs->count() > 0) {
                $iLogs = $logs->filter(fn($log) => $log->log_status === 'i');
                if ($iLogs->count() === 1) {
                    $userId = $iLogs->keys()->first();
                    $unidentified = false;
                }
            }

            $vehicleLogs = VehicleLogHistory::where('identifier', $serialNumber)
                ->where('is_updated', 0)
                ->orderBy('event_time', 'ASC')
                ->get();

            foreach ($vehicleLogs as $log) {
                $eventStart = Carbon::parse($log->event_date_time);

                if ($currentTime->lte($eventStart))
                    continue;

                $message = $log->message_reason;
                $speed = $log->speed;
                $status = ($speed >= 5 && $message != 'POWER_CUT') ? 3 : 1;

                $locationStart = get_driver_activity_location($device, $mapKey, $eventStart);
                $locationEnd = get_driver_activity_location($device, $mapKey, $currentTime);
                $odometerStart = get_driver_activity_odometer($device, $eventStart);
                $odometerEnd = get_driver_activity_odometer($device, $currentTime);
                $engineHour = get_driver_activity_rpm($device, $currentTime);

                $notificationText = str_replace(
                    ['{{ serial_number }}', '{{ time }}', '{{ name }}'],
                    [$vehicleVIN, $currentTime->format('j M Y h:i a'), $vehicleName],
                    $template->template_text
                );

                $notificationRecipients = array_merge($driverIds, [$device->created_by]);
                $approval = true;

                if (!$userId) {

                    $logRecord = DriverShiftLog::create([
                        'driver_id' => $userId,
                        'vehicle_id' => $vehicleId,
                        'current_shift_status' => $status,
                        'system_entry' => 1,
                        'location_name' => $locationStart,
                        'location_end' => $locationEnd,
                        'odometer' => $odometerStart,
                        'odometer_end' => $odometerEnd,
                        'engineHour' => $engineHour,
                        'start_log_time' => $eventStart,
                        'start_log_time_unix' => $eventStart->timestamp,
                        'end_log_time' => null,
                        'end_log_time_unix' => null,
                        'is_add_approved' => $unidentified ? 0 : 1,
                        'log_type' => $unidentified ? 6 : 3,
                        'is_unidentified' => $unidentified ? 1 : 0,
                        'created_by' => $userId,
                        'created_at' => $currentTime,
                    ]);

                    // $ruleIds = RuleAssign::where('user_id', $userId)->pluck('rule_id');
                    // $shiftData = shift_cycle_start_check($logRecord, $currentTime, $locationStart, $ruleIds, 0);

                    // if ($shiftData) {
                    //     $logRecord->update([
                    //         'shift_start' => $shiftData[0] ?? 0,
                    //         'cycle_start' => $shiftData[1] ?? 0,
                    //     ]);
                    // }

                } else {

                    $driverLog = DriverShiftLog::where('driver_id', $userId)
                        ->where('is_unidentified', 0)
                        ->orderBy('start_log_time', "DESC")
                        ->first();

                    $shouldCreateNewLog = false;

                    if ($driverLog) {

                        $logExist = ($driverLog->current_shift_status == $status && $driverLog->system_entry == 1);

                        if (!$logExist && $driverLog->start_log_time < $eventStart) {

                            // Close previous log
                            $driverLog->update([
                                'end_log_time' => $eventStart,
                                'end_log_time_unix' => $eventStart->timestamp,
                                'location_end' => $locationEnd,
                                'odometer_end' => $odometerEnd,
                            ]);

                            $shouldCreateNewLog = true;
                        }
                    } else {

                        // No existing log, create a new one
                        $shouldCreateNewLog = true;
                    }

                    if ($shouldCreateNewLog) {
                        $logRecord = DriverShiftLog::create([
                            'driver_id' => $userId,
                            'vehicle_id' => $vehicleId,
                            'current_shift_status' => $status,
                            'system_entry' => 1,
                            'location_name' => $locationStart,
                            'location_end' => $locationEnd,
                            'odometer' => $odometerStart,
                            'odometer_end' => $odometerEnd,
                            'engineHour' => $engineHour,
                            'start_log_time' => $eventStart,
                            'start_log_time_unix' => $eventStart->timestamp,
                            'end_log_time' => null,
                            'end_log_time_unix' => null,
                            'is_add_approved' => $unidentified ? 0 : 1,
                            'log_type' => $unidentified ? 6 : 3,
                            'is_unidentified' => $unidentified ? 1 : 0,
                            'created_by' => $userId,
                            'created_at' => $currentTime,
                        ]);

                        // Apply rules and determine shift/cycle start
                        $ruleIds = RuleAssign::where('user_id', $userId)->pluck('rule_id');
                        $shiftData = shift_cycle_start_check($logRecord, $currentTime, $locationStart, $ruleIds, 0);

                        if ($shiftData) {
                            $logRecord->update([
                                'shift_start' => $shiftData[0] ?? 0,
                                'cycle_start' => $shiftData[1] ?? 0,
                            ]);
                        }
                    }
                }

                if ($unidentified) {
                    $usersToNotify = User::whereIn('id', $notificationRecipients)->get();
                    Notification::send($usersToNotify, new UnidentifiedNotification($notificationText));
                }

                $log->update(['is_updated' => 1]);
            }
        }

        $this->info('Driver log is updated to driving.');

        return Command::SUCCESS;
    }
}
