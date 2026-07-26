<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAlert extends Model
{
    use HasFactory;

    protected $table = 'user_alerts';

    protected $fillable = [
        'alert_name',
        'type_id',
        'trigger_range',
        'trigger_week_day',
        'trigger_start_time',
        'trigger_end_time',
        'user_id',
        'vehicle_id',
        'recipient_id',
        'is_priority',
        'method',
        'frequency',
        'created_by',
        'master_id',
        'master_company_id',
        'status',
    ];

    public function ListOption()
    {
        return $this->hasOne(ListOption::class, 'option_id', 'type_id')->where('list_id', 'alert_type');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
