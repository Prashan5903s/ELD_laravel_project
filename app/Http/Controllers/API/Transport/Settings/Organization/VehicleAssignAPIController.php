<?php

namespace App\Http\Controllers\API\Transport\Settings\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VehicleAssign;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

class VehicleAssignAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $assignments = VehicleAssign::get();
        $driverIds = $assignments->pluck('driver_id')->unique();
        $vehicleIds = $assignments->pluck('vechile_id')->unique();

        $drivers = User::whereIn('id', $driverIds)->where('user_type', 'U')->get();
        $vehicles = Vehicle::whereIn('id', $vehicleIds)->get();

        // Organize data
        $data = [
            'assignments' => $assignments,
            'drivers' => $drivers,
            'vehicles' => $vehicles,
        ];

        return response()->json($data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['driver'] = User::where('user_type', 'U')->where('master_id', Auth::user()->id)->where('is_active', 1)->get();

        $data['vehicle'] = Vehicle::all();

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        VehicleAssign::create([
            'driver_id' => $request->driver_id,
            'vechile_id' => $request->vehicle_id,
            'is_active' => $request->is_active,
            'created_by' => Auth::user()->id,
        ]);

        return response()->json("Added successfully", 200);

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
        $data['vehicleAssign'] = VehicleAssign::find($id);

        return response()->json($data);
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
        $vehicleAssign = VehicleAssign::find($id);

        $vehicleAssign->update([
            'driver_id' => $request->driver_id,
            'vechile_id' => $request->vehicle_id,
            'is_active' => $request->is_active,
            'updated_by' => Auth::user()->id
        ]);

        return response()->json("Added successfully", 200);
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
    
    public function vehicle_driver_unique_assign(Request $request, $did = null, $vid = null, $id = null)
    {
        $query = VehicleAssign::where('vechile_id', $vid)->where('driver_id', $did);

        // If $id is provided, exclude the user with that ID
        if ($id) {
            $query->where('id', '!=', $id);
        }

        $userCount = $query->count();

        return response()->json($userCount);
    }
    
}
