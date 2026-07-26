<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DeviceType;
use App\Models\Vehicle;
use App\Models\Hardware;

class Device extends Model
{
    use HasFactory;

    protected $table = 'devices';


    protected $fillable = [
        'device_type_id',
        'hardware_id',
        'serial_number',
        'master_id',
        'master_company_id',
        'name',
        'gateway_serial',
        'gateway',
        'created_by',
        'updated_by',
        'is_active',
        'vehicle_id'
    ];

    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class, 'device_type_id');
    }

    public function hardware()
    {
        return $this->belongsTo(Hardware::class, 'hardware_id');
    }

    public function vehicle() {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function vehicleLogHistory() {
        return $this->hasMany(VehicleLogHistory::class, 'identifier', 'serial_number');
    }
    public function driverShiftLogs()
    {
        return $this->hasMany(DriverShiftLog::class, 'vehicle_id', 'vehicle_id');
    }
}
