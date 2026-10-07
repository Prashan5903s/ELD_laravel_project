<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestResponseLog extends Model
{
    use HasFactory;

    protected $table = 'request_response_log';

    public const UPDATED_AT = null;

    protected $fillable = [
        'request_url',
        'request_method',
        'request_headers',
        'response_status',
        'ip_address',
        'request_data',
        'response_data',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'created_by' => 'integer',
    ];
}
