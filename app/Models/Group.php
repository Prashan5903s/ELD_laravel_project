<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';
    
    protected $primaryKey = 'group_id';

    protected $fillable = [

        'group_title',
        'group_name',
        'group_description',
        'master_id',
        'master_company_id',
        'created_by',
        'is_active'

    ];

     public function userGroups()
    {
        return $this->hasMany(UserGroup::class, 'group_id', 'group_id');
    }

    // Relationship: Groups have many Users through UserGroup
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_group', 'group_id', 'user_id');
    }

}
