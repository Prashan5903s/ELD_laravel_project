<?php

namespace App\Http\Controllers\API\Transport\Settings\device;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Device;
use App\Models\Vehicle;
use App\Models\Hardware;
use App\Models\DeviceType;
use App\Models\UserDevice;

class DeviceAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data['device'] = Device::with(['deviceType', 'vehicle', 'hardware'])
            ->where('created_by', Auth::user()->id)
            ->get()
            ->map(function ($device) {
                // Fetch the latest vehicle_log_history matching the device's serial_number
                $device->latest_log = DB::table('vehicle_log_history')
                    ->where('identifier', $device->serial_number)
                    ->orderBy('event_date_time', 'desc') // Assuming created_at column exists
                    ->first();

                return $device;
            });

        $data['userDevice'] = UserDevice::where('user_id', Auth::user()->id)->get();

        return response()->json($data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['device_type'] = DeviceType::where('is_active', 1)->get();
        $data['vehicle'] = Vehicle::where('status', 1)->get();
        $data['hardware'] = Hardware::all();
        $data['userDevice'] = UserDevice::where('user_id', Auth::user()->id)->get();
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Device::create([
            'hardware_id' => $request->hardware,
            'serial_number' => $request->serial_number,
            'device_type_id' => $request->device_type,
            'master_id' => Auth::user()->id,
            'master_company_id' => Auth::user()->master_company_id,
            'vehicle_id' => $request->vehicle_id,
            'gateway_serial' => $request->gateway_serial,
            'gateway' => $request->gateway,
            'created_by' => Auth::user()->id,
            'is_active' => $request->is_active
        ]);

        return response()->json("Driver added successfully");

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
        $data['device'] = Device::find($id);
        return response()->json($data, 200);
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
        $device = Device::find($id);

        $device->update([
            'hardware_id' => $request->hardware,
            'serial_number' => $request->serial_number,
            'device_type_id' => $request->device_type,
            'master_id' => Auth::user()->id,
            'master_company_id' => Auth::user()->master_company_id,
            'vehicle_id' => $request->vehicle_id,
            'gateway_serial' => $request->gateway_serial,
            'gateway' => $request->gateway,
            'updated_by' => Auth::user()->id,
            'is_active' => $request->is_active
        ]);

        return response()->json('Device updated successfully');
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

    public function check_unique_serial(Request $request, $serial = null, $id = null)
    {

        $query = Device::where('serial_number', $serial);

        // If $id is provided, exclude the user with that ID
        if ($id) {
            $query->where('id', '!=', $id);
        }

        $userCount = $query->count();

        return response()->json($userCount);

    }

    public function unique_vehicle_assign(Request $request, $vid = null, $id = null)
    {
        if ($vid != null) {


            $query = Device::where('vehicle_id', $vid);

            // If $id is provided, exclude the user with that ID
            if ($id) {
                $query->where('id', '!=', $id);
            }

            $userCount = $query->count();

            return response()->json($userCount);
        }else{
            return response()->json(0);
        }
    }

}
