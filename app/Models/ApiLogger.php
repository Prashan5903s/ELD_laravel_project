<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\ApiLoggerChanged;
use Illuminate\Http\Request;
use App\Events\ApiLoggerCreated;

class ApiLogger extends Model
{
    use HasFactory;

    protected $table = 'api_logger';

    protected $primaryKey = 'request_id';

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            event(new ApiLoggerChanged('created', $model));
        });

        static::updated(function ($model) {
            event(new ApiLoggerChanged('updated', $model));
        });

        static::deleted(function ($model) {
            event(new ApiLoggerChanged('deleted', $model));
        });
    }
    public function store(Request $request)
    {
        $data = $request->all();

        // Save data to the api_logger table
        $apiLogger = ApiLogger::create($data);

        // Fire the event
        event(new ApiLoggerCreated($data));

        return response()->json(['message' => 'Data saved successfully'], 200);
    }
}
