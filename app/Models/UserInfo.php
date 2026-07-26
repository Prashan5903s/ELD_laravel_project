<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;
    protected $table = 'user_info'; // specify the table name if it's different from the model name
    protected $primaryKey = 'id'; // specify the primary key if it's different from 'id'

    protected $fillable = [
        'user_id',
        'fleet_user_id',
        'username',
        'main_office_address',
        'career_name',
        'home_terminal_timezone',
        'home_terminal_address',
        'driver_id',
        'licenseNumber',
        'odometer',
        'cargo_type_id'
    ];

    public static function generateUniqueUsername($baseUsername)
    {
        $username = $baseUsername;
        $count = 1;

        // Check if username already exists for the user
        while (self::where('username', $username)->exists()) {
            $username = $baseUsername . $count++;
        }

        return $username;
    }

    public function homeTerminal()
    {
        return $this->belongsTo(Location::class, 'home_terminal_address');
    }
    
    public function licenseState()
    {
        return $this->hasOne(State::class, 'state_id', 'driver_license_state');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
