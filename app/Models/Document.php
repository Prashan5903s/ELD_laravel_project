<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    public $table = 'driver_documents';

    protected $fillable = [
        'driver_id',
        'document_type',
        'image',
        'note',
        'status',
        'is_notify',
        'master_company_id',
        'master_id',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the driver that owns the document.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id', 'id');
    }

    /**
     * Get the ListOption associated with the document's document_type.
     */
    // In the Document model
    public function listOption()
    {
        // Define the relationship where list_id = document_type and option_id = document_type
        return $this->hasOne(ListOption::class, 'option_id', 'document_type')
            ->where('list_id', '=', \DB::raw("'document_type'"));  // Ensure list_id = document_type
    }

}
