<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Rules;
use App\Models\Device;
use App\Models\UserInfo;
use App\Models\RuleAssign;
use App\Models\VehicleAssign;
use App\Models\DriverShiftLog;
use Illuminate\Console\Command;
use App\Models\VehicleLogHistory;
use Illuminate\Support\Facades\Http;

class UpdateToOffDutyLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:off-duty-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is to update driving log to off duty!';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $locationName = null;

        $device = Device::all();

        $key = config('app.Map_key');  // Fetch the Google Maps API key

        if ($device && count($device) > 0) {

            foreach ($device as $dev) {

                $userId = $dev->created_by;

                $user =  User::find($userId);

                $timeZone = $user->timezone;

                $currentTime = Carbon::parse()->setTimezone($timeZone)->toDateTimeLocalString();

                $currentTime = Carbon::parse($currentTime);

                $vehicle_id = $dev->vehicle_id;

                if ($vehicle_id) {

                    $start = Carbon::parse($currentTime)->startOfDay(); // Ensure $start is a Carbon instance
                    $end = Carbon::parse($currentTime)->endOfDay();     // Ensure $e is a Carbon instance

                    $vehicleLog = VehicleLogHistory::where('is_off_updated', 0)
                        ->whereBetween('event_date_time', [$start, $end])
                        ->where('identifier', $dev->serial_number)
                        ->latest('event_date_time')
                        ->first();

                    if ($vehicleLog) {

                        $speed = $vehicleLog->speed;

                        $updated = $vehicleLog->is_off_updated;

                        $device = Device::where('serial_number', $vehicleLog->identifier)->first();

                        $locationName = get_driver_activity_location($device, $key, $currentTime);

                        $odometer = get_driver_activity_odometer($device, $currentTime);

                        $engineHour = get_driver_activity_rpm($device, $currentTime);

                        if (isset($speed) && $speed < 5 && $updated == 0) {

                            if ($vehicle_id) {

                                $driverLogs = DriverShiftLog::where('vehicle_id', $vehicle_id)
                                    ->where('is_add_approved', 1)
                                    ->whereIn('start_log_time', function ($query) use ($vehicle_id) {
                                        $query->selectRaw('MAX(start_log_time)')
                                            ->from('driver_shift_log')
                                            ->where('vehicle_id', $vehicle_id)
                                            ->where('is_add_approved', 1)
                                            ->groupBy('driver_id');
                                    })
                                    ->get();

                                if ($driverLogs && count($driverLogs) > 0) {

                                    foreach ($driverLogs as $logData) {

                                        $driverIds = $logData->driver_id;

                                        $currentLog = $logData->current_shift_status;

                                        if ($driverIds && $currentLog && $currentLog == 3) {

                                            $userInfo = UserInfo::where('user_id', $driverIds)->first();

                                            $cycle_start = 0;
                                            $shift_start = 0;

                                            $timeZone = $userInfo->home_terminal_timezone;

                                            // Get the current date-time in the specified time zone
                                            $currentDateTime = Carbon::parse()->setTimezone($timeZone)->toDateTimeLocalString();

                                            $currentDateTime = Carbon::parse($currentDateTime);

                                            if ($logData) {

                                                $log = DriverShiftLog::where('driver_id', $driverIds)
                                                    ->where('is_add_approved', 1)
                                                    ->whereNull('end_log_time')
                                                    ->latest('start_log_time')
                                                    ->first(); // Fetch the first (most recent) entry

                                                if ($log) { // Check if the log exists
                                                    $log->update([
                                                        'end_log_time' => Carbon::parse($currentDateTime), // Update with current date and time
                                                        'location_end' => $locationName,
                                                        'odometer_end' => $odometer,
                                                    ]);
                                                }

                                                DriverShiftLog::create([
                                                    'driver_id' => $driverIds,
                                                    'vehicle_id' => $vehicle_id,
                                                    'current_shift_status' => 1,
                                                    'system_entry' => 1,
                                                    'location_name' => $locationName,
                                                    'odometer' => $odometer,
                                                    'engineHour' => $engineHour,
                                                    'start_log_time' => Carbon::parse($currentDateTime),
                                                    'shift_start' => $shift_start,
                                                    'cycle_start' => $cycle_start,
                                                    'is_add_approved' => 1,
                                                    'is_edit_approved' => 1,
                                                    'created_by' => $driverIds,
                                                    'created_at' => Carbon::parse($currentDateTime),
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $records = VehicleLogHistory::where('is_off_updated', 0)->get();

            if (!$records->isEmpty()) {

                $ids = $records->pluck('id')->toArray(); // Assuming 'id' is the primary key

                VehicleLogHistory::whereIn('id', $ids)->update(['is_off_updated' => 1]);
            }
        }

        $this->info('Driver log is updated to off duty.');

        return Command::SUCCESS;
    }
}
