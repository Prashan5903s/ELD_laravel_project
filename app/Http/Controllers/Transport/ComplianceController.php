<?php

namespace App\Http\Controllers\Transport;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DriverShiftLog;
use App\Models\Language;
use App\Models\User;
use App\Models\VehicleAssign;
use App\Models\VehicleLogHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ComplianceController extends Controller
{
    public function index(Request $request, $lang)
    {
        if (empty($lang)) {
            return redirect()->route('transport.dashboard', ['en']);
        }

        $language = Language::where('Short_name', $lang)->first();

        if (!$language) {
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['en']);
        } else {
            App::setLocale($lang);
        }

        $currentDate = Carbon::now();
        $eightDaysAgo = Carbon::now()->subDays(8);
        $currentDateTime = Carbon::now()->format('M d, Y h:i:s A');
        $dateTimeDayBefore = Carbon::now()->subDays(8)->format('M d, Y h:i:s A');
        $users = User::where('user_type', 'U')->where('master_id', Auth::user()->id)->get();
        $data = [];
        $allVehicleLogs = [];
        $cycles = [];
        $shiftStart = null;
        $currentCycle = [];
        $startCycleTime = null;
        $totalVehicleLogCount = 0; // Initialize the total vehicle log count

        if ($users) {

            foreach ($users as $driver) {
                $vehicleAssigns = VehicleAssign::where('driver_id', $driver->id)->get();
                if ($vehicleAssigns) {

                    $totalViolationTime60 = 0; // Total violation time in minutes for 60 hours cycle
                    $totalViolationTime14 = 0; // Total violation time in minutes for 14 hours shift

                    foreach ($vehicleAssigns as $vehicleAssign) {
                        $device = Device::where('vehicle_id', $vehicleAssign->vechile_id)->first();
                        $shiftLogs = DriverShiftLog::where('driver_id', $driver->id)
                            ->where('vehicle_id', $vehicleAssign->vechile_id)
                            ->where('created_at', '>=', $eightDaysAgo)
                            ->orderBy('id', 'asc')
                            ->get();

                        if ($shiftLogs) {

                            // Calculate cycles
                            foreach ($shiftLogs as $shiftLog) {
                                if ($shiftLog->cycle_start == 1) {
                                    if ($currentCycle) {
                                        $cycles[] = $currentCycle;
                                    }
                                    $currentCycle = [$shiftLog];
                                    $startCycleTime = new Carbon($shiftLog->created_at);
                                } else {
                                    $currentCycle[] = $shiftLog;
                                }
                            }

                            if ($currentCycle) {
                                $cycles[] = $currentCycle;
                            }

                            // Calculate cycle violations
                            foreach ($cycles as $cycle) {
                                $startCycleTime = new Carbon($cycle[0]->created_at);
                                $endCycleTime = new Carbon(end($cycle)->created_at);
                                if (end($cycle)->current_shift_status != 1) {
                                    $endCycleTime = $currentDate;
                                }
                                $cycleDuration = $startCycleTime->diffInMinutes($endCycleTime);
                                if ($cycleDuration > 3600) { // 60 hours * 60 minutes
                                    $totalViolationTime60 += ($cycleDuration - 3600);
                                }
                            }

                            // Calculate shifts and violations
                            $shiftStart = null;

                            $latestShiftLogEnd = $currentDate; // Initialize with current date as default

                            $latestShiftLog = DriverShiftLog::where('driver_id', $driver->id)
                                ->where('vehicle_id', $vehicleAssign->vechile_id)
                                ->orderBy('id', 'desc')
                                ->first();



                            if ($latestShiftLog && $latestShiftLog->current_shift_status == 1) {
                                $latestShiftLogEnd = new Carbon($latestShiftLog->created_at);
                            }

                            for ($i = 0; $i < count($shiftLogs); $i++) {

                                $log = $shiftLogs[$i];
                                if ($log && $log->current_shift_status == 1) {


                                    if ($shiftStart) {

                                        $shiftEnd = new Carbon($log->created_at);
                                        $shiftDuration = $shiftStart->diffInMinutes($shiftEnd);

                                        if ($shiftDuration > 840) { // 14 hours * 60 minutes
                                            $totalViolationTime14 += ($shiftDuration - 840);
                                        }

                                        // Fetch VehicleLogHistory within the shift period
                                        if ($device) {

                                            $vehicleLogs = VehicleLogHistory::where('device_id', $device->id)
                                                ->whereBetween('created_at', [$shiftStart, $shiftEnd])
                                                ->whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])
                                                ->orderBy('id', 'asc')
                                                ->get();

                                            $totalShiftTime = 0; // Initialize the total shift time

                                            for ($j = 0; $j < count($vehicleLogs); $j++) {

                                                $log1 = $vehicleLogs[$j];

                                                $log2 = $vehicleLogs[$j + 1] ?? null;

                                                if ($log2) {

                                                    $start = new Carbon($log1['created_at']);

                                                    $end = new Carbon($log2['created_at']);

                                                } else {

                                                    $start = new Carbon($log1['created_at']);

                                                    $end = $currentDate;

                                                }

                                                $totalShiftTime += $start->diffInMinutes($end);

                                                // Check if the total shift time crosses 8 hours and calculate violation
                                                if ($totalShiftTime > 480) {

                                                    if ($end->diffInMinutes($start) >= 30) {

                                                        $totalViolationTime14 += ($totalShiftTime - 480);

                                                    }

                                                }

                                                // Check if the total shift time crosses 11 hours and calculate violation
                                                if ($totalShiftTime > 660) {

                                                    $totalViolationTime14 += ($totalShiftTime - 660);

                                                }

                                            }

                                            // Append the vehicle logs to the allVehicleLogs array
                                            $allVehicleLogs = array_merge($allVehicleLogs, $vehicleLogs->toArray());

                                        }

                                        $shiftStart = $shiftEnd; // Start new shift

                                    }else {
                                        if ($i == 0) {

                                            $log = $shiftLogs[0];

                                            $shiftStart = new Carbon($log->created_at);

                                        }
                                    }

                                } else {

                                    if ($i == 0) {

                                        $log = $shiftLogs[0];

                                        $shiftStart = new Carbon($log->created_at);

                                    }

                                }

                            }

                            // Handle the last ongoing shift
                            if ($shiftStart) {

                                $shiftEnd = ($latestShiftLog && $latestShiftLog->current_shift_status == 1) ? $latestShiftLogEnd : $currentDate;

                                $shiftDuration = $shiftStart->diffInMinutes($shiftEnd);

                                if ($shiftDuration > 840) { // 14 hours * 60 minutes
                                    $totalViolationTime14 += ($shiftDuration - 840);
                                }

                                // Fetch VehicleLogHistory for the ongoing shift
                                if ($device) {


                                    $vehicleLogs = VehicleLogHistory::where('device_id', $device->id)
                                        ->whereBetween('created_at', [$shiftStart, $shiftEnd])
                                        ->whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])
                                        ->orderBy('id', 'asc')
                                        ->get();

                                    $totalShiftTime = 0; // Initialize the total shift time
                                    for ($j = 0; $j < count($vehicleLogs); $j++) {
                                        $log1 = $vehicleLogs[$j];
                                        $log2 = $vehicleLogs[$j + 1] ?? null;

                                        if ($log2) {
                                            $start = new Carbon($log1['created_at']);
                                            $end = new Carbon($log2['created_at']);
                                        } else {
                                            $start = new Carbon($log1['created_at']);
                                            $end = $currentDate;
                                        }

                                        $totalShiftTime += $start->diffInMinutes($end);

                                        // Check if the total shift time crosses 8 hours and calculate violation
                                        if ($totalShiftTime > 480) {
                                            if ($end->diffInMinutes($start) >= 30) {
                                                $totalViolationTime14 += ($totalShiftTime - 480);
                                            }
                                        }

                                        // Check if the total shift time crosses 11 hours and calculate violation
                                        if ($totalShiftTime > 660) {
                                            $totalViolationTime14 += ($totalShiftTime - 660);
                                        }
                                    }

                                    // Append the vehicle logs to the allVehicleLogs array
                                    $allVehicleLogs = array_merge($allVehicleLogs, $vehicleLogs->toArray());
                                }
                            }

                            // Ensure unique vehicle logs by 'id' to avoid duplicates

                            // Ensure unique vehicle logs by 'id' to avoid duplicates
                            $allVehicleLogs = array_map("unserialize", array_unique(array_map("serialize", $allVehicleLogs)));

                            // Increment total vehicle log count
                        }
                    }

                    $totalVehicleLogCount += count($allVehicleLogs);
                    // Calculate total violation time by adding both 60 and 14 hours violation times
                    $totalViolationTime = $totalViolationTime60 + $totalViolationTime14;

                    // Format totalViolationTime in "00 h 00 min"
                    $hours = floor($totalViolationTime / 60);
                    $minutes = $totalViolationTime % 60;
                    $violatedTimeFormatted = sprintf('%02d h %02d min', $hours, $minutes);

                    $data[] = [
                        'name' => $driver->first_name . ' ' . $driver->last_name,
                        'violation' => $totalViolationTime > 0 ? $violatedTimeFormatted : '00 h 00 min',
                        'vehicle_logs' => $allVehicleLogs, // Add vehicle logs to the response data
                    ];
                }
            }
        }

        $allLogs = VehicleLogHistory::whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])->get();
        $totalLogs = count($allLogs);

        // Calculate assigned and unassigned percentages
        $assgn_per = ($totalVehicleLogCount / $totalLogs) * 100;
        $assgn_per = sprintf('%.2f', $assgn_per);
        $unassgn_per = 100 - $assgn_per;

        // exit();

        // return response()->json(['data' => $data, 'currentDate' => $currentDate, 'eightDaysAgo' => $eightDaysAgo, 'totalVehicleLogCount' => $totalVehicleLogCount, 'allLogs' => $totalLogs, 'assignedPercent' => $assgn_per, 'unassignedPercent' => $unassgn_per,]);

        return view('transport.compliance.index', compact(
            'data',
            'currentDate',
            'eightDaysAgo',
            'totalVehicleLogCount',
            'totalLogs',
            'assgn_per',
            'unassgn_per',
            'dateTimeDayBefore',
            'currentDateTime'
        ));

    }

}
