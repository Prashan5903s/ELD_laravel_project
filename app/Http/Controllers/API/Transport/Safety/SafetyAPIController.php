<?php

namespace App\Http\Controllers\API\Transport\Safety;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\Vehicle;
use App\Models\DriverShiftLog;
use App\Models\VehicleAssign;
use App\Models\VehicleLogHistory;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SafetyAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index($start = null, $end = null)
    {

        $id = Auth::user()->id;

        $startDay = null;

        $endDay = null;

        // Default values for start and end days
        $startDay = $start ? ($start !== 'null' && $start !== null ? Carbon::parse($start)->startOfDay() : null) : null;
        $endDay = $end ? ($end !== 'null' && $end !== null ? Carbon::parse($end)->endOfDay() : null) : null;


        // Calculate safety score
        $data = safety_score_calculation($id, $startDay, $endDay);

        $eventPerMiles = event_per_miles($id, $startDay, $endDay);

        // Count the number of drivers
        $driver = User::where('user_type', 'U')
            ->where('master_id', $id)
            ->count();

        $driverData = safety_driver_score_calculation($id, $startDay,  $endDay);

        $logCount = collect($driverData)
            ->filter(fn($item) => $item[0] !== "Unidentified driver") // Exclude "Unidentified driver"
            ->count(); // Count the remaining logs

        return response()->json(['data' => $data, 'driver_count' => $logCount, 'event_per_miles' => $eventPerMiles, 'start_day' => $startDay, 'end_day' => $endDay]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($start = null, $end = null)
    {

        $id = Auth::user()->id;

        $startDay = null;

        $endDay = null;

        // Default values for start and end days
        $startDay = $start ? ($start !== 'null' && $start !== null ? Carbon::parse($start)->startOfDay() : null) : null;
        $endDay = $end ? ($end !== 'null' && $end !== null ? Carbon::parse($end)->endOfDay() : null) : null;

        $safety_score_factor = calculating_safety_score_factor($id, $startDay, $endDay);

        return response()->json($safety_score_factor);
    }

    public function safety_score_trend()
    {
        $id = Auth::user()->id;

        $now = Carbon::now();
        $latestSunday = $now->previous(Carbon::SUNDAY);
        $elevenWeeksBefore = $latestSunday->copy()->subWeeks(11); // Use copy() to avoid modifying $latestSunday

        // Create an array to hold the dates
        $dates = [];
        $dataSet = [];

        // Start from $elevenWeeksBefore and keep adding 7 days until $latestSunday
        $currentDate = $elevenWeeksBefore->copy();

        // Loop to generate dates
        while ($currentDate->lte($latestSunday)) {
            $dates[] = $currentDate->toDateString();
            $currentDate->addDays(7); // Add 7 days for the next iteration
        }

        // Optionally, if you want to include the end date if it's not already included
        if (!in_array($latestSunday->toDateString(), $dates)) {
            $dates[] = $latestSunday->toDateString();
        }

        if (!empty($dates)) {
            foreach ($dates as $date) {
                $value = Carbon::parse($date);

                $startDay = $value->startOfDay();
                $endDay = $value->endOfDay();

                $safetyScore = safety_score_calculation($id, $startDay, $endDay);
                $eventPerMiles = event_per_miles($id, $startDay, $endDay);

                $dataSet[] = [
                    'date' => $value->toDateString(), // Use a string key
                    'safety_score' => $safetyScore,
                    'event_per_miles' => $eventPerMiles,
                ];
            }
        }

        return response()->json($dataSet);
    }

    public function safety_score_per_driver($start = null, $end = null)
    {

        $id = Auth::user()->id;

        $startDay = null;

        $endDay = null;

        // Default values for start and end days
        $startDay = $start ? ($start !== 'null' && $start !== null ? Carbon::parse($start) : null) : null;
        $endDay = $end ? ($end !== 'null' && $end !== null ? Carbon::parse($end) : null) : null;

        $data = safety_driver_score_calculation($id, $startDay, $endDay);

        return $data;
    }

    public function event_data_set()
    {
        $id = Auth::user()->id;

        $data['driver'] = User::where('user_type', 'U')->where('master_id', $id)->get();

        $data['vehicle'] = Vehicle::where('created_by', $id)->get();

        return response()->json($data);
    }

    public function event_data_filter($start = null, $end = null, $driverId = [], $vehicleId = [], $behaviour = [])
    {

        $id = Auth::user()->id;

        $timezone = User::find($id)->timezone;

        $currentTime = Carbon::parse()->setTimezone($timezone)->toDateTimeLocalString();

        $currentTime = Carbon::parse($currentTime);

        $startDay = null;

        $endDay = null;

        // Default values for start and end days
        $startDay = $start ? ($start !== 'null' && $start !== null ? Carbon::parse($start)->startOfDay() : null) : null;
        $endDay = $end ? ($end !== 'null' && $end !== null ? Carbon::parse($end)->endOfDay() : null) : null;

        $id = Auth::user()->id;

        function parseIds($driverId)
        {
            if (is_null($driverId) || $driverId == 'null' || $driverId == '') {
                return []; // Return an empty array for null or empty string
            }

            if (!is_array($driverId)) {
                return array_map('intval', array_map('trim', explode(',', $driverId))); // Split, trim, and convert to integers
            } else {

                return $driverId;
            }
        }

        function parseBehaviourIds($driverId)
        {
            if (is_null($driverId) || $driverId == 'null' || $driverId == '') {
                return []; // Return an empty array for null or empty string
            }

            if (!is_array($driverId)) {
                return array_map('trim', explode(',', $driverId)); // Split and trim, but don't convert to integers
            }
        }

        function parseVehicleIds($driverId, $id)
        {
            if (is_null($driverId) || $driverId == 'null' || $driverId == '') {
                $vehicleId = Vehicle::where('created_by', $id)->pluck('id')->toArray();
                return $vehicleId;
            }

            if (!is_array($driverId)) {
                return array_map('trim', explode(',', $driverId)); // Split and trim, but don't convert to integers
            }
        }

        // Parse start and end dates
        $startDay = $start && $start !== 'null' ? Carbon::parse($start)->startOfDay() : null;
        $endDay = $end && $end !== 'null' ? Carbon::parse($end)->endOfDay() : null;

        $behaviour = parseBehaviourIds($behaviour);

        // Default behaviours if none provided
        $behArry = (is_array($behaviour) && count($behaviour) > 0) ? $behaviour : ['HARDSTOP', 'HARDACCEL', 'SPEEDING', 'HARDBRAKE', 'HARDTURN'];

        $driverId = parseIds($driverId);

        $vehicleId = parseVehicleIds($vehicleId, $id);

        $dataTotal = [];

        foreach ($vehicleId as $veh) {

            $create = $startDay;

            $last = $endDay;

            $device = Device::where('vehicle_id', $veh)->first();

            if ($device) {

                $identifier = $device->serial_number;

                $vehicleName = Vehicle::find($veh)->name;

                // Fetch Driver Shift Logs
                $logs = DriverShiftLog::where('vehicle_id', $veh)
                    ->where('is_add_approved', 1)
                    ->where(function ($query) use ($create, $last, $currentTime) {
                        $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                            $subQuery
                                ->where(function ($overlapQuery) use ($create, $last, $currentTime) {
                                    $overlapQuery->where('start_log_time', '<=', $last)
                                        ->whereRaw('IFNULL(end_log_time, ?) >= ?', [$currentTime, $create]);
                                })
                                ->whereRaw('IFNULL(end_log_time, ?) != ?', [$currentTime, $create])
                                ->whereRaw('? != start_log_time', [$last]);
                        });
                    })
                    ->orderBy('start_log_time', 'asc')
                    ->get();

                // Fetch Vehicle Log History
                $vehicleLogs = VehicleLogHistory::where('identifier', $identifier)
                    ->whereIn('message_reason', $behArry)
                    ->whereBetween('event_date_time', [$create, $last])
                    ->get();

                foreach ($vehicleLogs as $logsa) {

                    // Find the matching driver shift log
                    $matchedLog = $logs->first(function ($log) use ($logsa, $currentTime) {
                        $endLogTime = $log->end_log_time ?? $currentTime; // Default to $currentTime if NULL
                        return $logsa->event_date_time >= $log->start_log_time && $logsa->event_date_time <= $endLogTime;
                    });

                    $driver_Id = 0;

                    // Determine the driver name
                    if ($matchedLog) {

                        $driver = $matchedLog->driver
                            ? $matchedLog->driver->first_name . ' ' . $matchedLog->driver->last_name
                            : 'Unidentified Driver';

                        $driver_Id = $matchedLog->driver
                            ? $matchedLog->driver->id
                            : 0;
                    } else {

                        $driver_Id = 0;

                        $driver = 'Unidentified Driver';
                    }

                    $dataTotal[] = [
                        'ids' => $logsa->id,
                        'driver' => $driver,
                        'driver_id' => $driver_Id,
                        'vehicle' => $vehicleName,
                        'logs' => $logsa
                    ];
                }
            }
        }

        if (count($driverId)  > 0) {

            $filteredDataTotal = collect($dataTotal)->whereIn('driver_id', $driverId)->values();

            return response()->json($filteredDataTotal);
        } else {

            return response()->json($dataTotal);
        }
    }

    public function event_detail_data($id)
    {

        $vehicleLog = VehicleLogHistory::find($id);

        $vehicleName = null;

        $driver = null;

        $timeDifference = null;

        if ($vehicleLog) {

            $eventDate = $vehicleLog->event_date_time;

            $identifier = $vehicleLog->identifier;

            if ($identifier) {

                $device = Device::where('serial_number', $identifier)->first();

                $vehicleId = $device->vehicle_id;

                if ($vehicleId) {

                    $vehicle = Vehicle::find($vehicleId);

                    if ($vehicle) {

                        $vehicleAssign = VehicleAssign::where('vechile_id', $vehicleId)->first();

                        if ($vehicleAssign) {

                            $driverId = $vehicleAssign->driver_id;

                            if ($driverId) {

                                $driver = User::find($driverId);

                                $userInfo = UserInfo::where('user_id', $driverId)->first();

                                $homeTimezone = $userInfo->home_terminal_timezone;

                                $currentTime = Carbon::parse()->setTimezone($homeTimezone)->toDateTimeLocalString();

                                $start = Carbon::parse($eventDate);

                                $end = Carbon::parse($currentTime);

                                $timeDifference = $start->diffForHumans($end, [
                                    'short' => true,  // Use short units like "1d" for "1 day"
                                    'parts' => 1      // Limit to one unit, e.g., "1d" or "3h"
                                ]);
                            }
                        }

                        $vehicleName = $vehicle->name;
                    }
                }

                $data = [$vehicleName, $vehicleLog, $driver, $timeDifference];

                return response()->json($data, 200);
            }
        } else {

            return response()->json('Data does not exist', 404);
        }
    }

    public function safety_detail_data($id)
    {

        $driver = null;

        $vehicle = null;

        $belowVLog = null;

        $abvVLog = null;

        $vehicleLog = VehicleLogHistory::find($id);

        if ($vehicleLog) {

            $identifier = $vehicleLog->identifier;

            if ($identifier) {

                $belowVLog = VehicleLogHistory::where('identifier', $identifier)
                    ->where('id', '<', $id)
                    ->orderBy('id', 'desc')  // Order by descending to get the closest lower ID first
                    ->first();

                $abvVLog = VehicleLogHistory::where('identifier', $identifier)
                    ->where('id', '>', $id)
                    ->orderBy('id', 'asc')  // Order by ascending to get the closest higher ID first
                    ->first();


                $device = Device::where('serial_number', $identifier)->first();

                if ($device) {

                    $vehicleId = $device->vehicle_id;

                    if ($vehicleId) {

                        $vehicle = Vehicle::find($vehicleId);

                        $vehicleAssign = VehicleAssign::where('vechile_id', $vehicleId)->first();

                        if ($vehicleAssign) {

                            $driverId = $vehicleAssign->driver_id;

                            if ($driverId) {

                                $user = User::find($driverId);

                                if ($user) {

                                    $driver = $user;
                                }
                            }
                        }
                    }

                    $location = array_filter([$belowVLog, $vehicleLog, $abvVLog], function ($value) {
                        return !is_null($value) && $value !== '';
                    });


                    return response()->json([$driver, $vehicle, $location, $vehicleLog]);
                }
            }
        } else {

            return response()->json('Data not found', 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
