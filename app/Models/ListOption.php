<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListOption extends Model
{
    use HasFactory;

    protected $table = "list_options";

    public static function getOptions($list_id, $seq = [], $lang = '1') {
        $query = self::select('option_id', 'title');

        // Add conditions for list_id and language_id
        $query->where('list_id', $list_id)->where('language_id', $lang);

        // If $seq is provided and it's an array, order the results based on the array values
        if (!empty($seq) && is_array($seq)) {
            $query->orderByRaw('FIELD(seq, ' . implode(',', $seq) . ')');
        }

        return $query->get();
    }

}
