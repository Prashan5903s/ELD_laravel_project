<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Device;
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

        $transportUsers = User::where('user_type', 'TR')->get();

        // Initialize an empty collection to store drivers
        $drivers = collect();

        // Iterate through each transport user
        foreach ($transportUsers as $transport) {
            // Retrieve drivers for the current transport user
            $driversForTransport = User::where('user_type', 'U')->where('master_id', $transport->id)->get();
            // Merge the drivers into the main collection
            $drivers = $drivers->merge($driversForTransport);
        }

        function conTimezone($timezone, $time)
        {
            // Parse the given time string and set it to the specified timezone
            $convertedTime = Carbon::parse($time)->setTimezone($timezone);

            return $convertedTime->toDateTimeString();
        }

        if ($drivers && count($drivers) > 0) {

            foreach ($drivers as $data) {

                $odometerChange = 0;

                $id = $data->id;

                $master = $data->master_id;

                $userInfo = UserInfo::where('user_id', $id)->first();

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

                    if ($currentStatus == 3 && $vehicleId) {

                        $device = Device::where('vehicle_id', $vehicleId)->first();

                        if ($device) {

                            $serialNumber = $device->serial_number;

                            if ($serialNumber) {

                                $vehicleLog = VehicleLogHistory::where('identifier', $serialNumber)
                                    ->where('is_notify', 0)
                                    ->where('speed', '<', 5)
                                    ->whereBetween('event_date_time', [$create, $last])
                                    ->orderBy('event_date_time', 'asc')
                                    ->get();

                                if ($vehicleLog && count($vehicleLog) > 0) {

                                    $firstLog = $vehicleLog->first();

                                    if ($firstLog) {

                                        $prevLog = VehicleLogHistory::where('identifier', $serialNumber)
                                            ->where('id', '<', $firstLog->id) // Corrected condition
                                            ->orderBy('id', 'desc') // Order by event date time descending
                                            ->first();

                                        if ($prevLog) {

                                            $obd1 = $firstLog->obd_odometer;

                                            $obd2 = $prevLog->obd_odometer;

                                            $diff = $obd1 - $obd2;

                                            $odometerChange += $diff;

                                            $prevLog->update([
                                                'is_notify' => 1,
                                            ]);
                                        }
                                    }

                                    $vehicleLogCount = count($vehicleLog);

                                    foreach ($vehicleLog as $key => $log) {

                                        $currentData = $log->obd_odometer;  // Current data

                                        if ($key + 1 < $vehicleLogCount) {

                                            $nextData = $vehicleLog[$key + 1]->obd_odometer;  // Next data

                                            $milesData = $nextData - $currentData;

                                            $odometerChange += $milesData;
                                        }
                                    }

                                    $firstName = $data->first_name;

                                    $lastName = $data->last_name;

                                    $url = route('transport.dashboard');

                                    $message = $firstName . ' ' . $lastName . ' ' . 'your current log is in driving with' . ' ' . $vehicle->name . ' ' . 'and data is not being recieved from device, Please change your current duty status. Thank You!';

                                    if ($odometerChange > 0) {
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
                                    }


                                    VehicleLogHistory::where('identifier', $serialNumber)
                                        ->where('is_notify', 0)
                                        ->where('speed', '<', 5)
                                        // ->whereBetween('event_date_time', [$create, $last])
                                        ->orderBy('event_date_time', 'asc')
                                        ->update(['is_notify' => 1]);

                                    VehicleLogHistory::where('identifier', $serialNumber)
                                        ->where('is_notify', 0)
                                        ->where('speed', '<', 5)
                                        // ->where('event_date_time', '<', $create)
                                        ->orderBy('event_date_time', 'asc')
                                        ->update(['is_notify' => 1]);
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
