<?php

namespace App\Http\Controllers\API\Transport\Mileage;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use App\Models\Vehicle;
use App\Models\ListOption;
use Illuminate\Http\Request;
use App\Models\VehicleLogHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class MileageAPIController extends Controller
{
    public function index($start, $end, $vehicle, $jurisdiction, $fuelType)
    {

        $startTime = Carbon::parse($start)->startOfDay();
        $endTime = Carbon::parse($end)->endOfDay();

        $userId = Auth::user()->id;

        $totalDistance = 0;
        $groupData = []; // Array to hold grouped data

        $start = Carbon::parse($start)->startOfDay();
        $end = Carbon::parse($end)->endOfDay();

        $data['fuelType'] = ListOption::where('list_id', 'fuel_type')->pluck('option_id')->toArray();
        $data['vehicle'] = Vehicle::where('created_by', $userId)->pluck('id')->toArray();

        $vehicleLogHistory = null;

        if ($vehicle == 'null') {

            if ($fuelType == 'null') {

                $vehicleLogHistory = Vehicle::whereIn('id', $data['vehicle'])
                    ->whereIn('fuel_type', $data['fuelType'])
                    ->select('id', 'name')
                    ->with([
                        'devices:vehicle_id,serial_number',
                    ])
                    ->get();
            } else {

                // Fix for handling $fuelType if it's an array
                if (is_array($fuelType)) {
                    $fuelType = implode(',', $fuelType);  // Convert array to string
                }
                $fuelType = explode(',', $fuelType); // Now it's safe to use explode

                $vehicleLogHistory = Vehicle::whereIn('id', $data['vehicle'])
                    ->whereIn('fuel_type', $fuelType)
                    ->select('id', 'name')
                    ->with([
                        'devices:vehicle_id,serial_number',
                    ])
                    ->get();
            }
        } else {

            $vehicle = explode(',', $vehicle);

            if ($fuelType == 'null') {

                $vehicleLogHistory = Vehicle::whereIn('id', $vehicle)
                    ->whereIn('fuel_type', $data['fuelType'])
                    ->select('id', 'name')
                    ->with([
                        'devices:vehicle_id,serial_number',
                    ])
                    ->get();
            } else {

                // Fix for handling $fuelType if it's an array
                if (is_array($fuelType)) {
                    $fuelType = implode(',', $fuelType);  // Convert array to string
                }

                $fuelType = explode(',', $fuelType); // Now it's safe to use explode

                $vehicleLogHistory = Vehicle::whereIn('id', $vehicle)
                    ->whereIn('fuel_type', $fuelType)
                    ->select('id', 'name')
                    ->with([
                        'devices:vehicle_id,serial_number',
                    ])
                    ->get();
            }
        }

        $datas = [];

        foreach ($vehicleLogHistory as $log) {

            $device = $log['devices'];

            if ($device && count($device) > 0) {

                $serialNumber = $device[0]['serial_number'];

                $logs = VehicleLogHistory::where('identifier', $serialNumber)
                    ->whereBetween("created_at", [$startTime, $endTime])
                    ->get();

                $groupData = []; // Array to hold grouped data
                $start = 0;

                if ($logs && count($logs) > 0) {

                    for ($i = 1; $i < count($logs); $i++) {

                        $current = $logs[$i];
                        $previous = $logs[$i - 1];

                        $currentLocation = json_decode($current->location, true)['GeoLocation'];
                        $currentLatitude = $currentLocation['Latitude'];
                        $currentLongitude = $currentLocation['Longitude'];
                        $currentAddress = getStateFromCoordinates($currentLatitude, $currentLongitude);

                        $nextLocation = json_decode($previous->location, true)['GeoLocation'];
                        $nextLatitude = $nextLocation['Latitude'];
                        $nextLongitude = $nextLocation['Longitude'];
                        $nextAddress = getStateFromCoordinates($nextLatitude, $nextLongitude);

                        if ($currentAddress !== $nextAddress) {

                            $firstData = $logs[$start];
                            $lastData = $logs[$i - 1];

                            $firstLocation = json_decode($firstData->location, true)['GeoLocation'];
                            $firstLatitude = $firstLocation['Latitude'];
                            $firstLongitude = $firstLocation['Longitude'];
                            $firstDate = $firstData->event_date_time;
                            $firstOdometer = $firstData->odometer;
                            $firstCoordinate = $firstLatitude . ', ' . $firstLongitude;
                            $firstAddress = getStateFromCoordinates($firstLatitude, $firstLongitude);

                            $lastLocation = json_decode($lastData->location, true)['GeoLocation'];
                            $lastLatitude = $lastLocation['Latitude'];
                            $lastLongitude = $lastLocation['Longitude'];
                            $lastDate = $lastData->event_date_time;
                            $lastOdometer = $lastData->odometer;
                            $lastCoordinate = $lastLatitude . ', ' . $lastLongitude;
                            $lastAddress = getStateFromCoordinates($lastLatitude, $lastLongitude);

                            if ($firstAddress !== 'Unknown') {

                                $groupData[] = [
                                    "startCoordinate" => $firstCoordinate,
                                    "endCoordinate" => $lastCoordinate,
                                    "stateName" => $firstAddress,
                                    "StartOdometer" => $firstOdometer,
                                    "endOdometer" => $lastOdometer,
                                    'distance' => ($lastOdometer - $firstOdometer),
                                    'date' => $firstDate
                                ];
                            }

                            $start = $i;
                        }
                    }

                    $firstSETData = $logs[$start];
                    $lastSETData = $logs[count($logs) - 1];

                    $firstSETLocation = json_decode($firstSETData->location, true)['GeoLocation'];
                    $firstSETLatitude = $firstSETLocation['Latitude'];
                    $firstSETLongitude = $firstSETLocation['Longitude'];
                    $firstSETDate = $firstSETData->event_date_time;
                    $firstSETOdometer = $firstSETData->odometer;
                    $firstSETCoordinate = $firstSETLatitude . ', ' . $firstSETLongitude;
                    $firstSETAddress = getStateFromCoordinates($firstSETLatitude, $firstSETLongitude);

                    $lastSETLocation = json_decode($lastSETData->location, true)['GeoLocation'];
                    $lastSETLatitude = $lastSETLocation['Latitude'];
                    $lastSETLongitude = $lastSETLocation['Longitude'];
                    $lastSETDate = $lastSETData->event_date_time;
                    $lastSETOdometer = $lastSETData->odometer;
                    $lastSETCoordinate = $lastSETLatitude . ', ' . $lastSETLongitude;
                    $lastSETAddress = getStateFromCoordinates($lastSETLatitude, $lastSETLongitude);

                    if ($firstSETAddress !== 'Unknown') {

                        $groupData[] = [
                            "startCoordinate" => $firstSETCoordinate,
                            "endCoordinate" => $lastSETCoordinate,
                            "stateName" => $firstSETAddress,
                            "StartOdometer" => $firstSETOdometer,
                            "endOdometer" => $lastSETOdometer,
                            'distance' => ($lastSETOdometer - $firstSETOdometer),
                            'date' => $firstSETDate
                        ];
                    }

                    if ($jurisdiction != 'null') {

                        $jurisdiction = explode(',', $jurisdiction);

                        $filteredData = collect($groupData)
                            ->filter(function ($item) use ($jurisdiction) {
                                return in_array($item['stateName'], $jurisdiction);
                            })
                            ->values() // Reset the keys of the resulting array
                            ->toArray();

                        $groupData = $filteredData;
                    }

                    foreach ($groupData as $data) {
                        $totalDistance += $data['endOdometer'] - $data['StartOdometer'];
                    }
                }


                $datas[] = [
                    'name' => $log['name'],
                    'mileage_data' => $groupData,
                ];
            }
        }

        // Now, $data will contain all the grouped log entries with start and end details and province name.
        return response()->json([$datas, $totalDistance]);
    }

    public function mileage_filter()
    {
        $userId = Auth::user()->id;

        // List of U.S. states
        $data['states'] = [
            "Alabama",
            "Alaska",
            "Arizona",
            "Arkansas",
            "California",
            "Colorado",
            "Connecticut",
            "Delaware",
            "Florida",
            "Georgia",
            "Hawaii",
            "Idaho",
            "Illinois",
            "Indiana",
            "Iowa",
            "Kansas",
            "Kentucky",
            "Louisiana",
            "Maine",
            "Maryland",
            "Massachusetts",
            "Michigan",
            "Minnesota",
            "Mississippi",
            "Missouri",
            "Montana",
            "Nebraska",
            "Nevada",
            "New Hampshire",
            "New Jersey",
            "New Mexico",
            "New York",
            "North Carolina",
            "North Dakota",
            "Ohio",
            "Oklahoma",
            "Oregon",
            "Pennsylvania",
            "Rhode Island",
            "South Carolina",
            "South Dakota",
            "Tennessee",
            "Texas",
            "Utah",
            "Vermont",
            "Virginia",
            "Washington",
            "West Virginia",
            "Wisconsin",
            "Wyoming"
        ];

        $data['vehicle'] = Vehicle::where('created_by', $userId)->select('id', 'name')->get();
        $data['fuelType'] = ListOption::where('list_id', 'fuel_type')->select('option_id', 'title')->get();

        return response()->json($data);
    }
}
