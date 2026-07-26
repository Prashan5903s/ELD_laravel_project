<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    protected $table = 'user_device';

    protected $fillable = [
        'user_id',
        'device_id',
        'serialNo',
        'created_by',
        'updated_by',
        'master_id',
        'master_company_id',
        'created_by',
        'updated_by',
    ];

    // Define the relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
