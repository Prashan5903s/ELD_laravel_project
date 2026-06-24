<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;

    protected $table = 'user_group';

    protected $fillable = [
        'user_id',
        'group_id',
        'is_active'
    ];
    
    
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'group_id');
    }

    // Relationship: UserGroup belongs to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
