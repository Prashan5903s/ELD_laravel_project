<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleLogHistory extends Model
{
    use HasFactory;

    protected $table = 'vehicle_log_history';
    protected $fillable = [
        'measurements',
        'device_id',
        'format',
        'identifier',
        'message_reason',
        'event_time',
        'event_date_time',
        'location',
        'unique_id',
        'location_age_min',
        'operating_states',
        'duration',
        'speed',
        'direction_alpha',
        'odometer',
        'num_satellites',
        'idle_duration',
        'obd_engine_rpm',
        'obd_coolant',
        'obd_speed',
        'obd_odometer',
        'is_updated',
        'is_off_updated',
        'is_notify',
        'obd_vin',
        'obd_fuel',
        'obd_throttle',
        'is_send',
        'obd_mpg',
        'obd_trip_mpg',
        'obd_instant_mpg',
        'EngineLight',
        'DiagnosticCodes',
    ];

    public function device() {
        return $this->belongsTo(Device::class, 'device_id');
    }
}
