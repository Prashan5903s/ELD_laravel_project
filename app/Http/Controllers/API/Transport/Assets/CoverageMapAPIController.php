<?php

namespace App\Http\Controllers\API\Transport\Assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\VehicleAssign;
use App\Models\Vehicle;
use App\Models\Device;
use App\Models\VehicleLogHistory;

class CoverageMapAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $dateStart = null, $dateEnd = null)
    {

        $today = Carbon::now()->format('Y-m-d');

        $userId = Auth::user()->id;

        $vehicle = Vehicle::where('created_by', $userId)->get();

        $vehicleLog = [];

        $processedIdentifiers = []; // Array to keep track of processed identifiers

        if ($vehicle && count($vehicle) > 0) {

            if ($vehicle && count($vehicle) > 0) {

                foreach ($vehicle as $data) {

                    $device = Device::where('vehicle_id', $data->id)->first();

                    if ($device) {

                        $identifier = $device->serial_number;

                        // Check if the identifier has already been processed
                        if (!in_array($identifier, $processedIdentifiers)) {

                            if ($dateStart && $dateStart) {

                                $latestVehicleLog = VehicleLogHistory::where('identifier', $identifier)
                                    ->whereBetween('event_date_time', [$dateStart, $dateEnd])
                                    ->orderBy('event_date_time', 'desc')
                                    ->first();

                            }

                            if ($dateStart == null && $dateEnd == null) {

                                $latestVehicleLog = VehicleLogHistory::where('identifier', $identifier)
                                    ->orderBy('event_date_time', 'desc')
                                    ->first();

                            }

                            if ($latestVehicleLog) {
                                $vehicleLog[] = [$latestVehicleLog, $data->name];
                                $processedIdentifiers[] = $identifier; // Add identifier to the processed list
                            }

                        }

                    }

                }
            }

        }

        return response()->json($vehicleLog);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
