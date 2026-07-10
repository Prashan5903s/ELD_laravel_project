<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Device;
use App\Models\BluetoothLogData;
use App\Models\RuleAssign;
use App\Models\UserInfo;
use App\Models\DriverShiftLog;
use App\Models\VehicleLogHistory;
use App\Notifications\NonDrivingNotification;

class NotifyNonDriving extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nondriving:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To notify driving log is setup but vehicle is not moving';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $transportIds = User::where('user_type', 'TR')->pluck('id');

        $drivers = User::where('user_type', 'U')
            ->whereIn('master_id', $transportIds)
            ->get();

        function conTimezone($timezone, $time)
        {
            // Parse the given time string and set it to the specified timezone
            $convertedTime = Carbon::parse($time)->setTimezone($timezone);

            return $convertedTime->toDateTimeString();
        }

        if ($drivers && count($drivers) > 0) {

            foreach ($drivers as $data) {
                $shiftStart = 0;
                $cycleStart = 0;
                $locationName = "";

                $id = $data->id;

                $rule_ids = RuleAssign::where('user_id', $id)
                    ->pluck('rule_id');


                $master = $data->master_id;

                $userInfo = UserInfo::where('user_id', $id)->first();

                if (!$userInfo || empty($userInfo->home_terminal_timezone)) {
                    continue;
                }

                $timeZone = $userInfo->home_terminal_timezone;

                //Current time of today
                $currentTime = Carbon::now()->setTimezone($timeZone)->toDateTimeLocalString();

                $currentTime = Carbon::parse($currentTime);

                $startTime = Carbon::parse($currentTime)->startOfDay();

                $endTime = Carbon::parse($currentTime)->endOfDay();

                $driverLog = DriverShiftLog::where('driver_id', $id)
                    ->where('is_add_approved', 1)
                    ->latest('start_log_time')
                    ->first();

                if ($driverLog) {

                    $currentStatus = $driverLog->current_shift_status;

                    $timeData = create_end_time($driverLog, $startTime, $driverLog, $endTime, $currentTime);

                    $create = $timeData[0];

                    $last = $timeData[1];

                    $vehicleId = $driverLog->vehicle_id;

                    $vehicle = Vehicle::find($vehicleId);

                    if (!$vehicle) {
                        continue;
                    }

                    if ($currentStatus == 3 && $vehicleId) {

                        $device = Device::where('vehicle_id', $vehicleId)->first();

                        if (!$device || empty($device->serial_number)) {
                            continue;
                        }

                        if ($device) {

                            $serialNumber = $device->serial_number;

                            if ($serialNumber) {

                                $vehicleLog = VehicleLogHistory::where('identifier', $serialNumber)
                                    ->where('is_notify', 0)
                                    ->where('speed', '>=', 5)
                                    ->whereBetween('event_date_time', [$create, $last])
                                    ->orderBy('event_date_time', 'asc')
                                    ->get();

                                $bluetoothLog = BluetoothLogData::whereRaw(
                                    "CAST(JSON_UNQUOTE(JSON_EXTRACT(log_data, '$.speed')) AS DECIMAL(10,2)) >= ?",
                                    [5]
                                )
                                    ->whereRaw(
                                        "JSON_UNQUOTE(JSON_EXTRACT(log_data, '$.vin')) = ?",
                                        [$vehicle->vin]
                                    )
                                    ->whereRaw(
                                        "JSON_UNQUOTE(JSON_EXTRACT(log_data, '$.start_log_time')) <= ?",
                                        [$last]
                                    )
                                    ->whereRaw(
                                        "JSON_UNQUOTE(JSON_EXTRACT(log_data, '$.end_log_time')) >= ?",
                                        [$create]
                                    )
                                    ->get();

                                if (count($vehicleLog) == 0 && count($bluetoothLog) == 0) {

                                    $firstName = $data->first_name;

                                    $lastName = $data->last_name;

                                    $url = route('transport.dashboard');

                                    $message = $firstName . ' ' . $lastName . ' ' . 'your current log is in driving with' . ' ' . $vehicle->name . ' ' . 'and data is not being recieved from device, Please change your current duty status. Thank You!';

                                    // Send notification to the current user
                                    $user1 = User::find($id);
                                    if ($user1) {
                                        $notification1 = new NonDrivingNotification($message, $url, $id);
                                        $user1->notify($notification1);
                                    }

                                    // Send notification to the master user
                                    $user2 = User::find($master);
                                    if ($user2) {
                                        $notification2 = new NonDrivingNotification($message, $url, $master);
                                        $user2->notify($notification2);
                                    }

                                    $newDriverLog = DriverShiftLog::create([
                                        'driver_id' => $id,
                                        'start_log_time' => $driverLog->start_log_time,
                                        'end_log_time' => null,
                                        "current_shift_status" => 1,
                                        'vehicle_id' => $vehicleId,
                                        'system_entry' => 1,
                                        "accepted" => 1,
                                        'is_add_approved' => 1,
                                        'is_edit_approved' => 1,
                                        'is_assign_approved' => 1,
                                        'is_edit' => 1,
                                        'is_active' => 1,
                                    ]);

                                    $startData = shift_cycle_start_check($newDriverLog, $currentTime, $locationName, $rule_ids, 0);

                                    if (count($startData) > 0) {
                                        $shiftStart = $startData[0];
                                        $cycleStart = $startData[1];
                                    }

                                    if ($newDriverLog) {

                                        $newDriverLog->update([
                                            'shift_start' => $shiftStart,
                                            'cycle_start' => $cycleStart,
                                        ]);
                                    }

                                }
                            }
                        }
                    }
                }
            }
        }

        // Now, $drivers contains all drivers associated with the transport users
        $this->info('Non driving notification to make it to off duty has been done.');

        return Command::SUCCESS;
    }
}
