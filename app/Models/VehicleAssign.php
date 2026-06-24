<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleAssign extends Model
{
    use HasFactory;

    protected $table = 'vechile_assign';

    protected $fillable = [
        'driver_id',
        'vechile_id',
        'is_active',
        'created_by',
        'updated_by'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vechile_id');
    }

    public function device()
    {
        return $this->hasOne(Device::class, 'vehicle_id', 'vechile_id');
    }

    public function driver()
    {
        return $this->hasOne(User::class, 'id', 'driver_id');
    }
    
    public function userInfo()
    {
        return $this->hasOne(UserInfo::class, 'user_id', 'driver_id');
    }

}
