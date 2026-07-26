<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    public $table = 'locations';

    protected $fillable = [
        'name',
        'address',
        'type',
        'latitude',
        'longitude',
        'radius',
        'tags',
        'notes',
        'shapeData',
        'master_id',
        'master_company_id',
        'status',
        'created_by',
        'updated_by',
    ];
}
