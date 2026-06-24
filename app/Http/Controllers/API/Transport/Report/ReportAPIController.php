<?php

namespace App\Http\Controllers\API\Transport\Report;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReportAPIController extends Controller
{

    public function index($start = null, $end = null, $userId = null)
    {

        $user = null;

        $start = Carbon::parse($start)->startOfDay();

        $end = Carbon::parse($end)->endOfDay();

        if ($userId == 'null') {

            $logUser = Auth::user()->id;

            $user = User::where('master_id', $logUser)->select('id', 'first_name', 'last_name')->get();

        } else {

            $userId = explode(',', $userId);

            if (!is_array($userId)) {
                $userId = [$userId]; // Convert single value to array
            }

            // Query the users
            $user = User::whereIn('id', $userId)
                ->select('id', 'first_name', 'last_name')
                ->get();

        }

        $data = [];

        foreach ($user as $val) {

            $valId = $val->id;

            $eldRule = check_eld_rules($valId, $start, $end);

            $data[] = [$val, $eldRule];

        }

        return response()->json($data);

    }

}
