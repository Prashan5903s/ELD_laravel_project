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
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

    public function approval_func(Request $request)
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

        try {
            $request->validate([
                'log_id' => 'required|string|max:255',
                'type' => [
                    'required',
                    'string',
                    Rule::in([
                        'coDriver',
                        'addLog',
                        'editLog',
                        'reassignLog',
                        'unidentifiedDriving',
                    ]),
                ],
                'is_approved' => [
                    'required',
                    'numeric',
                    Rule::in([0, 1]),
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $user = Auth::user();
        $userId = $user->id;

        $logId = $request->log_id;
        $type = $request->type;
        $accept = (int) $request->is_approved; // 1 = accepted, 0/2 = rejected

        $logId = explode(',', $logId);

        $rule_ids = RuleAssign::where('user_id', $userId)->pluck('rule_id');

        $userInfo = UserInfo::where('user_id', $userId)->first();
        $timezone = $userInfo ? ($userInfo->home_terminal_timezone ?? config('app.timezone')) : config('app.timezone');

        $currentTime = Carbon::now($timezone);

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
                        'accepted' => $accept === 1 ? 1 : 2,
                        'approved' => $accept
                    ]);
                }

                $messageText = $accept === 1 ? "accepted" : "rejected";

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Co-driver ' . $messageText . ' successfully',
                ];
            }

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
                DB::transaction(function () use ($addLog, $accept, $userId, $currentTime, $rule_ids) {
                    foreach ($addLog as $Log) {
                        $Log->update([
                            'accepted' => $accept,
                        ]);

                        if ($accept === 1) {
                            $windowStart = Carbon::parse($Log->start_log_time, $timezone ?? null);
                            $windowEnd = $Log->end_log_time ? Carbon::parse($Log->end_log_time, $timezone ?? null) : $currentTime->copy();
                            $vehicleId = $Log->vehicle_id;

                            if ($vehicleId) {
                                DriverShiftLog::where('driver_id', $userId)
                                    ->where('vehicle_id', $vehicleId)
                                    ->where('id', '!=', $Log->id)
                                    ->where('start_log_time', '<', $windowEnd)
                                    ->where(function ($q) use ($windowStart) {
                                        $q->whereNull('end_log_time')
                                            ->orWhere('end_log_time', '>', $windowStart);
                                    })
                                    ->delete();
                            }

                            $shiftData = shift_cycle_start_check($Log, $currentTime, $Log->location_name, $rule_ids, 0);

                            $shiftStart = $shiftData[0] ?? 0;
                            $cycleStart = $shiftData[1] ?? 0;

                            $Log->update([
                                "accepted" => 1,
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
                });

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Added driver shift log approved successfully',
                ];
            }

            return response()->json($data, $data['code']);

        } else if ($type == 'editLog') {

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

            DB::transaction(function () use ($editLog, $accept, $userId, $currentTime, $rule_ids) {
                foreach ($editLog as $log) {
                    if ($accept !== 1) {
                        $log->update(['accepted' => 2]);
                        continue;
                    }

                    $vehicleIdChange = $log->vehicle_id_change ?? $log->vehicle_id;
                    $statusChange = $log->current_shift_status_change ?? $log->current_shift_status;
                    $startTimeChange = $log->start_log_time_change ?? $log->start_log_time;
                    $endTimeChange = $log->end_log_time_change ?? $log->end_log_time;
                    $locationNameChange = $log->location_name_change ?? $log->location_name;
                    $odometerChange = $log->odometer_change ?? $log->odometer;
                    $odometerEndChange = $log->odometer_end_change ?? $log->odometer_end;
                    $notesChange = $log->notes_change ?? $log->notes;
                    $messageReasonChange = $log->message_reason_change ?? $log->message_reason;

                    $create = Carbon::parse($startTimeChange);
                    $last = is_null($endTimeChange) ? Carbon::parse($currentTime) : Carbon::parse($endTimeChange);

                    if ($vehicleIdChange) {
                        DriverShiftLog::where('driver_id', $userId)
                            ->where('vehicle_id', $vehicleIdChange)
                            ->where('id', '!=', $log->id)
                            ->where('start_log_time', '<', $last)
                            ->where(function ($q) use ($create) {
                                $q->whereNull('end_log_time')
                                    ->orWhere('end_log_time', '>', $create);
                            })
                            ->delete();
                    }

                    $startData = shift_cycle_start_check(
                        $log,
                        $currentTime,
                        $locationNameChange,
                        $rule_ids,
                        0
                    );

                    $shift_start = $startData[0] ?? 0;
                    $cycle_start = $startData[1] ?? 0;

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
                        'is_add_approved' => 1,
                        'is_edit_approved' => 1,
                        'is_assign_approved' => 1,
                        'is_edit' => 1,
                        'is_active' => 1,
                        'accepted' => 1,
                        'shift_start' => $shift_start,
                        'cycle_start' => $cycle_start,
                        'notes_change' => null,
                        'vehicle_id_change' => null,
                        'end_log_time_change' => null,
                        'location_name_change' => null,
                        'message_reason_change' => null,
                        'start_log_time_change' => null,
                        'current_shift_status_change' => null,
                    ]);
                }
            });

            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Edit driver shift log approved successfully',
            ];

            return response()->json($data, 200);

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
                DB::transaction(function () use ($reassignLog, $accept, $userId, $currentTime) {
                    foreach ($reassignLog as $log) {
                        if ($accept !== 1) {
                            $log->update(['accepted' => 2]);
                            continue;
                        }

                        $driverIdChange = $log->driver_id_change ? $log->driver_id_change : $log->driver_id;
                        $rule_ids_target = RuleAssign::where('user_id', $driverIdChange)->pluck('rule_id');

                        $startLogTimeChange = Carbon::parse($log->start_log_time);
                        $endLogTimeChange = $log->end_log_time
                            ? Carbon::parse($log->end_log_time)
                            : Carbon::parse($currentTime);

                        if ($log->vehicle_id) {
                            DriverShiftLog::where('driver_id', $driverIdChange)
                                ->where('vehicle_id', $log->vehicle_id)
                                ->where('id', '!=', $log->id)
                                ->where('start_log_time', '<', $endLogTimeChange)
                                ->where(function ($q) use ($startLogTimeChange) {
                                    $q->whereNull('end_log_time')
                                        ->orWhere('end_log_time', '>', $startLogTimeChange);
                                })
                                ->delete();
                        }

                        $startData = shift_cycle_start_check(
                            $log,
                            $currentTime,
                            $log->location_name,
                            $rule_ids_target,
                            0
                        );

                        $shift_start = $startData[0] ?? 0;
                        $cycle_start = $startData[1] ?? 0;

                        $log->update([
                            'driver_id' => $driverIdChange,
                            'driver_id_change' => null,
                            'accepted' => 1,
                            'is_assign_approved' => 1,
                            'is_add_approved' => 1,
                            'is_edit' => 1,
                            "is_edit_approved" => 1,
                            "is_active" => 1,
                            'end_log_time' => $endLogTimeChange,
                            'end_log_time_unix' => $endLogTimeChange->timestamp,
                            'shift_start' => $shift_start,
                            'cycle_start' => $cycle_start
                        ]);
                    }
                });

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Re-assign log processed successfully',
                ];
            }

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

            DB::transaction(function () use ($driverLog, $accept, $userId, $currentTime, $rule_ids) {
                foreach ($driverLog as $log) {
                    if ($accept === 1) {
                        $windowStart = Carbon::parse($log->start_log_time);
                        $windowEnd = $log->end_log_time ? Carbon::parse($log->end_log_time) : $currentTime->copy();

                        if ($log->vehicle_id) {
                            DriverShiftLog::where('driver_id', $userId)
                                ->where('vehicle_id', $log->vehicle_id)
                                ->where('id', '!=', $log->id)
                                ->where('start_log_time', '<', $windowEnd)
                                ->where(function ($q) use ($windowStart) {
                                    $q->whereNull('end_log_time')
                                        ->orWhere('end_log_time', '>', $windowStart);
                                })
                                ->delete();
                        }

                        $shiftData = shift_cycle_start_check($log, $currentTime, $log->location_name, $rule_ids, 0);

                        $shiftStart = $shiftData[0] ?? 0;
                        $cycleStart = $shiftData[1] ?? 0;

                        $log->update([
                            "driver_id" => $userId,
                            "shift_start" => $shiftStart,
                            "cycle_start" => $cycleStart,
                            'accepted' => 1,
                            "is_add_approved" => 1,
                            "is_edit_approved" => 1,
                            "is_assign_approved" => 1,
                            "is_edit" => 1,
                            "is_active" => 1,
                        ]);
                    } else {
                        $log->update([
                            'accepted' => 2,
                        ]);
                    }
                }
            });

            $data = [
                'status' => 'Success',
                'statusCode' => 200,
                'message' => "Data updated successfully",
            ];

            return response()->json($data, 200);

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
