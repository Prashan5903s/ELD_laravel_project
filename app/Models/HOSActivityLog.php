<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HOSActivityLog extends Model
{
    use HasFactory;

    protected $table = "hos_activity_log";

    protected $fillable = [
        'id',
        'timeData',
        'user_id',
        'first_name',
        'last_name',
        'distance',
        'odometer',
        'cariier_name',
        'main_office_address',
        'home_terminal_address',
        'fromLoc',
        'toLoc',
        'notes',
        'is_change_certified',
        'master_id',
        'master_company_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
}
