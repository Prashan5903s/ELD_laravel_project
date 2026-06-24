<?php

namespace App\Http\Controllers\API\Transport\Report;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Inspection;
use Illuminate\Http\Request;
use App\Models\VehicleLogHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InspectionReportAPIController extends Controller
{
    public function index($start, $end, $driver, $vehicle)
    {
        $user = Auth::user();
        $userId = $user->id;

        $data = [];

        $start = Carbon::parse($start)->startOfDay();
        $end = Carbon::parse($end)->endOfDay();

        // Ensure $driver is handled properly
        if ($driver === 'null') {
            $driver = User::where('user_type', 'U')
                ->where('master_id', $userId)
                ->select('id', 'first_name', 'last_name')
                ->get();
        } else {
            $userData = explode(',', $driver);
            $driver = User::where('user_type', 'U')
                ->whereIn('id', $userData)
                ->select('id', 'first_name', 'last_name')
                ->get();
        }

        if ($vehicle === 'null') {
            $vehicle = Vehicle::where('created_by', $userId)->pluck('id')->toArray();
        } else {
            $vehicle = explode(',', $vehicle);
        }

        // Ensure $driver is iterable
        if ($driver instanceof \Illuminate\Support\Collection) {
            $driver = $driver->toArray();
        }

        if (is_array($driver) && count($driver) > 0) {

            foreach ($driver as $value) {

                $inspectionData = [];

                $driverName = $value['first_name'] . ' ' . $value['last_name'];

                $inspection = Inspection::where('created_by', $value['id'])
                    ->whereIn("vehicle_id", $vehicle)
                    ->whereBetween('inspection_date_time', [$start, $end])
                    ->select(
                        'id',
                        'inspection_type',
                        'vehicle_id',
                        'inspection_date_time',
                        'inspection_start_time',
                        'inspection_end_time',
                        'created_by'
                    )
                    ->with([
                        'vehicle:id,name',
                        'vehicle.devices:id,vehicle_id,serial_number',
                        'typeInspection:option_id,title',
                        'inspectionLog:inspection_id,parts_id,is_ok,defect_type,notes,image_url,is_open',
                        'inspectionLog.parts:option_id,title',
                        'inspectionLog.defect:option_id,title'
                    ])
                    ->get();

                $defectLog = Inspection::where('created_by', $value['id'])
                    ->whereIn('vehicle_id', $vehicle)
                    ->whereBetween('inspection_date_time', [$start, $end])
                    ->with('inspectionLog') // Load the inspectionLog relationship
                    ->get();

                $defectCount = 0;

                // Iterate through each inspection
                foreach ($defectLog as $values) {
                    // Check if any inspectionLog for this inspection has is_ok = 2
                    if ($values->inspectionLog->contains('is_ok', 2) && $values->inspectionLog->contains('is_open', 0)) {
                        $defectCount++;
                    }
                }

                $inspectionCount = Count($inspection);

                if ($inspection && count($inspection) > 0) {
                    foreach ($inspection as $inspectionVal) {
                        $startTime = Carbon::parse($inspectionVal->inspection_start_time);
                        $endTime = Carbon::parse($inspectionVal->inspection_end_time);
                        $duration = $endTime->diffInSeconds($startTime);

                        $vehicleName = $inspectionVal->vehicle->name ?? 'N/A';
                        $inspectionLog = $inspectionVal->inspectionLog;

                        // Ensure $inspectionLog is an array
                        if (is_object($inspectionLog)) {
                            $inspectionLog = json_decode(json_encode($inspectionLog), true);
                        } elseif (!is_array($inspectionLog)) {
                            $inspectionLog = []; // Default to an empty array if not already an array
                        }

                        $isOpenCount = count(array_filter($inspectionLog, function ($row) {
                            return isset($row['is_open'], $row['is_ok']) && $row['is_open'] == 0 && $row['is_ok'] == 2;
                        }));

                        $isResolveCount = count(array_filter($inspectionLog, function ($row) {
                            return isset($row['is_open'], $row['is_ok']) && $row['is_open'] == 1 && $row['is_ok'] == 2;
                        }));

                        $serialNumber =  $inspectionVal->vehicle->devices[0]->serial_number ?? null;

                        // Check if serialNumber exists to avoid errors
                        $logHistory = $serialNumber
                            ? VehicleLogHistory::where('identifier', $serialNumber)
                            ->whereBetween('event_date_time', [$startTime, $endTime])
                            ->latest('event_date_time')
                            ->first() ??
                            VehicleLogHistory::where('identifier', $serialNumber)
                            ->where('event_date_time', '<', $startTime)
                            ->latest('event_date_time')
                            ->first()
                            : null;

                        $inspectionData[] = [
                            'inspection_time' => $inspectionVal->inspection_date_time,
                            'duration' => $duration,
                            'vehicle' => $vehicleName,
                            'open' => $isOpenCount,
                            'status' => 'Satisfactory',
                            'resolve' => $isResolveCount,
                            'odometer' => $logHistory->odometer ? $logHistory->odometer : '....',
                        ];
                    }
                }

                $data[$driverName] = [$inspectionData, $inspectionCount, $defectCount];
            }
        }

        return response()->json($data, 200);
    }
}
