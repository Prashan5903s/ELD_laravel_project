<?php

namespace App\Http\Controllers\API\Transport;

use App\Models\User;
use App\Models\CoDriver;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DriverShiftLog;

class ReAssignCoDriverAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($driverId, $date)
    {
        $data = [];
        $coDriver = CoDriver::where('user_id', $driverId)
            ->where('codriver_date', $date)
            ->where('is_approved', 1)
            ->first();
        if ($coDriver) {
            $firstExtended = $coDriver->is_extended;
            $coDriverId = $coDriver->codriver_id;
            $coDriverId = explode(',', $coDriverId);
            if ($firstExtended == 1) {
                foreach ($coDriverId as $id) {
                    $user = User::find($id);
                    $firstName = $user->first_name;
                    $lastName = $user->last_name;
                    $data[] = [
                        'id' => $id,
                        'name' => $firstName . " " . $lastName,
                    ];
                }
            }
        } else {
            $coDrivers = CoDriver::where('user_id', $driverId)
                ->where('is_approved', 1)
                ->where('codriver_date', '<=', $date)
                ->orderBy('codriver_date', 'desc')
                ->first();

            if ($coDrivers) {
                $extended = $coDrivers->is_extended;
                $coDriverIds = $coDrivers->codriver_id;
                $coDriverIds = explode(',', $coDriverIds);

                if ($extended == 1) {
                    foreach ($coDriverIds as $id) {
                        $user = User::find($id);
                        $firstName = $user->first_name;
                        $lastName = $user->last_name;
                        $data[] = [
                            'id' => $id,
                            'name' => $firstName . " " . $lastName,
                        ];
                    }
                }
            }
        }
        return response()->json($data, 200);
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
    public function store(Request $request, $driverId, $date)
    {
        $logId = $request->id;
        $driverId = $request->driver_id;

        $driverShiftLog = DriverShiftLog::find($logId);

        $driverIdChange = $driverShiftLog->driver_id_change;

        if ($driverId != $driverIdChange) {
            $driverShiftLog->update([
                'driver_id_change' => $driverId,
                'is_assign_approved' => 0,
                'log_type' => 2,
                'accepted' => 3
            ]);
        }

        return response()->json("CoDriver has been successfully assigned to the log", 200);
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
