<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\DriverShiftLog;

class UniqueDriverShiftLog implements Rule
{
    public $driverId;
    public $vehicleId;
    public $driverStatus;

    public function __construct($driverId, $vehicleId, $driverStatus)
    {
        $this->driverId = $driverId;
        $this->vehicleId = $vehicleId;
        $this->driverStatus = $driverStatus;
    }

    public function passes($attribute, $value)
    {
        $latestLog = DriverShiftLog::where('driver_id', $this->driverId)
            ->where('vehicle_id', $this->vehicleId)
            ->latest('created_at')
            ->first();
        if ($latestLog) {
            if ($latestLog->current_shift_status == $this->driverStatus) {
                return false;
            }else{
                return true;
            }
        } else {
            return true;
        }
    }

    public function message()
    {
        return 'You have the same driver status entry in the latest entry.';
    }
}
