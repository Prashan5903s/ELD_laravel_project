<?php

namespace App\Http\Controllers\Mobile\API;

use App\Http\Controllers\Controller;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Device;
use App\Models\CoDriver;
use App\Models\UserInfo;
use App\Models\RuleAssign;
use Illuminate\Http\Request;
use App\Models\VehicleAssign;
use App\Models\DriverShiftLog;
use Illuminate\Support\Facades\Auth;

class ApprovalMobileAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $auth = Auth::check();

        $finalData = [];

        if (!$auth) {

            $data = [
                'status' => 'failure',
                'code' => 401,
                'message' => 'Not authenticated'
            ];
        } else {

            $user = Auth::user();

            $userId = $user->id;

            $vehicleId = VehicleAssign::where('driver_id', $userId)
                ->pluck('vechile_id')
                ->toArray();

            $unidentifiedDriving = DriverShiftLog::whereIn('vehicle_id', $vehicleId)
                ->where('is_unidentified', 1)
                ->where('is_add_approved', 0)
                ->where('accepted', 3)
                ->select('id', 'vehicle_id', 'start_log_time', 'current_shift_status')
                ->with('vehicle:id,name', 'option:list_id,option_id,title')
                ->get();

            $coDriver = CoDriver::where('user_id', $userId)
                ->where('is_approved', 0)
                ->where('accepted', 3)
                ->with('user:id,first_name,last_name', 'codriver:id,first_name,last_name')
                ->get();

            $addLog = DriverShiftLog::where('driver_id', $userId)
                ->where('is_add_approved', 0)
                ->where('is_unidentified', 0)
                ->where('accepted', 3)
                ->select('id', 'driver_id', 'vehicle_id', 'start_log_time', 'current_shift_status')
                ->with('user:id,first_name,last_name', 'vehicle:id,name', 'option:list_id,option_id,title')
                ->get();

            $editLog = DriverShiftLog::where('driver_id', $userId)
                ->where('is_edit_approved', 0)
                ->where('is_unidentified', 0)
                ->where('accepted', 3)
                ->select('id', 'driver_id', 'vehicle_id', 'start_log_time', 'end_log_time', 'current_shift_status', 'message_reason', 'notes', 'vehicle_id_change', 'current_shift_status_change', 'message_reason_change', 'start_log_time_change', 'end_log_time_change', 'notes_change', 'location_name_change')->where('is_unidentified', 0)
                ->with('user:id,first_name,last_name', 'vehicle:id,name', 'option:list_id,option_id,title')
                ->get();

            $reassignLog = DriverShiftLog::where('driver_id', $userId)
                ->where('is_assign_approved', 0)
                ->where('is_unidentified', 0)
                ->where('accepted', 3)
                ->select('id', 'driver_id', 'vehicle_id', 'start_log_time', 'end_log_time', 'current_shift_status', 'message_reason', 'notes', 'driver_id_change')
                ->with('user:id,first_name,last_name', 'vehicle:id,name', 'option:list_id,option_id,title', 'driverChange:id,first_name,last_name')
                ->get();

            if ($coDriver && count($coDriver) > 0) {

                foreach ($coDriver as $value) {

                    $codriver = $value->codriver_id;

                    $codriver = explode(',', $codriver);

                    $codriverUser = User::whereIn("id", $codriver)->select('id', 'first_name', 'last_name')->get();

                    $finalData['coDriver'][] = [
                        'id' => $value->id,
                        'co_drivers' => $codriverUser,
                        'driver' => $value->user,
                        'added_date' => $value->codriver_date,
                    ];
                }
            } else {
                $finalData['coDriver'] = [];
            }

            $finalData['addLog'] = $addLog;

            $finalData['editLog'] = $editLog;

            $finalData['reassignLog'] = $reassignLog;

            $finalData['unidentifiedDriving'] = $unidentifiedDriving;

            $data = [
                'status' => 'Success',
                'code' => 200,
                'message' => 'Data fetched successfully',
                'data' => $finalData
            ];
        }

        return response()->json($data, $data['code']);
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

    public function approval_func(Request $request, $type, $accept)
    {

        $auth = Auth::check();

        if (!$auth) {

            $data = [
                'status' => 'failure',
                'code' => 401,
                'message' => 'Not authenticated'
            ];

            return response()->json($data, $data['code']);
        }

        if (!in_array($accept, [1, 2])) {

            $data = [
                'status' => 'failure',
                'code' => 500,
                'message' => 'Value can only be 1 & 2'
            ];

            return response()->json($data, $data['code']);

        }

        $key = config('app.Map_key');

        $user = Auth::user();

        $userId = $user->id;

        $locationName = null;

        $logId = $request->log_id;

        $logId = explode(',', $logId);

        $rule_ids = RuleAssign::where('user_id', $userId)->pluck('rule_id');

        $userInfo = UserInfo::where('user_id', $userId)->first();

        $timezone = $userInfo->home_terminal_timezone;

        $currentTime = Carbon::parse()->setTimezone($timezone)->toDateTimeLocalString();

        $currentTime = Carbon::parse($currentTime);

        if ($type == "coDriver") {

            $coDriver = CoDriver::where('user_id', $userId)
                ->whereIn('id', $logId)
                ->where('accepted', 3)
                ->where('is_approved', 0)
                ->get();

            $data = [
                'status' => 'failure',
                'code' => 404,
                'message' => 'Co-driver does not exist',
            ];

            if ($coDriver && count($coDriver) > 0) {

                foreach ($coDriver as $log) {

                    $log->update([
                        'accepted' => $accept,
                    ]);

                }

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Co-driver rejected successfully',
                ];

            }

            update_latest_end_time_log($userId);

            return response()->json($data, $data['code']);

        } else if ($type == 'addLog') {

            $addLog = DriverShiftLog::where('driver_id', $userId)
                ->whereIn('id', $logId)
                ->where('accepted', 3)
                ->where('is_add_approved', 0)
                ->orderBy('start_log_time', "ASC")
                ->get();

            $data = [
                'status' => 'failure',
                'code' => 404,
                'message' => 'Log does not exist',
            ];

            if ($addLog && count($addLog) > 0) {

                foreach ($addLog as $Log) {

                    $Log->update([
                        'accepted' => $accept,
                    ]);

                    if ($accept == "1") {

                        log_add_unidentified_approval($Log, $userId, $currentTime, 1);

                        $shiftData = shift_cycle_start_check($Log, $currentTime, $locationName, $rule_ids, 0);

                        $shiftStart = $shiftData[0];
                        $cycleStart = $shiftData[1];

                        $Log->update([
                            "accepted" => $accept,
                            "is_add_approved" => 1,
                            "is_edit_approved" => 1,
                            "is_assign_approved" => 1,
                            "is_edit" => 1,
                            "is_active" => 1,
                            "shift_start" => $shiftStart,
                            "cycle_start" => $cycleStart,
                        ]);

                    }

                }

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Added driver shift log approved successfully',
                ];

            }

            update_latest_end_time_log($userId);

            return response()->json($data, $data['code']);

        } else if ($type == 'editLog') {

            $shift_start = 0;
            $cycle_start = 0;

            $editLog = DriverShiftLog::where('driver_id', $userId)
                ->whereIn('id', $logId)
                ->where('is_edit_approved', 0)
                ->where('accepted', 3)
                ->orderBy('start_log_time', 'ASC')
                ->get();

            if (!$editLog || count($editLog) == 0) {

                return response()->json([
                    'status' => 'failure',
                    'code' => 404,
                    'message' => 'Log does not exist',
                ], 404);

            }

            if ($editLog && count($editLog) > 0) {

                foreach ($editLog as $log) {

                    $vehicleIdChange = $log->vehicle_id_change;
                    $statusChange = $log->current_shift_status_change;
                    $startTimeChange = $log->start_log_time_change;
                    $endTimeChange = $log->end_log_time_change;
                    $locationNameChange = $log->location_name_change;
                    $odometerChange = $log->odometer_change;
                    $odometerEndChange = $log->odometer_end_change;
                    $notesChange = $log->notes_change;
                    $messageReasonChange = $log->message_reason_change;

                    $create = Carbon::parse($startTimeChange);

                    $last = is_null($endTimeChange) ? Carbon::parse($currentTime) : Carbon::parse($endTimeChange);

                    $exist = check_log_driver_exist(
                        $userId,
                        $create,
                        $last,
                        $log->id
                    );

                    $log->update([
                        "accepted" => $accept,
                        "is_add_approved" => 1,
                        "is_edit_approved" => 1,
                        "is_assign_approved" => 1,
                        "is_edit" => 1,
                        "is_active" => 1,
                    ]);

                    if (!$exist['status']) {

                        $changes = driver_log_time_data_edit(
                            $userId,
                            $create,
                            $last,
                            $currentTime,
                            $log
                        );

                        $locationName = null;

                        if ($changes) {

                            $editLogVehicleId = $log->vehicle_id;

                            if ($editLogVehicleId && !is_null($editLogVehicleId)) {

                                $device = Device::where('vehicle_id', $log->vehicle_id)->first();

                                $locationName = get_driver_activity_location(
                                    $device,
                                    $key,
                                    $startTimeChange
                                );
                            }

                            $log->update([
                                'vehicle_id' => $vehicleIdChange,
                                'current_shift_status' => $statusChange,
                                'start_log_time' => $startTimeChange,
                                'end_log_time' => $endTimeChange,
                                'start_log_time_unix' => Carbon::parse($startTimeChange)->copy()->timestamp,
                                'end_log_time_unix' => is_null($endTimeChange) ? null : Carbon::parse($endTimeChange)->copy()->timestamp,
                                'location_name' => $locationNameChange,
                                'notes' => $notesChange,
                                'odometer' => $odometerChange,
                                'odometer_end' => $odometerEndChange,
                                'message_reason' => $messageReasonChange,
                                'is_edit_approved' => 1,
                                'accepted' => 1,
                                'notes_change' => null,
                                'vehicle_id_change' => null,
                                'end_log_time_change' => null,
                                'location_name_change' => null,
                                'message_reason_change' => null,
                                'start_log_time_change' => null,
                                'current_shift_status_change' => null,
                            ]);

                            $startData = shift_cycle_start_check(
                                $log,
                                $currentTime,
                                $locationName,
                                $rule_ids,
                                0
                            );

                            if (count($startData) > 0) {
                                $shift_start = $startData[0];
                                $cycle_start = $startData[1];
                            }

                            $log->update([
                                'shift_start' => $shift_start,
                                'cycle_start' => $cycle_start,
                            ]);


                        }

                    }

                }

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Edit driver shift log approved successfully',
                ];

            }

            update_latest_end_time_log($userId);

            return response()->json($data, $data["code"]);

        } else if ($type == 'reassignLog') {

            $reassignLog = DriverShiftLog::where('driver_id', $userId)
                ->where('is_assign_approved', 0)
                ->where('is_add_approved', 1)
                ->where('accepted', 3)
                ->whereIn('id', $logId)
                ->orderBy('start_log_time', 'ASC')
                ->get();

            $data = [
                'status' => 'failure',
                'code' => 404,
                'message' => 'Log does not exist',
            ];

            if ($reassignLog && count($reassignLog) > 0) {

                foreach ($reassignLog as $log) {

                    $driverIdChange = $log->driver_id_change;

                    $driverIdChange = $driverIdChange ? $driverIdChange : $log->driver_id;

                    $rule_ids = RuleAssign::where('user_id', $driverIdChange)->pluck('rule_id');

                    $startLogTimeChange = $log->start_log_time;

                    $startLogTimeChange = Carbon::parse($startLogTimeChange);

                    $endLogTimeChange = $log->end_log_time
                        ? Carbon::parse($log->end_log_time)
                        : Carbon::parse($currentTime);

                    $create = $startLogTimeChange;

                    $last = $endLogTimeChange;

                    $exist = check_log_driver_exist(
                        $driverIdChange,
                        $create,
                        $last,
                        $log->id
                    );

                    $log->update([
                        'accepted' => $accept,
                        'driver_id_change' => null,
                        'end_log_time' => $endLogTimeChange,
                        "end_log_time_unix" => $endLogTimeChange->timestamp
                    ]);

                    if (!$exist['status']) {

                        $changes = driver_log_time_data_edit(
                            $driverIdChange,
                            $create,
                            $last,
                            $currentTime,
                            $log
                        );

                        if ($changes) {

                            $editLogVehicleId = $log->vehicle_id;

                            if ($editLogVehicleId && !is_null($editLogVehicleId)) {

                                $device = Device::where('vehicle_id', $log->vehicle_id)
                                    ->first();

                                $locationName = get_driver_activity_location(
                                    $device,
                                    $key,
                                    $create
                                );

                            }

                            $startData = shift_cycle_start_check(
                                $log,
                                $currentTime,
                                $locationName,
                                $rule_ids,
                                0
                            );

                            $shift_start = 0;
                            $cycle_start = 0;

                            if (count($startData) > 0) {
                                $shift_start = $startData[0];
                                $cycle_start = $startData[1];
                            }

                            $log->update([
                                'driver_id' => $driverIdChange,
                                'driver_id_change' => null,
                                'is_assign_approved' => 1,
                                'is_add_approved' => 1,
                                'is_edit' => 1,
                                "is_edit_approved" => 1,
                                "is_active" => 1,
                                'shift_start' => $shift_start,
                                'cycle_start' => $cycle_start
                            ]);

                            $changeLatestLog = DriverShiftLog::where('driver_id', $driverIdChange)
                                ->latest('start_log_time')
                                ->first();

                            $currentLatestLog = DriverShiftLog::where('driver_id', $userId)
                                ->latest('start_log_time')
                                ->first();

                            if ($changeLatestLog) {

                                $changeLatestLog->update([
                                    'end_log_time' => null,
                                ]);

                            }

                            if ($currentLatestLog) {

                                $currentLatestLog->update([
                                    'end_log_time' => null
                                ]);

                            }

                        }

                    }
                }

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Re-assign log rejected successfully',
                ];

            }

            update_latest_end_time_log($userId);

            return response()->json($data, $data['code']);

        } else if ($type == 'unidentifiedDriving') {

            $driverLog = DriverShiftLog::whereIn('id', $logId)
                ->where('is_unidentified', 1)
                ->where('accepted', 3)
                ->orderBy('start_log_time', 'ASC')
                ->get();

            if (!$driverLog || count($driverLog) == 0) {

                return response()->json([
                    'status' => 'Failure',
                    'statusCode' => 404,
                    'message' => "Data does not exist"
                ], 404);

            }

            $data = [
                'status' => 'failure',
                'code' => 401,
                'message' => 'Log already exist on same time',
            ];

            if ($driverLog && count($driverLog) > 0) {

                foreach ($driverLog as $log) {

                    $locationName = $log->location_name;

                    if ($accept == "1") {

                        log_add_unidentified_approval($log, $userId, $currentTime, 2);

                        $shiftData = shift_cycle_start_check($log, $currentTime, $locationName, $rule_ids, 0);

                        $shiftStart = $shiftData[0];
                        $cycleStart = $shiftData[1];

                        $log->update([
                            "driver_id" => $userId,
                            "shift_start" => $shiftStart,
                            "cycle_start" => $cycleStart,
                        ]);

                    }

                    $log->update([
                        'accepted' => $accept,
                        "is_add_approved" => 1,
                        "is_edit_approved" => 1,
                        "is_assign_approved" => 1,
                        "is_edit" => 1,
                        "is_active" => 1,
                    ]);

                }

                $data = [
                    'status' => 'Success',
                    'statusCode' => 200,
                    'message' => "Data updated successfully",
                ];

            }

            update_latest_end_time_log($userId);

            return response()->json($data, $data["statusCode"]);

        } else {
            $data = [
                'status' => 'failure',
                'code' => 404,
                'message' => 'Wrong type used',
            ];

            return response()->json($data, $data['code']);
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
