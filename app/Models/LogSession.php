<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSession extends Model
{
    use HasFactory;

    protected $table = 'log_session';
    
    protected $fillable = [
      'log_status',
      'login_time',
      'logout_time',
      'ip',
      'user_token',
      'user_agent',
      'user_id',
    ];
    
}
