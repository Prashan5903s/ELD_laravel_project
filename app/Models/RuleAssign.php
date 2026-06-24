<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Rules;

class RuleAssign extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_id',
        'user_id',
        'master_id',
        'master_company_id'
    ];

    protected $table = 'rule_assign';

    public function rule()
    {
        return $this->belongsTo(Rules::class, 'rule_id', 'id');
    }
}
