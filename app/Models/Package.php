<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends Model
{
    use HasFactory;

    public $table = 'packages';

    protected $fillable = [
        'name',
        'package_code',
        'description',
        'duration_id',
        'currency_id',
        'package_type_id',
        'price',
        'status',
    ];

    public function modules() : BelongsToMany {
        return $this->belongsToMany(Module::class, 'package_module')->distinct();
    }

    public function permissions() : BelongsToMany {
        return $this->belongsToMany(Permission::class, 'package_module');
    }

    public function hasModule( $module )
    {
        return $this->modules->contains('id', $module);
    }

    public function hasPermission( $permission )
    {
        return $this->permissions->contains('id', $permission);
    }

    public function packageType()
    {
        return $this->belongsTo(PackageType::class, 'package_type_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    // Define the relationship to the ListOption model for duration
    public function duration()
    {
        return $this->belongsTo(ListOption::class, 'duration_id');
    }

    public function packageAssigns()
    {
        return $this->hasMany(PackageAssign::class);
    }

}
