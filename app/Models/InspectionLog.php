<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionLog extends Model
{
    use HasFactory;

    protected $table = 'inspection_logs';

    protected $fillable = [
        'inspection_id',
        'parts_id',
        'is_ok',
        'defect_type',
        'notes',
        'image_url',
        'is_open',
        'master_id',
        'master_company_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function parts()
    {
        return $this->hasOne(ListOption::class, 'option_id', 'parts_id')->where('list_id', 'parts_type');
    }

    public function defect()
    {
        return $this->hasOne(ListOption::class, 'option_id', 'defect_type')->where('list_id', 'defect_type');
    }
}
