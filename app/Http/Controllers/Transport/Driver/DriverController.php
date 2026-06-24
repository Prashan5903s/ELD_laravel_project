<?php

namespace App\Http\Controllers\Transport\Driver;

use App\Http\Controllers\Controller;
use App\Models\ApiLogger;
use App\Models\Country;
use App\Models\Device;
use App\Models\DriverShiftLog;
use App\Models\Language;
use App\Models\ListOption;
use App\Models\Location;
use App\Models\RuleAssign;
use App\Models\State;
use App\Models\Timezone;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\Vehicle;
use App\Models\VehicleAssign;
use App\Models\VehicleLogHistory;
use App\Notifications\NotifyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Rules;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{

    public function index(Request $request, $lang)
    {
        if (empty($lang)) {
            return redirect()->route('transport.dashboard', ['en']);
        }
        $languag = Language::where('Short_name', $lang)->first();
        if (!$languag) {
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['en']);
        } else {
            App::setLocale($lang);
        }
        
        $user = Auth::user();
        $userIds = Auth::user()->master_id;
        $trans = User::where('master_id', $userIds)->get();
        $users = User::where('user_type', 'U')->where('master_id', $request->session()->get('ids'))->get();
        return view('transport.driver.index', compact('users', 'trans'));
    }

    public function add(Request $request, $lang)
    {
        if (empty($lang)) {
            return redirect()->route('transport.dashboard', ['en']);
        }
        $languag = Language::where('Short_name', $lang)->first();
        if (!$languag) {
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['en']);
        } else {
            App::setLocale($lang);
        }
        $user = Auth::user();
        $userIds = Auth::user()->master_id;
        $trans = User::where('master_id', $userIds)->get();
        $statess = State::where('is_active', 1)->get();
        $lang = Language::where('is_active', 1)->get();
        $location = Location::all();
        $HOS = Config::get('app.HOS');
        $EDSH = Config::get('app.EDSH');
        $UE = Config::get('app.UE');

        $countries = Country::with([
            'states' => function ($query) {
                $query->where('is_active', 1)->with([
                    'cities' => function ($query) {
                        $query->where('is_active', 1);
                    },
                ]);
            },
        ])
            ->where('is_active', 1)
            ->get();

        $timezones = Timezone::where('status', 1)->get();

        return view('transport.driver.add', compact('countries', 'timezones', 'statess', 'lang', 'trans', 'HOS', 'EDSH', 'UE', 'location'));
    }

    public function addForm(Request $request, $lang)
    {

        if (empty($lang)) {
            return redirect()->route('transport.dashboard', ['en']);
        }
        $languag = Language::where('Short_name', $lang)->first();
        if (!$languag) {
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['en']);
        } else {
            App::setLocale($lang);
        }
        $is_master = $request->session()->get('is_master');

        $imageName = null; // Initialize imageName variable

        if ($request->hasFile('file')) {
            $imageName = time() . '.' . $request->file->extension();
            $request->file->move(public_path('driverss'), $imageName);
        }

        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->user_type = 'U';
        $user->language_id = $request->language_id;
        $user->avatar_image = $imageName; // Assign the image name
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->mobile_no = $request->mobile_no;
        $user->landline_no = $request->landline_no;
        $user->country_id = $request->country_id;
        $user->country_code = $request->country_code;
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
            $user->master_company_id = $user->id;
            $user->master_id = $user->id;
        }

        $user->save();

        $userInfos = new UserInfo;
        $userInfos->user_id = $user->id;
        $userInfos->licenseNumber = $request->driver_license_number;
        $userInfos->username = $request->username;
        $userInfos->note = $request->note;
        $userInfos->driver_license_state = $request->driver_license_state;
        $userInfos->hour_of_service = $request->hour_of_service;
        $userInfos->eld_day_start_hour = $request->eld_day_start_hour;
        $userInfos->home_terminal_timezone = $request->home_terminal_timezone;
        $userInfos->career_name = $request->career_name;
        $userInfos->main_office_address = $request->main_office_address;
        $userInfos->carrer_us_dot_number = $request->carrer_us_dot_number;
        $userInfos->home_terminal_name = $request->home_terminal_name;
        $userInfos->home_terminal_address = $request->home_terminal_address;
        $userInfos->peer_group_tag = $request->peer_group_tag;
        $userInfos->vechile_selection_tag = $request->vechile_selection_tag;
        $userInfos->trailor_selection_tag = $request->trailor_selection_tag;
        $userInfos->code_card = $request->id_card_code;
        $userInfos->tachograph_card = $request->tachograph_card;
        $userInfos->driver_status = $request->driver_status;
        $userInfos->driver_attribute = $request->driver_attribute;
        $userInfos->us_short_haul_exemption = $request->us_short_haul_exemption;
        $userInfos->driver_id = $request->driver_id;
        $userInfos->driver_ruleset_cycle = $request->driver_ruleset_cycle;
        $userInfos->save();

        $user = User::where('user_type', 'SA')->first();
        $message = "New driver has been added " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('transport.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        $request->session()->flash('success', __('lang.saved'));

        return redirect()->route('driver.index', [$lang]);

    }

    public function edit(Request $request, $lang, $id)
    {
        if (empty($lang)) {
            return redirect()->route('transport.dashboard', ['en']);
        }
        $languag = Language::where('Short_name', $lang)->first();
        if (!$languag) {
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['en']);
        } else {
            App::setLocale($lang);
        }
        $user = Auth::user();
        $userIds = Auth::user()->master_id;
        $trans = User::where('master_id', $userIds)->get();
        $statess = State::where('is_active', 1)->get();
        $lang = Language::where('is_active', 1)->get();
        $location = Location::all();
        $HOS = Config::get('app.HOS');
        $EDSH = Config::get('app.EDSH');
        $UE = Config::get('app.UE');
        $countries = Country::with([
            'states' => function ($query) {
                $query->where('is_active', 1)->with([
                    'cities' => function ($query) {
                        $query->where('is_active', 1);
                    },
                ]);
            },
        ])
            ->where('is_active', 1)
            ->get();

        $timezones = Timezone::where('status', 1)->get();

        $user = User::find($id);
        $userInfo = UserInfo::where('user_id', $id)->first();

        return view('transport.driver.edit', compact('user', 'timezones', 'countries', 'userInfo', 'statess', 'lang', 'trans', 'HOS', 'EDSH', 'UE', 'location'));

    }

    public function post(Request $request, $lang, $id)
    {
        if (empty($lang)) {
            return redirect()->route('transport.dashboard', ['en']);
        }
        $languag = Language::where('Short_name', $lang)->first();
        if (!$languag) {
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['en']);
        } else {
            App::setLocale($lang);
        }

        $is_master = $request->session()->get('is_master');

        $user = User::find($id);

        if ($request->hasFile('file')) {
            $imageName = time() . '.' . $request->file->extension();
            $request->file->move(public_path('driverss'), $imageName);
        } else {
            $imageName = $user->avatar_image; // Retain the previous image if no new image is provided
        }

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->user_type = 'U';
        $user->language_id = $request->language_id;
        $user->avatar_image = $imageName;
        $user->email = $request->email;
        $user->password = $request->password ? bcrypt($request->password) : $user->password; // Check if password provided, if not, retain the previous one
        $user->mobile_no = $request->mobile_no;
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
            $user->master_id = $request->session()->get('ids');
            $user->master_company_id = $request->session()->get('master_company_id');
        } else {
            $user->master_company_id = $id;
            $user->master_id = $id;
        }
        $user->save();
        $userInfos = UserInfo::where('user_id', $id)->first();
        $userInfos->user_id = $user->id;
        $userInfos->licenseNumber = $request->driver_license_number;
        $userInfos->username = $request->username;
        $userInfos->note = $request->note;
        $userInfos->driver_license_state = $request->driver_license_state;
        $userInfos->hour_of_service = $request->hour_of_service;
        $userInfos->eld_day_start_hour = $request->eld_day_start_hour;
        $userInfos->home_terminal_timezone = $request->home_terminal_timezone;
        $userInfos->career_name = $request->career_name;
        $userInfos->main_office_address = $request->main_office_address;
        $userInfos->carrer_us_dot_number = $request->carrer_us_dot_number;
        $userInfos->home_terminal_name = $request->home_terminal_name;
        $userInfos->home_terminal_address = $request->home_terminal_address;
        $userInfos->peer_group_tag = $request->peer_group_tag;
        $userInfos->vechile_selection_tag = $request->vechile_selection_tag;
        $userInfos->trailor_selection_tag = $request->trailor_selection_tag;
        $userInfos->code_card = $request->id_card_code;
        $userInfos->tachograph_card = $request->tachograph_card;
        $userInfos->driver_status = $request->driver_status;
        $userInfos->driver_attribute = $request->driver_attribute;
        $userInfos->us_short_haul_exemption = $request->us_short_haul_exemption;
        $userInfos->driver_id = $request->driver_id;
        $userInfos->driver_ruleset_cycle = $request->driver_ruleset_cycle;
        $userInfos->save();

        $user = User::where('user_type', 'SA')->first();
        $message = "New driver has been edited " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('transport.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        $request->session()->flash('success', __('lang.saved'));

        return redirect()->route('driver.index', [$lang]);

    }

    public function data_log(Request $request, $lang)
    {
        if (empty($lang)) {
            return redirect()->route('transport.dashboard', ['en']);
        }

        $languag = Language::where('Short_name', $lang)->first();
        if (!$languag) {
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['en']);
        } else {
            App::setLocale($lang);
        }

        // Retrieve data from ApiLogger model
        $data = ApiLogger::orderBy('service_call_date', 'desc')
            ->paginate(10);

        // Check if data is retrieved
        if ($data->isEmpty()) {
            // Data not found, handle accordingly (e.g., redirect, display message)
            return redirect()->back()->with('error', 'No data found for the specified IP address.');
        }

        // Data found, pass it to the view
        return view('transport.report.index', compact('data'));
    }

    public function report_vechile(Request $request, $lang)
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

        $data = VehicleLogHistory::latest('event_date_time')->first();

        if (!$data) {
            // Handle the case where no data is found, for example, return an error response
            return response()->json(['error' => 'No data found'], 404);
        }

        $locations = [];
        $location = json_decode($data['location'], true) ?? null;
        $fuelData = $data['obd_coolant'] ?? null;
        $ignition = $data['operating_states'] ?? null;
        $eventDateTime = $data['event_date_time'] ?? null;
        $speed = $data['speed'] ?? null;
        $ignitionData = json_decode($ignition, true);
        $ignitionOnIds = $ignitionData[0]['Id'];
        // Check if $data['request_json'] is a JSON string
        if (is_array($location)) {

            $geoData = $location;

            if ($geoData) {
                $latitude = $geoData['GeoLocation']['Latitude'] ?? null;
                $longitude = $geoData['GeoLocation']['Longitude'] ?? null;
                if ($latitude && $longitude && is_numeric($latitude) && is_numeric($longitude)) {
                    // Generate a cache key based on latitude and longitude
                    $cacheKey = "location_{$latitude}_{$longitude}";

                    // Check if the location name is already cached
                    if (Cache::has($cacheKey)) {
                        $locationName = Cache::get($cacheKey);
                    } else {
                        // Make request to OpenStreetMap Nominatim API to get location name
                        $response = Http::get('https://nominatim.openstreetmap.org/reverse', [
                            'format' => 'json',
                            'lat' => $latitude,
                            'lon' => $longitude,
                        ]);

                        // Check if request was successful
                        if ($response->successful()) {
                            $data = $response->json();
                            $locationName = $data['display_name'] ?? 'Unknown';
                            // Cache the location name for future requests
                            Cache::put($cacheKey, $locationName, now()->addDays(30));
                        } else {
                            $locationName = 'Unknown';
                        }
                    }

                    $locations[] = ['latitude' => $latitude, 'longitude' => $longitude, 'name' => $locationName];
                }
            }

        }

        // Pass the $ignitionOnIds array to the view if needed
        return view('transport.report.vechile', compact('locations', 'fuelData', 'data', 'ignitionOnIds', 'eventDateTime', 'speed'));
    }

    public function enviorement_data(Request $request, $lang)
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

        // Fetch data from ApiLogger
        $data = VehicleLogHistory::latest('event_date_time')->first();

        $jsonData = $data;

        $locations = [];

        // $jsonData = json_decode($data, true);

        // Check if 'OBDCoolant' key exists in the JSON data
        if (isset($jsonData['obd_fuel'])) {
            // If the key exists, assign its value to $obdCoolant
            $obdCoolant = $jsonData['obd_fuel'];

            // Convert Celsius to Fahrenheit
            $obdCoolant = ($obdCoolant * 9 / 5) + 32;

        } else {
            // If the key doesn't exist, assign a default value
            $obdCoolant = 'Data not received';
        }

        $geoLocation = $jsonData['location'] ?? null;

        $geoData = json_decode($geoLocation, true);

        if ($geoData && isset($geoData['GeoLocation'])) {
            $latitude = $geoData['GeoLocation']['Latitude'] ?? null;
            $longitude = $geoData['GeoLocation']['Longitude'] ?? null;
            if ($latitude && $longitude && is_numeric($latitude) && is_numeric($longitude)) {
                // Generate a cache key based on latitude and longitude
                $cacheKey = "location_{$latitude}_{$longitude}";

                // Check if the location name is already cached
                if (Cache::has($cacheKey)) {
                    $locationName = Cache::get($cacheKey);
                } else {
                    // Make request to OpenStreetMap Nominatim API to get location name
                    $response = Http::get('https://nominatim.openstreetmap.org/reverse', [
                        'format' => 'json',
                        'lat' => $latitude,
                        'lon' => $longitude,
                    ]);

                    // Check if request was successful
                    if ($response->successful()) {
                        $data = $response->json();
                        $locationName = $data['display_name'] ?? 'Unknown';
                        // Cache the location name for future requests
                        Cache::put($cacheKey, $locationName, now()->addDays(30));
                    } else {
                        $locationName = 'Unknown';
                    }
                }

                $locations[] = ['latitude' => $latitude, 'longitude' => $longitude, 'name' => $locationName];
            }
        }

        return view('transport.enviorement.index', compact('data', 'obdCoolant', 'locations'));

    }

    public function driver_organisation(Request $request, $lang)
    {
        if (empty($lang)) {
            return redirect()->route('transport.dashboard', ['lang' => 'en']);
        }

        $language = Language::where('Short_name', $lang)->first();

        if (!$language) {
            App::setLocale('en');
            return redirect()->route('transport.dashboard', ['lang' => 'en']);
        } else {
            App::setLocale($lang);
        }

        // Ensure the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login'); // Or any appropriate route
        }

        // Fetch all vehicle assignments
        $assignments = VehicleAssign::get();

        // If there are no assignments, handle the case
        if ($assignments->isEmpty()) {
            return view('transport.settings.organization.driver.driver', [
                'data' => null,
                'drivers' => null,
                'vehicles' => null,
            ]);
        }

        // Fetch all related drivers and vehicles
        $driverIds = $assignments->pluck('driver_id')->unique();
        $vehicleIds = $assignments->pluck('vechile_id')->unique();

        $drivers = User::whereIn('id', $driverIds)->where('user_type', 'U')->get();
        $vehicles = Vehicle::whereIn('id', $vehicleIds)->get();

        // Organize data
        $data = [
            'assignments' => $assignments,
            'drivers' => $drivers,
            'vehicles' => $vehicles,
        ];

        return view('transport.settings.organization.driver.driver', compact('data'));
    }

    public function driver_organisation_add(Request $request, $lang)
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

        $driver = User::where('user_type', 'U')->where('master_id', Auth::user()->id)->where('is_active', 1)->get();

        $vechile = Vehicle::all();

        return view('transport.settings.organization.driver.add', compact('driver', 'vechile'));

    }

    public function driver_organisation_edit(Request $request, $lang, $id)
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

        $data = VehicleAssign::find($id);

        if (!$data) {
            $request->session()->flash('error', 'Wrong user');
        }

        $driver = User::where('user_type', 'U')->where('master_id', Auth::user()->id)->where('is_active', 1)->get();

        $vechile = Vehicle::all();

        return view('transport.settings.organization.driver.edit', compact('data', 'driver', 'vechile'));

    }

    public function add_post(Request $request, $lang)
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

        $rules = [
            'driver_id' => 'required',
            'vechile_id' => [
                'required',
                Rule::unique('vechile_assign')->where(function ($query) use ($request) {
                    return $query->where('driver_id', $request->driver_id)
                        ->where('vechile_id', $request->vechile_id);
                }),
            ],
            'is_active' => 'required|boolean',
        ];

        $customMessages = [
            'vechile_id.unique' => 'This vehicle has been already assigned to this driver.',
        ];

        $this->validate($request, $rules, $customMessages);

        $vechile_assign = new VehicleAssign();

        $vechile_assign->driver_id = $request->driver_id;
        $vechile_assign->vechile_id = $request->vechile_id;
        $vechile_assign->is_active = $request->is_active;
        $vechile_assign->created_by = Auth::user()->id;

        $vechile_assign->save();

        $request->session()->flash('success', 'Successfully saved');

        return redirect()->route('setting.driver.organisation', [$lang]);
    }

    public function edit_post(Request $request, $lang, $id)
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

        $vechile_assign = VehicleAssign::find($id);

        if (!$vechile_assign) {
            return redirect()->route('transport.dashboard', [$lang])->withErrors(['error' => 'Vehicle assignment not found.']);
        }

        $rules = [
            'driver_id' => 'required',
            'vechile_id' => [
                'required',
                Rule::unique('vechile_assign')->where(function ($query) use ($request, $vechile_assign) {
                    return $query->where('driver_id', $request->driver_id)
                        ->where('vechile_id', $request->vechile_id)
                        ->where('id', '!=', $vechile_assign->id);
                }),
            ],
            'is_active' => 'required|boolean',
        ];

        $customMessages = [
            'vechile_id.unique' => 'This vehicle has been already assigned to this driver.',
        ];

        $this->validate($request, $rules, $customMessages);

        $vechile_assign->driver_id = $request->driver_id;
        $vechile_assign->vechile_id = $request->vechile_id;
        $vechile_assign->is_active = $request->is_active;
        $vechile_assign->updated_by = Auth::user()->id;

        $vechile_assign->save();

        $request->session()->flash('success', 'Successfully updated');

        return redirect()->route('setting.driver.organisation', [$lang]);
    }


    //Begins for tree



    // End for tree


    public function driver_detail(Request $request, $lang, $id)
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

        $logDetails = [];

        $shiftTime = 0;

        $cycleTime = 0;

        $driveTime = 0;

        $breakTime = 0;

        $shiftViolTime = 0;

        $cycleViolTime = 0;

        $driveViolTime = 0;

        $breakViolTime = 0;

        $count = 0;

        $aboveDrTime = null;

        $countBreak = 0;

        $ViolShift = null;

        $violCycleTime = null;

        $violDriveTime = null;

        $violBreakTime = null;

        $ruleAssgn = RuleAssign::where('user_id', $id)->get();

        if ($id) {

            $rowTime = null;

            $aboveTime = null;

            $user = User::where('user_type', 'U')->where('id', $id)->first();

            $userInfo = UserInfo::where('user_id', $id)->first();

            $timeZone = $userInfo->home_terminal_timezone;

            //Current time of today
            $currTime = Carbon::now()->setTimezone($timeZone);

            $currentTime = conTimezone($timeZone, $currTime);

            $userId = $user->id;

            if ($user) {

                if ($user->master_id == Auth::user()->id) {

                    $vehicleLog = DriverShiftLog::where('driver_id', $userId)->latest()->first();

                    if ($vehicleLog) {

                        $vehicleId = $vehicleLog->vehicle_id;

                        $device = Device::where('vehicle_id', $vehicleId)->first();

                        $startCycleId = DriverShiftLog::where('driver_id', $userId)->where('vehicle_id', $vehicleId)->where('cycle_start', 1)->latest()->pluck('id')->first();

                        $startShiftId = DriverShiftLog::where('driver_id', $userId)->where('vehicle_id', $vehicleId)->where('shift_start', 1)->latest()->pluck('id')->first();

                        $endId = DriverShiftLog::where('driver_id', $userId)->where('vehicle_id', $vehicleId)->latest()->pluck('id')->first();

                        $ShiftLogs = DriverShiftLog::whereBetween('id', [$startShiftId, $endId])->get();

                        $cycleLogs = DriverShiftLog::whereBetween('id', [$startCycleId, $endId])->get();

                        $driverLog = DriverShiftLog::whereBetween('id', [$startShiftId, $endId])->where('current_shift_status', 3)->get();

                        if ($ShiftLogs) {

                            foreach ($ShiftLogs as $data) {

                                $rowTime = $data->created_at;

                                $rowId = $data->id;

                                $rowStatus = $data->current_shift_status;

                                $aboveRow = DriverShiftLog::where('id', '>', $rowId)
                                    ->where('driver_id', $userId)
                                    ->orderBy('id', 'asc')
                                    ->first();

                                if ($aboveRow) {

                                    $aboveTime = $aboveRow->created_at;

                                } else {

                                    if ($rowStatus == 1 || $rowStatus == 2 || $rowStatus == 5) {

                                        $aboveTime = $rowTime;

                                    } else {
                                        $aboveTime = Carbon::parse($currentTime);
                                    }

                                }

                                $timeInSeconds = $aboveTime->diffInSeconds($rowTime);

                                $shiftTime += $timeInSeconds;

                            }

                        }

                        if ($cycleLogs) {

                            foreach ($cycleLogs as $data) {

                                $rowTime = $data->created_at;

                                $rowId = $data->id;

                                $rowStatus = $data->current_shift_status;

                                $aboveRow = DriverShiftLog::where('id', '>', $rowId)
                                    ->where('driver_id', $userId)
                                    ->orderBy('id', 'asc')
                                    ->first();

                                if ($aboveRow) {

                                    $aboveTime = $aboveRow->created_at;

                                } else {

                                    if ($rowStatus == 1 || $rowStatus == 2 || $rowStatus == 5) {

                                        $aboveTime = $rowTime;

                                    } else {
                                        $aboveTime = Carbon::parse($currentTime);
                                    }

                                }

                                $timeInSeconds = $aboveTime->diffInSeconds($rowTime);

                                $cycleTime += $timeInSeconds;

                            }

                        }

                        if ($driverLog) {

                            foreach ($driverLog as $data) {

                                $rowTime = $data->created_at;

                                $rowId = $data->id;

                                $rowStatus = $data->current_shift_status;

                                $aboveRow = DriverShiftLog::where('id', '>', $rowId)
                                    ->where('driver_id', $userId)
                                    ->orderBy('id', 'asc')
                                    ->first();

                                if ($aboveRow) {

                                    $aboveTime = $aboveRow->created_at;

                                } else {

                                    if ($rowStatus == 1 || $rowStatus == 2 || $rowStatus == 5) {

                                        $aboveTime = $rowTime;

                                    } else {
                                        $aboveTime = Carbon::parse($currentTime);
                                    }

                                }


                                if ($device) {

                                    $vehicleLog = VehicleLogHistory::where('identifier', $device->serial_number)
                                        ->whereBetween('event_date_time', [$rowTime, $aboveTime])
                                        ->whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])
                                        ->get();

                                    if ($vehicleLog) {

                                        foreach ($vehicleLog as $data) {

                                            $rowDriveTime = $data->created_at;

                                            $rowDriveId = $data->id;

                                            $aboveDriveRow = VehicleLogHistory::where('id', '>', $rowDriveId)
                                                ->where('identifier', $device->serial_number)
                                                ->orderBy('id', 'asc')
                                                ->first();

                                            if ($aboveDriveRow) {

                                                $aboveVehicleTime = $aboveDriveRow->created_at;

                                            } else {

                                                $aboveVehicleTime = $rowDriveTime;

                                            }

                                            $timeInSeconds = $aboveVehicleTime->diffInSeconds($rowDriveTime);

                                            $driveTime += $timeInSeconds;

                                        }

                                    }

                                }

                            }

                        }

                        if ($ruleAssgn) {

                            foreach ($ruleAssgn as $data) {

                                $rule = Rules::find($data->rule_id);

                                if ($rule->reason == 4) {

                                    $maxDriveHr = $rule->max_hour_limit;

                                    $maxDriveSec = $maxDriveHr * 3600;

                                    $maxBreakHr = $rule->max_break_minute;

                                    $maxBreakSec = $maxBreakHr * 60;

                                    // return [$maxDriveSec, $maxBreakSec];

                                    if ($driverLog) {

                                        $firstDriver = $driverLog->first();
                                        
                                        return $driverLog;

                                        $firstTime = $firstDriver->created_at;

                                        $lastDriver = $driverLog->last();

                                        $firstId = $firstDriver->id;

                                        $lastId = $lastDriver->id;

                                        foreach ($driverLog as $data) {

                                            $rowTime = $data->created_at;

                                            $rowId = $data->id;

                                            $rowStatus = $data->current_shift_status;

                                            $aboveRow = DriverShiftLog::where('id', '>', $rowId)
                                                ->where('driver_id', $userId)
                                                ->orderBy('id', 'asc')
                                                ->first();

                                            if ($aboveRow) {

                                                $aboveTime = $aboveRow->created_at;

                                            } else {

                                                if ($rowStatus == 1 || $rowStatus == 2 || $rowStatus == 5) {

                                                    $aboveTime = $rowTime;

                                                } else {
                                                    $aboveTime = Carbon::parse($currentTime);
                                                }

                                            }


                                            if ($device) {

                                                $vehicleLog = VehicleLogHistory::where('identifier', $device->serial_number)
                                                    ->whereBetween('event_date_time', [$rowTime, $aboveTime])
                                                    ->whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])
                                                    ->get();

                                                if ($countBreak == 0) {

                                                    if ($vehicleLog) {

                                                        foreach ($vehicleLog as $data) {

                                                            $rowDriveTime = $data->created_at;

                                                            $rowDriveId = $data->id;

                                                            $aboveDriveRow = VehicleLogHistory::where('id', '>', $rowDriveId)
                                                                ->where('identifier', $device->serial_number)
                                                                ->orderBy('id', 'asc')
                                                                ->first();

                                                            if ($aboveDriveRow) {

                                                                $aboveVehicleTime = $aboveDriveRow->created_at;

                                                            } else {

                                                                $aboveVehicleTime = $rowDriveTime;

                                                            }

                                                            $timeInSeconds = $aboveVehicleTime->diffInSeconds($rowDriveTime);

                                                            $breakTime += $timeInSeconds;

                                                            if ($breakTime >= $maxDriveSec) {

                                                                $onRow = DriverShiftLog::where('driver_id', $userId)
                                                                    ->where('vehicle_id', $vehicleId)
                                                                    ->whereBetween('id', [$startShiftId, $firstId])
                                                                    ->where('current_shift_status', 4)
                                                                    ->get();


                                                                if (count($onRow) > 0) {

                                                                    foreach ($onRow as $value) {

                                                                        $onRowId = $value->id;

                                                                        $rowONTime = $value->created_at;

                                                                        $aboveONRow = DriverShiftLog::where('id', '>', $onRowId)
                                                                            ->where('driver_id', $userId)
                                                                            ->orderBy('id', 'asc')
                                                                            ->first();

                                                                        if ($aboveONRow) {
                                                                            $aboveOnTime = $aboveONRow->created_at;
                                                                        } else {
                                                                            $aboveOnTime = Carbon::parse($currentTime);
                                                                        }

                                                                        $breakInsec = $aboveOnTime->diffInSeconds($rowONTime);

                                                                        if ($breakInsec < $maxBreakSec) {

                                                                            $countBreak = 1;

                                                                            $aboveDrRow = DriverShiftLog::where('id', '>', $lastId)
                                                                                ->where('driver_id', $userId)
                                                                                ->orderBy('id', 'asc')
                                                                                ->first();

                                                                            if ($aboveDrRow) {

                                                                                $aboveDrTime = $aboveDrRow->created_at;
                                                                            } else {
                                                                                $aboveDrTime = Carbon::parse($currentTime);
                                                                            }

                                                                            $breakViolTime = $aboveDrTime->diffInSeconds($rowONTime);

                                                                        }

                                                                    }

                                                                } else {

                                                                    $countBreak = 1;

                                                                    $aboveDrRow = DriverShiftLog::where('id', '>', $lastId)
                                                                        ->where('driver_id', $userId)
                                                                        ->orderBy('id', 'asc')
                                                                        ->first();

                                                                    if ($aboveDrRow) {
                                                                        $aboveDrTime = $aboveDrRow->created_at;
                                                                    } else {
                                                                        $aboveDrTime = Carbon::parse($currentTime);
                                                                    }
return $aboveDrTime;
                                                                    $breakViolTime = $aboveDrTime->diffInSeconds($rowONTime);

                                                                }

                                                            }

                                                        }

                                                    }

                                                }

                                            }

                                        }

                                    }

                                }

                            }

                        }

                    }

                } else {

                    $request->session()->flash('error', 'Unauthorized access');

                    return redirect(route('driver.index', [request()->lang]));

                }

            }

        }

        if ($ruleAssgn) {

            foreach ($ruleAssgn as $data) {

                $rule = Rules::find($data->rule_id);

                if ($rule->reason == 1) {

                    $maxHr = $rule->max_hour_limit;

                    $maxShiftSec = $maxHr * 3600;

                    if ($maxShiftSec > $shiftTime) {

                        $shiftViolTime = $maxShiftSec - $shiftTime;

                        $ViolShift = secondsToTime($shiftViolTime);

                    } else {
                        $ViolShift = '00h 00min';
                    }

                } elseif ($rule->reason == 5) {

                    $maxHr = $rule->max_hour_limit;

                    $maxCycleSec = $maxHr * 3600;

                    if ($maxCycleSec) {

                        $violCycleTime = $maxCycleSec - $cycleTime;

                        $violCycleTime = secondsToTime($violCycleTime);

                    } else {
                        $violCycleTime = '00:00:00';
                    }

                } elseif ($rule->reason == 2) {

                    $maxHr = $rule->max_hour_limit;

                    $maxCycleSec = $maxHr * 3600;

                    if ($maxCycleSec) {

                        $violCycleTime = $maxCycleSec - $cycleTime;

                        $violCycleTime = secondsToTime($violCycleTime);

                    } else {
                        $violCycleTime = '00h 00min';
                    }

                } elseif ($rule->reason == 3) {

                    $maxHr = $rule->max_hour_limit;

                    $maxDriveSec = $maxHr * 3600;

                    if ($maxDriveSec) {

                        $violDriveTime = $maxDriveSec - $cycleTime;

                        $violDriveTime = secondsToTime($violDriveTime);

                    } else {

                        $violDriveTime = '00:00:00';

                    }

                }

            }

        }

        $violBreakTime = secondsToTime($breakViolTime);

        return [$ViolShift, $violCycleTime, $violDriveTime, $violBreakTime];

        return redirect(route('driver.driver_detail', [$lang]));

    }
    public function data_date(Request $request)
    {
        $logID = $request->input('logId');

        $id = $request->input('driver_id');

        if ($id && $logID) {

            $vehicleAssign = VehicleAssign::where('driver_id', $id)->get();
            $startDate = Carbon::now();
            $date = Carbon::createFromFormat('l, M jS', $logID)->format('Y-m-d');
            $totalTime = 0;
            $data = [];

            if ($vehicleAssign) {
                foreach ($vehicleAssign as $value) {
                    $logs = DriverShiftLog::where('driver_id', $id)
                        ->where('vehicle_id', $value->vechile_id)
                        ->whereDate('created_at', $date)
                        ->get();

                    if ($logs) {
                        foreach ($logs as $log) {
                            $rowTime = $log->created_at;

                            if ($log) {
                                $aboveRow = DriverShiftLog::where('id', '>', $log->id)
                                    ->where('driver_id', $log->driver_id)
                                    ->where('vehicle_id', $value->vechile_id)
                                    ->orderBy('id', 'asc')
                                    ->first();

                                if ($aboveRow) {
                                    $aboveTime = $aboveRow->created_at;

                                    if ($log->current_shift_status == 3) {
                                        $device = Device::where('vehicle_id', $value->vechile_id)->first();

                                        if ($device) {
                                            $LogData = VehicleLogHistory::where('device_id', $device->id)
                                                ->whereBetween('created_at', [$rowTime, $aboveTime])
                                                ->whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])
                                                ->get();

                                            foreach ($LogData as $logValue) {
                                                $aboveData = VehicleLogHistory::where('id', '>', $logValue->id)
                                                    ->where('device_id', $device->id)
                                                    ->orderBy('id', 'asc')
                                                    ->first();

                                                if ($aboveData) {
                                                    $aboveDataTime = $aboveData->created_at;
                                                    $timeInSeconds = $aboveDataTime->diffInSeconds($logValue->created_at);
                                                } else {
                                                    $timeInSeconds = $startDate->diffInSeconds($logValue->created_at);
                                                }

                                                $formattedTime = $logValue->created_at->format('h:i A T');
                                                $hours = intdiv($timeInSeconds, 3600);
                                                $minutes = ($timeInSeconds % 3600) / 60;

                                                // Format the minutes to two decimal places
                                                $formattedMinutes = sprintf('%02d', ((int) $minutes));

                                                $data[] = [
                                                    'status' => $log->current_shift_status,
                                                    'created' => $formattedTime,
                                                    'time' => "{$hours}h {$formattedMinutes}m",
                                                ];
                                            }
                                        }
                                    } else {
                                        $timeInSeconds = $aboveTime->diffInSeconds($rowTime);
                                        $formattedTime = $rowTime->format('h:i A T');
                                        $hours = intdiv($timeInSeconds, 3600);
                                        $minutes = ($timeInSeconds % 3600) / 60;

                                        // Format the minutes to two decimal places
                                        $formattedMinutes = sprintf('%02d', ((int) $minutes));

                                        $data[] = [
                                            'status' => $log->current_shift_status,
                                            'created' => $formattedTime,
                                            'time' => "{$hours}h {$formattedMinutes}m",
                                        ];
                                    }
                                } else {
                                    if ($log->current_shift_status == 3) {
                                        $device = Device::where('vehicle_id', $value->vechile_id)->first();

                                        if ($device) {
                                            $LogData = VehicleLogHistory::where('device_id', $device->id)
                                                ->whereBetween('created_at', [$startDate, $rowTime])
                                                ->whereJsonContains('operating_states', [['Id' => 'IgnitionOn']])
                                                ->get();

                                            foreach ($LogData as $logValue) {
                                                $aboveData = VehicleLogHistory::where('id', '>', $logValue->id)
                                                    ->where('device_id', $device->id)
                                                    ->orderBy('id', 'asc')
                                                    ->first();

                                                if ($aboveData) {
                                                    $aboveDataTime = $aboveData->created_at;
                                                    $timeInSeconds = $aboveDataTime->diffInSeconds($logValue->created_at);
                                                } else {
                                                    $timeInSeconds = $startDate->diffInSeconds($logValue->created_at);
                                                }

                                                $formattedTime = $logValue->created_at->format('h:i A T');
                                                $hours = intdiv($timeInSeconds, 3600);
                                                $minutes = ($timeInSeconds % 3600) / 60;

                                                // Format the minutes to two decimal places
                                                $formattedMinutes = sprintf('%02d', ((int) $minutes));

                                                $data[] = [
                                                    'status' => $log->current_shift_status,
                                                    'created' => $formattedTime,
                                                    'time' => "{$hours}h {$formattedMinutes}m",
                                                ];
                                            }
                                        }
                                    } else {
                                        $timeInSeconds = $startDate->diffInSeconds($rowTime);
                                        $formattedTime = $rowTime->format('h:i A T');
                                        $hours = intdiv($timeInSeconds, 3600);
                                        $minutes = ($timeInSeconds % 3600) / 60;

                                        // Format the minutes to two decimal places
                                        $formattedMinutes = sprintf('%02d', ((int) $minutes));

                                        $data[] = [
                                            'status' => $log->current_shift_status,
                                            'created' => $formattedTime,
                                            'time' => "{$hours}h {$formattedMinutes}m",
                                        ];

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Return the log data as JSON
        return response()->json($data);

    }

    public function generate_username(Request $request)
    {
        $firstName = $request->first_name;
        $lastName = $request->last_name;

        // Generate a unique username
        $baseUsername = Str::slug($firstName . $lastName); // You can adjust the format as needed
        $username = UserInfo::generateUniqueUsername($baseUsername);


        return response()->json(['username' => $username]);

    }

    public function check_username(Request $request)
    {

        $username = $request->input('username');
        $isUnique = UserInfo::where('username', $username)->doesntExist();

        return response()->json(['unique' => $isUnique]);
    }

    public function editUsername(Request $request)
    {
        $username = $request->input('username');
        $currentUsername = $request->input('current_username');

        // Check if the username is unique except for the current user's username
        $isUnique = !UserInfo::where('username', $username)
            ->where('username', '!=', $currentUsername)
            ->exists();

        return response()->json(['unique' => $isUnique]);
    }

}
