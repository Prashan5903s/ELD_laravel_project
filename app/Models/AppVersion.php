<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    use HasFactory;

    protected $table = 'app_version';

    protected $primaryKey = 'app_version_id';

    protected $fillable = [
        'version_id',
        'type',
    ];
}
