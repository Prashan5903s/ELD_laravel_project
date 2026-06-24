<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceAdmin extends Model
{
    use HasFactory;

    protected $table = 'device_admin';

    protected $fillable = [
        'serialNo',
        'master_id',
        'master_company_id',
        'created_by',
        'updated_by',
    ];
}
