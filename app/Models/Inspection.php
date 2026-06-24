<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory;

    protected $table = 'inspections';

    protected $fillable = [
        'inspection_type',
        'vehicle_id',
        'inspection_start_time',
        'master_id',
        'master_company_id',
        'created_by',
        'updated_by'
    ];

    public function typeInspection()
    {
        return $this->hasOne(ListOption::class, 'option_id', 'inspection_type')->where('list_id', 'inspection_type');
    }

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class, 'id', 'vehicle_id');
    }

    public function inspectionLog()
    {
        return $this->hasMany(InspectionLog::class, 'inspection_id', 'id');
    }
}
