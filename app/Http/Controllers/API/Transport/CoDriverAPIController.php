<?php

namespace App\Http\Controllers\API\Transport;

use Carbon\Carbon;
use App\Models\User;
use App\Models\CoDriver;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use App\Models\DriverShiftLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CoDriverAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function driver_list($id)
    {
        // Find the user by ID
        $user = User::find($id);

        // Check if the user exists
        if ($user) {
            // Get the master_id of the user
            $masterId = $user->master_id;

            // Get the list of drivers with the same master_id, excluding the current user
            $drivers = User::where('master_id', $masterId)
                ->where('id', '!=', $id)  // Exclude the current user by comparing ids
                ->select('id', 'first_name', 'last_name')
                ->get();

            // Return the list of drivers
            return response()->json($drivers, 200);
        }

        // If the user doesn't exist, return a 404 response
        return response()->json("User does not exist", 404);
    }
    public function index($date, $id,)
    {
        $coDriver = CoDriver::where('user_id', $id)->where('codriver_date', $date)->select('codriver_id')->first();

        return response()->json($coDriver);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $date, $id)
    {
        $startDay = Carbon::parse($date)->startOfDay();
        $endDay = Carbon::parse($date)->endOfDay();

        $authId = Auth::user()->id;

        $userInfo = UserInfo::where('user_id', $id)->first();

        if ($userInfo) {

            $user = User::find($id);
            $timezone = $userInfo->home_terminal_timezone;

            $currentTime = Carbon::parse()->setTimezone($timezone)->toDateTimeLocalString();
            $currentTime = Carbon::parse($currentTime);

            $create = $startDay;
            $last = $endDay;

            $driverLog = DriverShiftLog::where('driver_id', $id)
                ->where('is_add_approved', 1)
                ->whereBetween('start_log_time', [$create, $last])
                ->orderBy('start_log_time', "ASC")
                ->first();

            if (!$driverLog) {
                return response()->json("This date has no log", 404);
            }

            // Get the codrivers from the request
            $codrivers = $request->co_drivers; // Assuming co_drivers is an array of codriver ids

            $newCodrivers = $codrivers;

            $coDriver = CoDriver::where('user_id', $id)->where('codriver_date', $date)->first();

            if (!$coDriver) {
                CoDriver::create([
                    'user_id' => $id,
                    'codriver_id' => implode(',', $codrivers),
                    'codriver_date' => $date,
                    'created_by' => $authId,
                    'master_id' => $user->master_id,
                    'master_company_id' => $user->master_company_id,
                ]);
            } else {

                $existingCodrivers = explode(',', $coDriver->codriver_id);

                // Normalize both arrays to strings before comparison
                $existingCodrivers = array_map('strval', explode(',', $coDriver->codriver_id));
                $newCodrivers = array_map('strval', $newCodrivers);


                sort($existingCodrivers);
                sort($newCodrivers);

                $arraysAreEqual = $existingCodrivers === $newCodrivers;

                if ($arraysAreEqual) {

                    $coDriver->update([
                        'user_id' => $id,
                        'codriver_id' => implode(',', $codrivers),
                        'codriver_date' => $date,
                        'updated_by' => $authId,
                        'master_id' => $user->master_id,
                        'master_company_id' => $user->master_company_id,
                    ]);
                } else {

                    $coDriver->update([
                        'user_id' => $id,
                        'codriver_id' => implode(',', $codrivers),
                        'codriver_date' => $date,
                        'updated_by' => $authId,
                        'is_approved' => 0,
                        'master_id' => $user->master_id,
                        'master_company_id' => $user->master_company_id,
                    ]);
                }
            }

            return response()->json("Codriver saved successfully", 200);
        } else {
            return response()->json("User does not exist", 404);
        }
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
