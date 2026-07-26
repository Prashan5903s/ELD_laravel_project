<?php

namespace App\Http\Controllers\driver;

use App\Http\Controllers\Controller;
use App\Models\DriverShiftLog;
use App\Models\VehicleAssign;
use App\Models\ListOption;
use Illuminate\Http\Request;
use App\Models\Rule;
use App\Models\RuleAssign;
use App\Models\UserInfo;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

class ActivityDriverController extends Controller
{
    public function index()
    {

        $userId = Auth::user()->id;

        $driverShift = DriverShiftLog::with('user', 'vehicle', 'option')->where('driver_id', $userId)->get();

        return view('driver.driver-activity.index', compact('driverShift'));

    }

    public function create()
    {

        $userId = Auth::user()->id;

        $listOption = ListOption::getOptions("driving_status", [], "1");

        $vehicleAssigns = VehicleAssign::where('driver_id', $userId)
            ->with('vehicle')
            ->get();

        return view('driver.driver-activity.create', compact('listOption', 'vehicleAssigns'));

    }

    public function store(Request $request)
    {

        $request->validate([
            'vehicle_id' => 'required',
            'driver_status' => 'required'
        ]);

        $cycle_start = 0;
        $shift_start = 0;
        $tim_st = 0;

        $latestLog = DriverShiftLog::where('driver_id', $request->driver_id)
            ->where('vehicle_id', $request->vehicle_id)
            ->latest('created_at')
            ->first();

        $rule_ids = RuleAssign::where('user_id', $request->driver_id)
            ->pluck('rule_id'); // Get an array of rule_ids from RuleAssign

        $userInfo = UserInfo::where('user_id', Auth::user()->id)->first();

        $timeZone = $userInfo->home_terminal_timezone;

        $conTime = conTimezone($timeZone, Carbon::now());

        if ($latestLog) {

            if ($latestLog->current_shift_status == 1 || $latestLog->current_shift_status == 2 || $latestLog->current_shift_status == 5) {

                if ($rule_ids) {

                    $cycleBreakRule = Rule::whereIn('id', $rule_ids)
                        ->where(function ($query) {
                            $query->where('reason', 8);
                        })
                        ->first();

                    $shiftBreakRule = Rule::whereIn('id', $rule_ids)
                        ->where(function ($query) {
                            $query->where('reason', 7);
                        })
                        ->first();


                    if ($shiftBreakRule) {

                        $shiftMinHour = $shiftBreakRule->min_hour_limit;

                        if (Carbon::parse($latestLog->created_at)->diffInHours(Carbon::now()) > $shiftMinHour) {
                            $shift_start = 1;
                        }

                    }

                    if ($cycleBreakRule) {

                        $cycleMinHour = $cycleBreakRule->min_hour_limit;

                        if (Carbon::parse($latestLog->created_at)->diffInHours(Carbon::now()) > $cycleMinHour) {
                            $cycle_start = 1;
                        }

                    }

                }

            }

        } else {

            $shift_start = 1;

            $cycle_start = 1;

        }


        $data = [
            'driver_id' => Auth::user()->id,
            'vehicle_id' => $request->vehicle_id,
            'current_shift_status' => $request->driver_status,
            'message_reason' => $request->message_reason,
            'is_active' => 1,
            'created_by' => Auth::user()->id,
            'cycle_start' => $cycle_start,
            'shift_start' => $shift_start,
            'created_at' => $conTime,
        ];

        DriverShiftLog::create($data);

        return redirect(route('driver.activity.dashboard.index'));

    }

    public function edit(Request $request, $id)
    {

        $log = DriverShiftLog::with('user', 'vehicle', 'option')->find($id);
        $driver = User::where('user_type', 'U')->where('id', Auth::user()->id)->get();
        $vehicle = VehicleAssign::where('driver_id', Auth::user()->id)
            ->with('vehicle')
            ->get();
        $listOption = ListOption::getOptions("driving_status", [], "1");
        return view('driver.driver-activity.edit', compact('log', 'driver', 'vehicle', 'listOption'));

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'driver_status' => 'required',
        ]);
        $driverShiftLog = DriverShiftLog::find($id);

        $driverShiftLog->update([
            'current_shift_status' => $request->driver_status,
            'message_reason' => $request->message_reason,
            'updated_by' => Auth::user()->id,
        ]);

        return redirect(route('driver.activity.dashboard.index', [request()->lang]));
    }

}
