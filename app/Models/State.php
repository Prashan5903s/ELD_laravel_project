<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use App\Models\City;

class State extends Model
{
    use HasFactory;
    protected $table = 'state';
    protected $primaryKey = 'state_id'; // Declaring state_id as the primary key

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class, 'state_id'); // Specify the foreign key
    }
}
