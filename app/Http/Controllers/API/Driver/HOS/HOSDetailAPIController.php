<?php

namespace App\Http\Controllers\API\Driver\HOS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DriverShiftLog;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\ListOption;
use App\Models\Vehicle;
use App\Models\Device;
use App\Models\VehicleLogHistory;
use App\Models\RuleAssign;
use App\Models\Rules;
use App\Models\User;
use App\Models\UserInfo;
use PHPUnit\Framework\ActualValueIsNotAnObjectException;

class HOSDetailAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function driver_date_hos_data(Request $request, $startTime, $endTime)
    {

        $id = Auth::user()->id;

        $start = Carbon::parse($startTime)->startOfDay();

        $end = Carbon::parse($endTime)->endOfDay(); // Ensure the end time includes the whole day

        $data = hos_date_data($id, $start, $end);

        // Return the array of dates
        return response()->json($data);
    }

    public function hos_detail_page()
    {

        $id = Auth::user()->id;

        $userInfo = UserInfo::where('user_id', $id)->first();

        $timezone = $userInfo->home_terminal_timezone;

        $currentTime = Carbon::parse()->setTimezone($timezone)->toDateTimeLocalString();

        $currentTime = Carbon::parse($currentTime);

        $data = driver_log_time($id, $currentTime);

        return response()->json($data);
    }

    public function graph_data(Request $request, $date)
    {
        $id = Auth::user()->id;

        $user = User::where('user_type', 'U')->where('id', $id)->first();

        $userInfo = UserInfo::where('user_id', $id)->first();

        $timeZone = $userInfo->home_terminal_timezone;

        //Current time of today
        $currTime = Carbon::parse()->setTimezone($timeZone)->toDateTimeLocalString();

        $currTimes = Carbon::parse($currTime);

        $currentTime = $currTimes;

        $date = Carbon::parse($date);

        $startTime = $date->copy()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $endTime = $date->copy()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $data = graph_hos_chart($id, $startTime, $endTime, $currentTime);

        return response()->json($data);
    }

    public function driver_hos_detail_page() {}

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
