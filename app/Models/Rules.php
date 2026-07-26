<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rules extends Model
{
    use HasFactory;

    protected $table = 'rules';

    public function ruleAssgn(){
        return $this->hasMany(RuleAssign::class, 'id', 'rule_id');
    }
}
