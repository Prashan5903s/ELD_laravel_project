<?php
namespace App\Http\Controllers\Transport\Assets;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Models\Language;
use App\Models\ListOption;
use App\Models\State;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\BluetoothLogData;
use App\Models\VehicleLogHistory;
use App\Notifications\NonDrivingNotification;
use App\Models\Vehicle;
use App\Models\RuleAssign;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Models\DriverShiftLog;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Twilio\Rest\Client;

class VehiclesController extends Controller
{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {


        $transportIds = User::where('user_type', 'TR')->pluck('id');

        $drivers = User::where('user_type', 'U')
            ->whereIn('master_id', $transportIds)
            ->get();

        function conTimezone($timezone, $time)
        {
            // Parse the given time string and set it to the specified timezone
            $convertedTime = Carbon::parse($time)->setTimezone($timezone);

            return $convertedTime->toDateTimeString();
        }

        if ($drivers && count($drivers) > 0) {

            foreach ($drivers as $data) {
                $shiftStart = 0;
                $cycleStart = 0;
                $locationName = "";

                $id = $data->id;

                $rule_ids = RuleAssign::where('user_id', $id)
                    ->pluck('rule_id');


                $master = $data->master_id;

                $userInfo = UserInfo::where('user_id', $id)->first();

                if (!$userInfo || empty($userInfo->home_terminal_timezone)) {
                    continue;
                }

                $timeZone = $userInfo->home_terminal_timezone;

                //Current time of today
                $currentTime = Carbon::now()->setTimezone($timeZone)->toDateTimeLocalString();

                $currentTime = Carbon::parse($currentTime);

                $startTime = Carbon::parse($currentTime)->startOfDay();

                $endTime = Carbon::parse($currentTime)->endOfDay();

                $driverLog = DriverShiftLog::where('driver_id', $id)
                    ->where('is_add_approved', 1)
                    ->latest('start_log_time')
                    ->first();

                if ($driverLog) {

                    $currentStatus = $driverLog->current_shift_status;

                    $timeData = create_end_time($driverLog, $startTime, $driverLog, $endTime, $currentTime);

                    $create = $timeData[0];

                    $last = $timeData[1];

                    $vehicleId = $driverLog->vehicle_id;

                    $vehicle = Vehicle::find($vehicleId);

                    if (!$vehicle) {
                        continue;
                    }

                    if ($currentStatus == 3 && $vehicleId) {

                        $device = Device::where('vehicle_id', $vehicleId)->first();

                        return $device;

                        if (!$device || empty($device->serial_number)) {
                            continue;
                        }


                        if ($device) {

                            $serialNumber = $device->serial_number;

                            if ($serialNumber) {

                                $vehicleLog = VehicleLogHistory::where('identifier', $serialNumber)
                                    ->where('is_notify', 0)
                                    ->where('speed', '>=', 5)
                                    ->whereBetween('event_date_time', [$create, $last])
                                    ->orderBy('event_date_time', 'asc')
                                    ->get();

                                $bluetoothLog = BluetoothLogData::whereRaw(
                                    "CAST(JSON_UNQUOTE(JSON_EXTRACT(log_data, '$.speed')) AS DECIMAL(10,2)) >= ?",
                                    [5]
                                )
                                    ->whereRaw(
                                        "JSON_UNQUOTE(JSON_EXTRACT(log_data, '$.vin')) = ?",
                                        [$vehicle->vin]
                                    )
                                    ->whereRaw(
                                        "JSON_UNQUOTE(JSON_EXTRACT(log_data, '$.start_log_time')) <= ?",
                                        [$last]
                                    )
                                    ->whereRaw(
                                        "JSON_UNQUOTE(JSON_EXTRACT(log_data, '$.end_log_time')) >= ?",
                                        [$create]
                                    )
                                    ->get();

                                return response()->json([count($vehicleLog) == 0 && count($bluetoothLog) == 0]);

                                if (count($vehicleLog) == 0 && count($bluetoothLog) == 0) {

                                    $firstName = $data->first_name;

                                    $lastName = $data->last_name;

                                    $url = route('transport.dashboard');

                                    $message = $firstName . ' ' . $lastName . ' ' . 'your current log is in driving with' . ' ' . $vehicle->name . ' ' . 'and data is not being recieved from device, Please change your current duty status. Thank You!';

                                    // Send notification to the current user
                                    $user1 = User::find($id);
                                    if ($user1) {
                                        $notification1 = new NonDrivingNotification($message, $url, $id);
                                        $user1->notify($notification1);
                                    }

                                    // Send notification to the master user
                                    $user2 = User::find($master);
                                    if ($user2) {
                                        $notification2 = new NonDrivingNotification($message, $url, $master);
                                        $user2->notify($notification2);
                                    }

                                    $logEndTime = $driverLog->end_log_time;

                                    // Close the current driving log if it's still open
                                    if (is_null($logEndTime)) {
                                        $logEndTime = $currentTime;

                                        $driverLog->update([
                                            'end_log_time' => $logEndTime,
                                        ]);
                                    }

                                    // Create the new Off Duty log
                                    $newDriverLog = DriverShiftLog::create([
                                        'driver_id' => $id,
                                        'start_log_time' => $logEndTime,
                                        'end_log_time' => null,
                                        'current_shift_status' => 1,
                                        'vehicle_id' => $vehicleId,
                                        'system_entry' => 1,
                                        'accepted' => 1,
                                        'is_add_approved' => 1,
                                        'is_edit_approved' => 1,
                                        'is_assign_approved' => 1,
                                        'is_edit' => 1,
                                        'is_active' => 1,
                                    ]);

                                    $startData = shift_cycle_start_check($newDriverLog, $currentTime, $locationName, $rule_ids, 0);

                                    if (count($startData) > 0) {
                                        $shiftStart = $startData[0];
                                        $cycleStart = $startData[1];
                                    }

                                    if ($newDriverLog) {

                                        $newDriverLog->update([
                                            'shift_start' => $shiftStart,
                                            'cycle_start' => $cycleStart,
                                        ]);
                                    }

                                }
                            }
                        }
                    }
                }
            }
        }


        // $user = Auth::user();
        $userIds = Auth::user()->master_id;

        $trans = User::where('master_id', $userIds)->get();

        $option = ListOption::where('list_id', 'fuel_type')->get();

        $make = ListOption::where('list_id', 'make')->get();

        $state = State::where('is_active', 1)->get();

        $throttle_wifi = Config::get('app.TH');

        $lang = $request->lang;

        if (isset($lang)) {

            App::setLocale($lang);

        } else {

            $user = Auth::user();

            $userInfo = UserInfo::where('user_id', $user->id)->first();

            $lang = Language::where('id', $userInfo->language_id)->first();

            $short = $lang->Short_name;

            App::setLocale($short);

        }

        $data['trans'] = $trans;

        $data['make'] = $make;

        $data['state'] = $state;

        $data['throttle_wifi'] = $throttle_wifi;

        $data['option'] = $option;

        $data['vehicles'] = Vehicle::where('created_by', Auth::user()->id)->get();

        $data['vehicle_year'] = Config::get('app.vehicle_year');

        return view('transport.assets.vehicles.index', $data);

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

    public function store(Request $request)
    {

        $request->validate([

            'name' => 'required',

            'vin' => 'required',

            'make' => 'required',

            'model' => 'required',

            'year' => 'required',

            'license_plate' => 'required',

            'fuel_type' => 'required',

            'license_state' => 'required',

            'fuel_tank_secondary' => 'required',

            'fuel_tank_primary' => 'required',

            'throttle_wifi' => 'required',

        ]);

        Vehicle::create([

            'name' => $request->name,

            'master_company_id' => Session::get('master_company_id'), // Company id

            'master_id' => Session::get('master_id'), // Group id

            'vin' => $request->vin,

            'make' => $request->make,

            'model' => $request->model,

            'year' => $request->year,

            'fuel_type' => $request->fuel_type,

            'license_state' => $request->license_state,

            'throttle_wifi' => $request->throttle_wifi,

            'fuel_tank_primary' => $request->fuel_tank_primary,

            'fuel_tank_secondary' => $request->fuel_tank_secondary,

            'license_plate' => $request->license_plate,

            'notes' => $request->notes,

            'created_by' => $request->user()->id,

            'updated_by' => $request->user()->id,

        ]);

        return response()->json(['success' => 'Vehicle created successfully.']);

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

    public function update(Request $request, $lang, $id)
    {

        $request->validate([

            'name' => 'required',

            'vin' => 'required',

            'make' => 'required',

            'model' => 'required',

            'year' => 'required',

            'fuel_type' => 'required',

            'license_plate' => 'required',

            'license_state' => 'required',

            'fuel_tank_secondary' => 'required',

            'fuel_tank_primary' => 'required',

            'throttle_wifi' => 'required',

        ]);

        $vehicle = Vehicle::find($id);

        if (!isset($vehicle)) {

            return response()->json(['error', 'Vehicle not found.']);

        }

        $vehicle->update([

            'name' => $request->name,

            'vin' => $request->vin,

            'make' => $request->make,

            'model' => $request->model,

            'year' => $request->year,

            'fuel_type' => $request->fuel_type,

            'license_state' => $request->license_state,

            'throttle_wifi' => $request->throttle_wifi,

            'fuel_tank_primary' => $request->fuel_tank_primary,

            'fuel_tank_secondary' => $request->fuel_tank_secondary,

            'license_plate' => $request->license_plate,

            'notes' => $request->notes,

            'updated_by' => $request->user()->id,

        ]);

        return response()->json(['success' => 'Vehicle updated successfully.']);

    }

    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($lang, $id)
    {

        $vehicle = Vehicle::find($id);

        if (!isset($vehicle)) {

            return response()->json(['error' => 'Vehicle not found.']);

        }

        if ($vehicle->status == "0") {

            $vehicle->update([

                'status' => '1',

            ]);

            return response()->json(['success' => 'Vehicle activated successfully.']);

        } else {

            $vehicle->update([

                'status' => '0',

            ]);

            return response()->json(['success' => 'Vehicle de-activated successfully.']);

        }

    }

}
