<?php

namespace App\Http\Controllers\Transport\Settings\Organization;

use App\Http\Controllers\Controller;
use App\Models\RuleAssign;
use App\Models\UserInfo;
use App\Models\VehicleAssign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\DriverShiftLog;
use App\Models\User;
use App\Models\Rule;
use App\Models\Vehicle;
use App\Models\ListOption;
use App\Rules\UniqueDriverShiftLog;

class DriverActivityController extends Controller
{
    public function index(Request $request, $lang)
    {
        $driverShift = DriverShiftLog::with('user', 'vehicle', 'option')->get();

        return view('transport.settings.organization.driver_activity.index', compact('driverShift'));
    }

    public function create(Request $request, $lang)
    {
        $driver = User::where('user_type', 'U')->where('master_id', Auth::user()->id)->get();
        $vechile = Vehicle::where('created_by', Auth::user()->id)->get();
        $listOption = ListOption::getOptions("driving_status", [], "1");
        return view('transport.settings.organization.driver_activity.add', compact('driver', 'vechile', 'listOption'));
    }

    public function store(Request $request, $lang)
    {

        $request->validate([
            'driver_id' => ['required', new UniqueDriverShiftLog($request->driver_id, $request->vehicle_id, $request->driver_status)],
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

        $userInfo = UserInfo::where('user_id', $request->driver_id)->first();

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
            'driver_id' => $request->driver_id,
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

        return redirect(route('driver.activity.index', [$lang]));
    }

    public function edit(Request $request, $lang, $id)
    {
        $log = DriverShiftLog::with('user', 'vehicle', 'option')->find($id);
        $driver = User::where('user_type', 'U')->where('master_id', Auth::user()->id)->get();
        $vechile = Vehicle::where('created_by', Auth::user()->id)->get();
        $listOption = ListOption::getOptions("driving_status", [], "1");
        return view('transport.settings.organization.driver_activity.edit', compact('log', 'driver', 'vechile', 'listOption'));
    }

    public function update(Request $request, $lang, $id)
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

        return redirect(route('driver.activity.index', [request()->lang]));
    }
    public function get_vehicles(Request $request)
    {

        $driverId = $request->input('driver_id');
        $vehicleAssignments = VehicleAssign::where('driver_id', $driverId)->get();

        $vehicles = [];
        foreach ($vehicleAssignments as $assignment) {
            $vehicleId = $assignment->vechile_id;
            $vehicle = Vehicle::find($vehicleId);
            if ($vehicle) {
                $vehicles[] = $vehicle;
            }
        }

        return response()->json($vehicles);

    }
}
