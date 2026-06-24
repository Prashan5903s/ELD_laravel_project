<?php

namespace App\Http\Controllers\API\Transport\Driver;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Device;
use App\Models\DriverShiftLog;
use App\Models\Language;
use App\Models\ListOption;
use App\Models\Location;
use App\Models\Package;
use App\Models\PackageAssign;
use App\Models\PackageModule;
use App\Models\RuleAssign;
use Illuminate\Support\Facades\DB;
use App\Models\Rules;
use App\Models\State;
use App\Models\Timezone;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\Vehicle;
use App\Models\VehicleAssign;
use App\Models\VehicleLogHistory;
use App\Notifications\NotifyNotification;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class DriverAPIController extends Controller
{
    public function index()
    {
        $curr_user = Auth::user();
        if ($curr_user) {
            $user = User::with('userInfo')  // Eager load the userInfo relation
                ->where('user_type', 'U')
                ->where('master_id', $curr_user->id)
                ->get();
            if ($user) {
                return response()->json([
                    'user' => $user,
                    'success' => 'Data fetched',
                ], 200);
            } else {
                return response()->json([
                    'success' => 'No driver exist',
                ], 401);
            }
        } else {
            return response()->json([
                'success' => 'Unauthorised',
            ], 403);
        }
    }

    public function create()
    {
        $data['language'] = Language::where('is_active', 1)->get();

        $data['cargo'] = ListOption::where('list_id', 'cargo_type')->get();

        $data['cycle'] = Rules::where('show_id', 2)->where('is_active', 1)->get();

        $data['restart'] = Rules::where('show_id', 4)->where('is_active', 1)->get();

        $data['break'] = Rules::where('show_id', 3)->where('is_active', 1)->get();

        $data['Country'] = Country::with([
            'states' => function ($query) {
                $query->where('is_active', 1)->with([
                    'cities' => function ($query) {
                        $query->where('is_active', 1);
                    },
                ]);
            },
        ])->where('is_active', 1)->get();

        $data['timezones'] = Timezone::where('status', 1)->get();

        $user = Auth::user();

        if ($user) {
            return response()->json($data);
        } else {
            return response()->json([
                'message' => 'Wrong user',
            ], 403);
        }
    }

    public function store(Request $request)
    {

        $is_master = $request->session()->get('is_master');

        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->user_type = 'U';
        $user->language_id = $request->language_id;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->mobile_no = $request->phone;
        $user->landline_no = $request->landline_no;
        $user->country_id = $request->country_id;
        $user->country_code = $request->country_code ?? '';
        $user->state_id = $request->state_id;
        $user->is_active = $request->is_active ?? 1; // Default to active
        $user->city_id = $request->city_id;
        $user->pin_code = $request->pincode; // Use correct variable name
        $user->address = $request->address;
        $user->timezone = $request->timezone;
        $user->is_master = 0; // Default to 0

        if ($is_master == 1) {
            $user->master_id = $request->session()->get('ids');
            $user->master_company_id = $request->session()->get('master_company_id');
        } else {
            $user->save(); // Save the user before assigning master_company_id
            $user->master_company_id = Auth::user()->master_company_id;
            $user->master_id = Auth::user()->id;
        }

        $user->save();

        // Initialize rules array with fixed values
        $rules = [
            ['rule_id' => 1],
            ['rule_id' => 2],
            ['rule_id' => 7],
            ['rule_id' => 9],
        ];

        // Add conditional rule if the condition is met
        if ($request->adverse_condition == '1') {
            $rules[] = ['rule_id' => 10];
        }

        // Add rules based on request values
        $rules[] = ['rule_id' => $request->rest_break];
        $rules[] = ['rule_id' => $request->restart];
        $rules[] = ['rule_id' => $request->cycle_rule];

        // Insert each rule into the database
        foreach ($rules as $rule) {
            RuleAssign::create([
                'rule_id' => $rule['rule_id'],
                'user_id' => $user->id,
                'master_id' => $user->master_id,
                'master_company_id' => $user->master_company_id,
                'created_bu' => Auth::user()->id,
            ]);
        }

        $userInfos = new UserInfo;
        $userInfos->cargo_type_id = $request->cargo_type;
        $userInfos->user_id = $user->id;
        $userInfos->licenseNumber = $request->driver_license_number;
        $userInfos->username = $request->username;
        $userInfos->note = $request->note;
        $userInfos->driver_license_state = $request->driver_license_state;
        $userInfos->home_terminal_timezone = $request->home_terminal_timezones;
        $userInfos->career_name = $request->career_name;
        $userInfos->main_office_address = $request->main_office_address;
        $userInfos->carrer_us_dot_number = $request->carrer_us_dot_number;
        $userInfos->home_terminal_name = $request->home_terminal_name;
        $userInfos->home_terminal_address = $request->home_terminal_address;
        $userInfos->driver_id = $request->driver_id;
        $userInfos->save();

        $user = User::where('user_type', 'SA')->first();
        $message = "New driver has been added " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('transport.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        return response()->json('Added successfully');
    }

    public function show(Request $request, $id) {}
    public function edit(request $request, $id)
    {
        $user = Auth::user();

        $data['user'] = user::find($id);

        $data['userInfo'] = UserInfo::where('user_id', $id)->first();

        $data['language'] = Language::all();

        $data['Country'] = Country::with([
            'states' => function ($query) {
                $query->where('is_active', 1)->with([
                    'cities' => function ($query) {
                        $query->where('is_active', 1);
                    },
                ]);
            },
        ])->where('is_active', 1)->get();

        $data['state'] = State::where('is_active', 1)->get();

        $userId = $id; // Replace this with the actual user_id you want to use

        $cycle = Rules::where('show_id', 2)
            ->where('is_active', 1)
            ->get();

        $cycleIds = $cycle->pluck('id');

        $data['cycle'] = RuleAssign::whereIn('rule_id', $cycleIds)->where('user_id', $userId)->with('rule')->get();

        $restart = Rules::where('show_id', 4)
            ->where('is_active', 1)
            ->get();

        $restartIds = $restart->pluck('id');

        $data['restart'] = RuleAssign::whereIn('rule_id', $restartIds)->where('user_id', $userId)->with('rule')->get();

        $break = Rules::where('show_id', 3)
            ->where('is_active', 1)
            ->get();

        $breakIds = $break->pluck('id');

        $data['break'] = RuleAssign::whereIn('rule_id', $breakIds)->where('user_id', $userId)->with('rule')->get();

        $advrs = Rules::where('show_id', 5)
            ->where('is_active', 1)
            ->get();

        $advIds = $advrs->pluck('id');

        $data['adverse'] = RuleAssign::whereIn('rule_id', $advIds)->where('user_id', $userId)->with('rule')->get();

        $userInfo = UserInfo::where('user_id', $id)->first();

        $cargoTypeId = $userInfo->cargo_type_id;

        $listOptions = ListOption::where('list_id', 'cargo_type')
            ->where('option_id', $cargoTypeId)
            ->first();

        $data['cargo'] = $listOptions;

        $data['HOS'] = Config::get('app.HOS');

        $data['EDSH'] = Config::get('app.EDSH');

        $data['UE'] = Config::get('app.UE');

        $data['location'] = Location::all();

        $data['timezones'] = Timezone::where('status', 1)->get();

        if ($data['user']->master_id == $user->id) {

            return response()->json(

                $data

            );
        } else {

            return response()->json([

                'message' => 'Data not found',

            ], 401);
        }
    }

    public function update(Request $request, $id)
    {

        $is_master = $request->session()->get('is_master');
        $user = User::find($id);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->user_type = 'U';
        $user->language_id = $request->language_id;
        $user->email = $request->email;
        $user->mobile_no = $request->phone;
        $user->landline_no = $request->landline_no;
        $user->country_id = $request->country_id;
        $user->country_code = $request->country_code;
        $user->timezone = $request->timezone;
        $user->state_id = $request->state_id;
        $user->city_id = $request->city_id;
        $user->pin_code = $request->pincode;
        $user->is_active = $request->is_active;
        $user->address = $request->address;
        $user->is_master = 0;

        if ($is_master == 1) {
            $user->master_id = Auth::user()->id;
            $user->master_company_id = Auth::user()->master_company_id;
        } else {
            $user->master_company_id = Auth::user()->master_company_id;
            $user->master_id = Auth::user()->id;
        }
        $user->save();

        RuleAssign::where('user_id', '=', $id)->delete();

        // Initialize rules array with fixed values
        $rules = [
            ['rule_id' => 1],
            ['rule_id' => 2],
            ['rule_id' => 7],
            ['rule_id' => 9],
        ];

        // Add conditional rule if the condition is met
        if ($request->adverse_condtion == '1') {
            $rules[] = ['rule_id' => 10];
        }

        // Add rules based on request values, ensuring they are arrays
        if ($request->rest_break) {
            $rules[] = ['rule_id' => $request->rest_break];
        }
        if ($request->restart) {
            $rules[] = ['rule_id' => $request->restart];
        }
        if ($request->cycle_rule) {
            $rules[] = ['rule_id' => $request->cycle_rule];
        }

        // Insert each rule into the database
        foreach ($rules as $rule) {
            RuleAssign::create([
                'rule_id' => $rule['rule_id'],
                'user_id' => $user->id,
                'master_id' => $user->master_id,
                'master_company_id' => $user->master_company_id,
                'updated_by' => Auth::user()->id,
            ]);
        }

        $userInfos = UserInfo::where('user_id', $id)->first();
        $userInfos->user_id = $user->id;
        $userInfos->cargo_type_id = $request->cargo_type;
        $userInfos->licenseNumber = $request->driver_license_number;
        $userInfos->username = $request->username;
        $userInfos->note = $request->note;
        $userInfos->driver_license_state = $request->driver_license_state;
        $userInfos->home_terminal_timezone = $request->home_terminal_timezones;
        $userInfos->career_name = $request->career_name;
        $userInfos->main_office_address = $request->main_office_address;
        $userInfos->carrer_us_dot_number = $request->carrer_us_dot_number;
        $userInfos->home_terminal_name = $request->home_terminal_name;
        $userInfos->home_terminal_address = $request->home_terminal_address;
        $userInfos->driver_id = $request->driver_id;
        $userInfos->save();

        $user = User::where('user_type', 'SA')->first();
        $message = "New driver has been edited " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('transport.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        return response()->json('Added successfully');
    }

    public function step2()
    {

        $data['state'] = State::where('is_active', 1)->get();

        $data['HOS'] = Config::get('app.HOS');

        $data['EDSH'] = Config::get('app.EDSH');

        $data['UE'] = Config::get('app.UE');

        $user = Auth::user();

        if ($user) {

            return response()->json($data);
        } else {
            return response()->json([
                'message' => 'Wrong user',
            ], 403);
        }
    }

    public function step3()
    {
        $data['location'] = Location::where('status', 1)->get();

        $data['timezones'] = Timezone::where('status', 1)->get();

        $user = Auth::user();

        if ($user) {

            return response()->json($data);
        } else {
            return response()->json([
                'message' => 'Wrong user',
            ], 403);
        }
    }

    public function driver_detail(Request $request, $id)
    {

        $userInfo = UserInfo::where('user_id', $id)->first();

        $timeZone = $userInfo->home_terminal_timezone;

        //Current time of today
        $currTime = Carbon::now()->setTimezone($timeZone)->toDateTimeLocalString();

        $currTime = Carbon::parse($currTime);

        $data = driver_log_time($id, $currTime);

        return response()->json($data);
    }

    public function driver_hos_detail(Request $request, $id)
    {

        $userInfo = UserInfo::where('user_id', $id)->first();

        $timezone = $userInfo->home_terminal_timezone;

        $currentTime = Carbon::parse()->setTimezone($timezone)->toDateTimeLocalString();

        $currentTime = Carbon::parse($currentTime);

        $data = driver_log_time($id, $currentTime);

        return response()->json($data);
    }

    public function driver_date_hos_data(Request $request, $id, $startTime, $endTime)
    {

        $data = hos_date_data($id, $startTime, $endTime);

        // Return the array of dates
        return response()->json($data);
    }

    public function driver_location_data(Request $request, $id)
    {

        $datas = [];

        if ($id) {

            $driver = User::find($id);

            if ($driver) {

                $driverId = $driver->id;

                $vehicleAssgn = VehicleAssign::where('driver_id', $driverId)->get();

                if ($vehicleAssgn) {

                    foreach ($vehicleAssgn as $data) {

                        $vehicleId = $data->vechile_id;

                        $vehicle = Vehicle::find($vehicleId);

                        if ($vehicle) {

                            $device = Device::where('vehicle_id', $vehicleId)->first();

                            if ($device) {

                                $logVehicle = VehicleLogHistory::where('identifier', $device->serial_number)->latest()->first();

                                if ($logVehicle) {

                                    $logVehicleLocation = $logVehicle->location;
                                    $locationData = json_decode($logVehicleLocation, true);

                                    $latitude = $locationData['GeoLocation']['Latitude'];
                                    $longitude = $locationData['GeoLocation']['Longitude'];

                                    $rotate = $logVehicle->direction_alpha;

                                    $datas[] = [

                                        $vehicle->name,

                                        $latitude,

                                        $longitude,

                                        $rotate,

                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        return $datas;
    }

    public function trans_perms()
    {
        // Get the authenticated user
        $user = Auth::user();
        $currTime = Carbon::now();

        // Check if the user is of type "TR"


        // Retrieve the latest active package assignment for the user
        $packAssgn = PackageAssign::where('user_id', $user->id)
            ->where('end_date', '>', $currTime) // Check if the package assignment is still valid
            ->latest()
            ->first();

        if ($packAssgn) {
            // Fetch the package details
            $package = Package::where('id', $packAssgn->package_id)->first();

            if ($package) {
                // Get the permissions associated with the package
                $permissions = PackageModule::where('package_id', $package->id)->pluck('permission_id');

                // Check if there are any permissions
                if ($permissions->isNotEmpty()) {
                    return response()->json($permissions);
                } else {
                    return response()->json(['error' => 'No permissions available for this package'], 401);
                }
            } else {
                return response()->json(['error' => 'Package not found'], 404);
            }
        } else {
            return response()->json([], 200);
        }
    }


    public function graph_chart_data(Request $request, $id)
    {

        $user = User::where('user_type', 'U')->where('id', $id)->first();

        $userInfo = UserInfo::where('user_id', $id)->first();

        $timeZone = $userInfo->home_terminal_timezone;

        //Current time of today
        $currentTime = Carbon::parse()->setTimezone($timeZone)->toDateTimeLocalString();

        $currentTime = Carbon::parse($currentTime);

        $currTimes = $currentTime;

        $startTime = $currTimes->copy()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $endTime = $currTimes->copy()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $data = graph_hos_chart($id, $startTime, $endTime, $currentTime);

        return response()->json($data);
    }

    public function graph_chart_date_data(Request $request, $id, $date)
    {

        $user = User::where('user_type', 'U')->where('id', $id)->first();

        $userInfo = UserInfo::where('user_id', $id)->first();

        $timeZone = $userInfo->home_terminal_timezone;

        //Current time of today
        $currTime = Carbon::now()->setTimezone($timeZone);

        $currTimes = Carbon::parse($currTime->toDateTimeString());

        $timeDate = Carbon::parse($date);

        $currentTime = $currTimes;

        $startTime = $timeDate->copy()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $endTime = $timeDate->copy()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $data = graph_hos_chart($id, $startTime, $endTime, $currentTime);

        return response()->json($data);
    }

    public function check_driver_api(Request $request, $id)
    {
        $user = User::find($id);

        if ($user) {

            $masterId = $user->master_id;
            $idAuth = Auth::user()->id;

            if ($masterId == $idAuth) {
                return response()->json(true);
            }

            return response()->json(false);
        }

        return response()->json(false);
    }

    public function check_unique_email($email, $id = null)
    {
        $query = User::where('email', $email);

        // If $id is provided, exclude the user with that ID
        if ($id) {
            $query->where('id', '!=', $id);
        }

        $userCount = $query->count();

        return response()->json($userCount);
    }

    public function generate_username()
    {
        do {
            // Generate a random alphanumeric username between 8 and 20 characters
            $username = Str::random(rand(8, 20));

            // Ensure the username is alphanumeric (letters and numbers only)
            $username = preg_replace('/[^A-Za-z0-9]/', '', $username);

            // Check if the username already exists in the UserInfo table
            $usernameExists = UserInfo::where('username', $username)->exists();
        } while ($usernameExists || strlen($username) < 8 || strlen($username) > 20);

        return response()->json($username);
    }

    public function check_unique_username($username, $id = null)
    {

        $query = UserInfo::where('username', $username);

        // If $id is provided, exclude the user with that ID
        if ($id) {
            $query->where('user_id', '!=', $id);
        }

        // Check if any user exists with the given username
        $isUnique = $query->count();

        return response()->json($isUnique);
    }
}
