<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'master_id',
        'master_company_id',
        'status'
    ];

    public function permissions() : BelongsToMany {
        return $this->belongsToMany(Permission::class, 'roles_permissions');
    }

    public function users(){
        return $this->belongsToMany(User::class, 'role_user');
    }

    public function hasPermission( $permission )
    {
        return $this->permissions->contains('name', $permission);
    }
}
