<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class CoDriver extends Model

{

    use HasFactory;



    protected $table = 'codriver_user';



    protected $fillable = [
        'user_id',
        'codriver_id',
        'codriver_date',
        'created_by',
        'updated_by',
        'is_approved',
        'is_extended',
        'accepted',
        'master_id',
        'master_company_id',
        'created_at	timestamp',
        'updated_at',
    ];



    public function user()

    {

        return $this->hasOne(User::class, 'id', 'user_id');

    }



    public function codriver()

    {

        return $this->belongsToMany(User::class, 'codriver_user', 'user_id', 'codriver_id');

    }

}

