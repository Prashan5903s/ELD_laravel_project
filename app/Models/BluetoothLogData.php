<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BluetoothLogData extends Model
{
    use HasFactory;

    protected $table = "bluetooth_log_data";

    protected $fillable = [
        "driver_id",
        "vehicle_id",
        "log_data",
        "request_json",
        "ip",
        "created_by",
        "updated_by"
    ];
}
