<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'module_id',
        'permission_id',
    ];

    public $table = 'package_module';
}
