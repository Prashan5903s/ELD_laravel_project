<?php

namespace App\Http\Controllers\API\Transport\Assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Device;
use App\Models\Vehicle;
use App\Models\VehicleAssign;
use App\Models\VehicleLogHistory;

class EnviornmentAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $userId = Auth::user()->id;

        $driver = User::where('user_type', 'U')->where('master_id', $userId)->get();

        $vehicleLog = [];
        $processedIdentifiers = []; // Array to keep track of processed identifiers

        if ($driver && count($driver) > 0) {
            foreach ($driver as $value) {
                $vehicleAssgn = VehicleAssign::where('driver_id', $value->id)->first();

                if ($vehicleAssgn) {
                    $vehicle = Vehicle::find($vehicleAssgn->vechile_id);

                    if ($vehicle) {
                        $device = Device::where('vehicle_id', $vehicle->id)->first();

                        if ($device) {
                            $identifier = $device->serial_number;
                            $vehicleName = $vehicle->name;

                            // Check if the identifier has already been processed
                            if (!in_array($identifier, $processedIdentifiers)) {
                                $latestVehicleLog = VehicleLogHistory::where('identifier', $identifier)
                                    ->orderBy('event_date_time', 'desc')
                                    ->first();

                                if ($latestVehicleLog) {
                                    $vehicleLog[] = [$latestVehicleLog, $vehicleName];
                                    $processedIdentifiers[] = $identifier; // Add identifier to the processed list
                                }
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
