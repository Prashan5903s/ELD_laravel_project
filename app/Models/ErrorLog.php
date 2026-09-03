<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    use HasFactory;

    protected $table = 'error_log';

    protected $fillable = [
        'device_platform',
        'log_data',
        'file_name',
        'file_path',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
