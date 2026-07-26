<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLogs extends Model
{
    use HasFactory;

    protected $table = 'template_logs';

    protected $fillable = [
        'sender_id',
        'template_id',
        'message_text',
        'reciever_email',
        'send_time',
        'start_time',
        'end_time',
        'sender_token',
        'type',
        'is_send',
        'is_used',
    ];

    // Relationship with the User model
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'sender_id');
    }

    // Relationship with the UserInfo model
    public function userInfo()
    {
        return $this->hasOne(UserInfo::class, 'user_id', 'sender_id');
    }

    // Relationship with the RuleAssign model
    public function ruleAssign()
    {
        return $this->hasOne(RuleAssign::class, 'user_id', 'sender_id')
            ->where('rule_id', 3)
            ->orWhere('rule_id', 4);
    }

    // Relationship with the DriverShiftLog model
    public function vehicleAssign()
    {
        return $this->hasMany(VehicleAssign::class, 'driver_id', 'sender_id');
    }

}
