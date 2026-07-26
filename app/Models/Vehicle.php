<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicles';

    protected $fillable = [
        'name',
        'master_company_id',
        'master_id',
        'vin',
        'serial',
        'make',
        'model',
        'year',
        'fuel_type',
        'license_state',
        'fuel_tank_secondary',
        'fuel_tank_primary',
        'throttle_wifi',
        'harsh_acceleration_setting_type',
        'license_plate',
        'notes',
        'status',
        'created_by',
        'updated_by',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'vechicle_assign', 'vechile_id', 'driver_id');
    }

    public function devices()
    {
        return $this->hasMany(Device::class, 'vehicle_id');
    }

    public function driverShiftLogs()
    {
        return $this->hasMany(DriverShiftLog::class, 'vehicle_id');
    }

    public function latestVehicleLogHistory()
    {
        return $this->hasOneThrough(VehicleLogHistory::class, Device::class, 'vehicle_id', 'identifier', 'id', 'serial_number')
            ->whereRaw("BINARY devices.serial_number COLLATE utf8mb4_unicode_ci = vehicle_log_history.identifier COLLATE utf8mb4_unicode_ci")
            ->latest('event_date_time');
    }

    public function latestDriverShiftLog()
    {
        return $this->hasOne(DriverShiftLog::class, 'vehicle_id')->latest('created_at');
    }


}
