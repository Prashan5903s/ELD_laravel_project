<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverShiftLog extends Model
{
    use HasFactory;

    protected $table = 'driver_shift_log';

    protected $fillable = [
        'driver_id',
        'vehicle_id',
        'device_id',
        'current_shift_status',
        'message_reason',
        'is_active',
        'system_entry',
        'start_log_time',
        'end_log_time',
        'start_log_time_unix',
        'end_log_time_unix',
        'location_name',
        'location_end',
        'notes',
        'codriver_id',
        'cycle_start',
        'shift_start',
        'created_at',
        'created_by',
        'is_add_approved',
        'is_edit_approved',
        'is_assign_approved',
        'is_unidentified',
        'is_edit',
        'odometer',
        'odometer_end',
        'updated_by',
        'driver_id_change',
        'vehicle_id_change',
        'current_shift_status_change',
        'message_reason_change',
        'start_log_time_change',
        'end_log_time_change',
        'notes_change',
        'location_name_change',
        'odometer_change',
        'odometer_end_change',
        "log_type",
        "accepted",
        'engineHour'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function shiftStatusOption()
    {
        return $this->belongsTo(ListOption::class, 'current_shift_status', 'option_id')
            ->where('list_id', 'driving_status');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function vehicleAssign()
    {
        return $this->hasMany(VehicleAssign::class, 'vechile_id', 'vehicle_id');
    }

    public function vehicleChange()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id_change');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function driverChange()
    {
        return $this->belongsTo(User::class, 'driver_id_change');
    }

    public function option()
    {
        return $this->belongsTo(ListOption::class, 'current_shift_status', 'option_id')
            ->where('list_id', 'driving_status');
    }

    public function logOption()
    {
        return $this->belongsTo(ListOption::class, 'current_shift_status_change', 'option_id')
            ->where('list_id', 'driving_status');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'vehicle_id', 'vehicle_id');
    }
}
