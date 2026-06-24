<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HOSUnsignedLog extends Model
{
    use HasFactory;

    protected $table = "hos_unsigned_log";

    protected $fillable = [
        'id',
        'user_id',
        'timeData',
        'signature',
        'is_certify',
        'master_id',
        'master_company_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
}
