<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDeviceNotify extends Model
{
    use HasFactory;

    protected $table = 'user_device';

    protected $fillable = [
        'user_id',
        'device_id',
        'fcm_token',
        'platform',
        'is_active',
        'created_by',
        'updated_by',
    ];


}
