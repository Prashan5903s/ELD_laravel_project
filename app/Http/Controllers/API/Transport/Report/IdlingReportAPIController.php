<?php

namespace App\Http\Controllers\API\Transport\Report;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Models\VehicleAssign;
use App\Models\VehicleLogHistory;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class IdlingReportAPIController extends Controller
{
    public function index($start, $end, $driver, $vehicle)
    {

        $start = Carbon::parse($start)->startOfDay();

        $end = Carbon::parse($end)->endOfDay();

        $userId = Auth::user()->id;

        $data = [];

        // Handle driver as null
        if ($driver == 'null') {
            $driver = User::where('master_id', $userId)->where('user_type', 'U')->pluck('id')->toArray();
        } else {
            $driver = explode(',', $driver);
        }

        // Handle vehicle as null
        if ($vehicle == 'null') {
            $vehicle = Vehicle::where('created_by', $userId)->pluck('id')->toArray();
        } else {
            $vehicle = explode(',', $vehicle);
        }

        // Fetch vehicle assignments
        $vehicleAssign = VehicleAssign::whereIn('driver_id', $driver)
            ->whereIn('vechile_id', $vehicle)
            ->select('vechile_id')
            ->with('device:vehicle_id,serial_number', 'vehicle:id,name')
            ->get();

        if ($vehicleAssign->isNotEmpty()) {
            foreach ($vehicleAssign as $assign) {
                $serialNumber = $assign->device->serial_number ?? null;
                $vehicleName = $assign->vehicle->name ?? 'Unknown Vehicle';

                if ($serialNumber) {
                    $vehicleLog = VehicleLogHistory::where('identifier', $serialNumber)
                        ->whereBetween('event_date_time', [$start, $end])
                        ->where(function ($query) {
                            $query->where('message_reason', 'IDLING')
                                ->orWhere('message_reason', 'IDLING_END');
                        })
                        ->select('id', 'identifier', 'message_reason', 'event_date_time', 'location', 'duration', 'created_at')
                        ->orderBy('event_date_time', "ASC")
                        ->get();


                    $data[$vehicleName] = $vehicleLog;
                }
            }
        }

        return response()->json($data);
    }
}
