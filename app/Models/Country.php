<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\State;

class Country extends Model
{
    use HasFactory;

    protected $table = 'country';
    protected $primaryKey = 'country_id'; // Specify the primary key

    public function states()
    {
        return $this->hasMany(State::class, 'country_id'); // Specify the foreign key
    }
    
     public function users()
    {
        return $this->hasMany(User::class, 'country_id', 'country_id');
    }
}
