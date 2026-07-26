<?php

namespace App\Http\Controllers\API\Transport\Report;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Device;
use Carbon\Carbon;
use App\Models\VehicleLogHistory;
use App\Models\DriverShiftLog;

class SafetyReportAPIController extends Controller
{
    public function index($start, $end, $driver, $safety)
    {
        $user = Auth::user();
        $userId = $user->id;

        $startDay = Carbon::parse($start)->startOfDay();

        $endDay = Carbon::parse($end)->endOfDay();

        $data = safety_score_report_calculation($userId, $startDay, $endDay);

        $filteredData = collect($data);

        if ($safety != null && $safety != 'null' && !empty($safety)) {
            $safety = explode(',', $safety);
            $filteredData = $filteredData->whereIn('performance', $safety);
        }

        if ($driver != null && $driver != 'null' && !empty($driver)) {
            $driver = explode(',', $driver);
            $filteredData = $filteredData->whereIn('driver_id', $driver);
        }

        return response()->json($filteredData);
    }
}
