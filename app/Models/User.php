<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'first_name',
        'last_name',
        'password',
        'user_type',
        'comp_name',
        'mobile_no',
        'country_id',
        'timezone',
        'language_id',
        'avatar_image',
        'address',
        'pin_code',
        'is_active',
        'master_id',
        'master_company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'master_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'master_id', 'id');
    }

    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    public function vehicleAssign()
    {
        return $this->hasMany(VehicleAssign::class, 'driver_id', 'id');
    }

    public function vehicles()
    {
        return $this->hasManyThrough(Vehicle::class, VehicleAssign::class, 'driver_id', 'id', 'id', 'vechile_id');
    }
    public function vehicleAssigns()
    {
        return $this->hasMany(VehicleAssign::class, 'driver_id');
    }
    public function driverShiftLogs()
    {
        return $this->hasMany(DriverShiftLog::class, 'driver_id');
    }

    public function packageAssigns()
    {
        return $this->hasMany(PackageAssign::class);
    }

    public function userInfo()
    {
        return $this->hasOne(UserInfo::class, 'user_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'country_id');
    }
    
    public function state(){
        return $this->hasOne(State::class, 'state_id', 'state_id');
    }

    public function wcUsers()
    {
        return $this->hasMany(User::class, 'master_id')->where('user_type', 'WC');
    }

    public function ecUsers()
    {
        return $this->hasMany(User::class, 'master_id')->where('user_type', 'EC');
    }

    public function trUsers()
    {
        return $this->hasMany(User::class, 'master_id')->where('user_type', 'TR');
    }
    
    public function inspection()
    {
        return $this->hasMany(Inspection::class, 'created_by', 'id');
    }

}
