<?php

namespace App\Http\Controllers\SuperAdmin;



use App\Http\Controllers\Controller;

use App\Models\CoDriver;

use App\Models\Country;

use App\Models\Role;

use App\Models\User;

use App\Models\UserInfo;

use App\Models\ListOption;

use App\Models\Vehicle;

use App\Models\VehicleAssign;

use App\Models\VehicleLogHistory;

use Carbon\Carbon;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Http;



class AdminProfileController extends Controller

{

    public function index(Request $request)

    {



        $user = User::where('user_type', "SA")->first();



        $request->session()->put('original_user_id', auth()->id());

        $request->session()->put('ids', $user->id);

        $request->session()->put('ut', 1);

        $request->session()->put('first', $user->first_name);

        $request->session()->put('last', $user->last_name);

        $request->session()->put('email', $user->email);

        $request->session()->put('avatar', $user->avatar_image);

        $request->session()->put('master_company_id', $user->master_company_id);

        $request->session()->put('master_id', $user->master_id);

        $request->session()->put('is_master', $user->is_master);



        $roles = Role::all();



        $usersCount = User::where('user_type', 'WC')->count();

        $rolesCount = $roles->count();



        return view('super-admin.profile.index', compact('user', 'roles', 'rolesCount', 'usersCount'));
    }



    public function dashboard(Request $request)

    {



        $user = User::where('user_type', "SA")->first();



        $request->session()->put('original_user_id', auth()->id());

        $request->session()->put('ids', $user->id);

        $request->session()->put('ut', 1);

        $request->session()->put('first', $user->first_name);

        $request->session()->put('last', $user->last_name);

        $request->session()->put('email', $user->email);

        $request->session()->put('avatar', $user->avatar_image);

        $request->session()->put('master_company_id', $user->master_company_id);

        $request->session()->put('master_id', $user->master_id);

        $request->session()->put('is_master', $user->is_master);



        $data['wcCount'] = User::where('user_type', 'WC')->count();

        $data['rsCount'] = User::where('user_type', 'RS')->count();

        $data['ecCount'] = User::where('user_type', 'EC')->count();

        $data['trCount'] = User::where('user_type', 'TR')->count();



        $rsIds             = User::where('user_type', 'RS')->pluck('id');

        $data['userCount'] = User::where('user_type', 'U')->whereIn('master_id', $rsIds)->count();



        $trIds               = User::where('user_type', 'TR')->pluck('id');

        $data['driverCount'] = User::where('user_type', 'U')->whereIn('master_id', $trIds)->count();



        return view('super-admin.dashboard', $data);
    }



    public function edit(Request $request)

    {

        $user = User::where('user_type', "SA")->first();



        $request->session()->put('original_user_id', auth()->id());

        $request->session()->put('ids', $user->id);

        $request->session()->put('ut', 1);

        $request->session()->put('first', $user->first_name);

        $request->session()->put('last', $user->last_name);

        $request->session()->put('email', $user->email);

        $request->session()->put('avatar', $user->avatar_image);

        $request->session()->put('master_company_id', $user->master_company_id);

        $request->session()->put('master_id', $user->master_id);

        $request->session()->put('is_master', $user->is_master);

        $user = User::where('user_type', "SA")->first();

        $request->session()->put('original_user_id', auth()->id());

        $request->session()->put('ids', $user->id);

        $request->session()->put('ut', 1);

        $request->session()->put('first', $user->first_name);

        $request->session()->put('last', $user->last_name);

        $request->session()->put('email', $user->email);

        $request->session()->put('avatar', $user->avatar_image);

        $request->session()->put('master_company_id', $user->master_company_id);

        $request->session()->put('master_id', $user->master_id);

        $request->session()->put('is_master', $user->is_master);



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



        $timezones = [];



        foreach (timezone_identifiers_list() as $timezone) {

            $dt                   = new \DateTime('now', new \DateTimeZone($timezone)); // Use \DateTime and \DateTimeZone without namespace

            $offset               = $dt->getOffset() / 3600;

            $offsetString         = ($offset >= 0 ? '+' : '-') . sprintf('%02d', abs($offset)) . ':00';

            $timezones[$timezone] = "(GMT$offsetString) " . str_replace('_', ' ', $timezone);
        }



        return view('super-admin.profile.edit', compact('user', 'countries', 'timezones'));
    }



    public function post(Request $request, $id)

    {

        // Validation rules

        $rules = [

            'comp_name'  => 'required|string',

            'first_name' => 'required|string',

            'last_name'  => 'required|string',

            'mobile_no'  => 'required|string', // Adding the size rule to enforce 10 digits

            'country_id' => 'required|integer',

            'state_id'   => 'required|integer',

            'city_id'    => 'required|integer',

            'timezone'   => 'required|string',

            'address'    => 'required|string',

        ];



        // Validate request

        $request->validate($rules);



        // Find the user

        $user = User::find($id);



        if ($request->hasFile('file')) {



            $imageName = time() . '.' . $request->file->extension();

            $request->file->move(public_path('admin'), $imageName);
        } else {



            $imageName = $user->avatar_image; // Retain the previous image if no new image is provided



        }



        $user->update([

            'first_name'        => $request->first_name,

            'last_name'         => $request->last_name,

            'user_type'         => 'SA',

            'comp_name'         => $request->comp_name,

            'avatar_image'      => $imageName,

            'email'             => $request->email,

            'mobile_no'         => $request->mobile_no,

            'landline_no'       => $request->landline_no,

            'country_id'        => $request->country_id,

            'timezone'          => $request->timezone,

            'state_id'          => $request->state_id,

            'city_id'           => $request->city_id,

            'pin_code'          => $request->pincode,

            'is_active'         => $request->is_active,

            'address'           => $request->address,

            'is_master'         => 1,

            'master_company_id' => $id,

            'master_id'         => $id,

        ]);



        session()->put('ids', $user->id);

        session()->put('first', $user->first_name);

        session()->put('last', $user->last_name);

        session()->put('email', $user->email);

        session()->put('avatar', $user->avatar_image);

        session()->put('master_company_id', $user->master_company_id);

        session()->put('master_id', $user->master_id);

        session()->put('is_master', $user->is_master);



        return redirect()->route('admin.profile.index');
    }



    public function changeUser(Request $request, $ut, $id, User $user)

    {



        $user = User::findOrFail($id);

        if (! $user) {

            return redirect()->back();
        }

        $ut = $user->user_type;



        $request->session()->put('original_user_id', auth()->id());

        $request->session()->put('ids', $user->id);

        $request->session()->put('ut', 1);

        $request->session()->put('first', $user->first_name);

        $request->session()->put('last', $user->last_name);

        $request->session()->put('email', $user->email);

        $request->session()->put('avatar', $user->avatar_image);

        $request->session()->put('master_company_id', $user->master_company_id);

        $request->session()->put('master_id', $user->master_id);

        $request->session()->put('is_master', $user->is_master);



        Auth::login($user);



        if ($ut == "SA") {



            return redirect()->route('admin.dashboard');
        } elseif ($ut == "WC") {



            return redirect()->route('white-label.dashboard');
        } elseif ($ut == "RS") {



            return redirect()->route('reseller.dashboard');
        } elseif ($ut == "EC") {

            $request->session()->put('chUsers', true);

            return redirect()->route('company.dashboard');
        } elseif ($ut == "TR") {



            return redirect()->route('transport.dashboard');
        } elseif ($ut == "U") {



            return redirect()->back();
        }
    }



    public function view_total(Request $request, $ut)

    {

        $user    = [];

        $no_comp = 0;



        switch ($ut) {

            case 'white-label':

                $user = User::where('user_type', 'WC')->get();

                break;



            case 'reseller':

                $user = User::where('user_type', 'RS')->get();

                break;



            case 'company':

                $user = User::where('user_type', 'EC')->get();

                break;



            case 'transport':

                $user = User::where('user_type', 'TR')->get();

                break;



            case 'user':



                $rs_user_ids = User::where('user_type', 'RS')->pluck('id');



                $matching_users = User::where('user_type', 'U')->whereIn('master_id', $rs_user_ids)->get();



                $user = $matching_users->all();



                break;



            case 'driver':

                $no_comp = 1;



                $tr_users = User::where('user_type', 'TR')->pluck('id');



                $matching_users = User::where('user_type', 'U')->whereIn('master_id', $tr_users)->get();



                $user = $matching_users->all();



                break;



            default:

                $request->session()->flash('error', 'This is wrong user type.');

                return redirect()->route('admin.dashboard');
        }



        return view('super-admin.user.all_view', compact('user', 'no_comp'));
    }



    public function fetch_callback_data()

    {

        $response = Http::withHeaders([

            'Authorization' => 'Bearer 9as8df7a9s8df7a98s7df9a8s7df9a87sdf9a87sdf9a87sdf9a87sdf9a87sdf9a8',

        ])->get('https://eld.apnatelelink.us/api/fetch_callback.php');



        // Check the status code

        if ($response->status() === 200) {

            // Request was successful, handle the response data

            $responseData = $response->json();

            // Process $responseData here

            dd($responseData);
        } else {

            // Handle errors

            // For example, if unauthorized

            if ($response->status() === 401) {

                dd('Unauthorized');
            } else {

                dd('Error occurred');
            }
        }
    }



    public function eld_output_file(Request $request, $type, $token)

    {
        $staticToken = config('app.ELD_OUTPUT_FILE_TOKEN');

        if ($staticToken !== $token) {

            $request->session()->flash('error', 'Invalid token provided.');

            return redirect()->route('login');
        }

        $id = 98; // Replace with actual dynamic ID logic

        $driver        = User::with('state', 'userInfo')->find($id);

        $userInfo      = $driver?->userInfo;

        $vehicleAssign = VehicleAssign::with('device', 'vehicle')->where('driver_id', $id)->first();

        if (! $driver || ! $userInfo || empty($userInfo->home_terminal_timezone)) {

            $request->session()->flash('error', 'Driver not found or missing timezone.');

            return redirect()->route('login');
        }

        $masterId    = $driver->master_id;

        $users       = User::where('master_id', $masterId)->where('user_type', 'U')->with('userInfo')->get();

        $cmvs        = Vehicle::where('created_by', $masterId)->select('id', 'name', 'vin')->get();

        $driverId    = $userInfo->driver_id;

        $timezone    = $userInfo->home_terminal_timezone;

        $currentTime = Carbon::now($timezone);

        $startTime   = $currentTime->copy()->startOfDay();

        $lastTime    = $currentTime->copy()->endOfDay();

        $formatDate  = $currentTime->format('Y-m-d');



        $hos    = hos_date_data($id, $startTime, $lastTime);

        $events = $hos[0][$formatDate][2] ?? [];

        $eventUserId = 0;

        $serial = $vehicleAssign?->device?->serial_number ?? null;

        $vehicleLogHistory = null;

        if ($serial) {

            $vehicleLogHistory = VehicleLogHistory::where('identifier', $serial)

                ->whereBetween('event_date_time', [$startTime, $lastTime])

                ->orderByDesc('event_date_time')

                ->first();



            if (! $vehicleLogHistory) {

                $vehicleLogHistory = VehicleLogHistory::where('identifier', $serial)

                    ->where('event_date_time', '<=', $startTime)

                    ->orderByDesc('event_date_time')

                    ->first();
            }
        }



        $malfunctions = collect();

        if ($serial) {

            $malfunctions = VehicleLogHistory::where('identifier', $serial)

                ->where('message_reason', 'MILON')

                ->whereBetween('event_date_time', [$startTime, $lastTime])

                ->orderByDesc('event_date_time')

                ->get();
        }



        $powerEvents = collect();

        if ($serial) {

            $powerEvents = VehicleLogHistory::where('identifier', $serial)

                ->whereIn('message_reason', ['POWER_CUT', 'POWER_UP'])

                ->whereBetween('event_date_time', [$startTime, $lastTime])

                ->orderByDesc('event_date_time')

                ->get();
        }



        $coDriverRecord = CoDriver::where('user_id', $id)->where('codriver_date', $formatDate)->first();



        if (! $coDriverRecord) {

            $coDriverRecord = CoDriver::where('user_id', $id)->where('codriver_date', '!=', $formatDate)->orderByDesc('id')->first();
        }



        $file = null;

        try {

            $folderPath = storage_path("app/eld");

            if (! is_dir($folderPath)) {

                mkdir($folderPath, 0755, true);
            }

            $filename = "eld_output_{$driverId}." . $type;

            $filePath = $folderPath . DIRECTORY_SEPARATOR . $filename;



            $file = fopen($filePath, 'w');

            if ($file === false) {

                throw new \Exception('Unable to open file for writing: ' . $filePath);
            }

            if ($type === 'txt') {
                $filePath = storage_path("app/eld_output_{$driverId}.txt");

                if ($file) {
                    fclose($file);
                }

                $file = fopen($filePath, 'w');
                if ($file === false) {
                    throw new \Exception('Unable to open file for writing: ' . $filePath);
                }

                $eol = "\r";

                // We'll accumulate all line check values here (decimal)
                $allLineCheckValues = [];

                // Helper function to write a line with check value calculation
                function fwriteWithCheck($file, $line, &$allLineCheckValues)
                {
                    $checkValue = calculateCheckValue($line);
                    fwrite($file, $line . ',' . $checkValue . "\r");
                    $allLineCheckValues[] = hexdec($checkValue);
                }

                // === ELD File Header Segment ===
                $segmentLabel = "ELD File Header Segment:";
                fwrite($file, $segmentLabel . $eol);

                $headerLine = implode(',', [
                    $driver->first_name ?? '',
                    $driver->last_name ?? '',
                    $userInfo->username ?? '',
                    getStateCode($driver->state?->state_name) ?? '',
                    '6A',
                ]);
                fwriteWithCheck($file, $headerLine, $allLineCheckValues);

                // === Co-Drivers ===
                if ($coDriverRecord) {
                    $driverIds = explode(',', $coDriverRecord->codriver_id);
                    $coDrivers = User::whereIn('id', $driverIds)->with('userInfo')->get();

                    foreach ($coDrivers as $coDriver) {
                        $coLine = implode(',', [
                            $coDriver->last_name ?? '',
                            $coDriver->first_name ?? '',
                            $coDriver->userInfo->username ?? '',
                        ]);
                        fwriteWithCheck($file, $coLine, $allLineCheckValues);
                    }
                }

                // === Vehicle Info ===
                if ($vehicleAssign && $vehicleAssign->vehicle) {
                    $line = "{$vehicleAssign->vehicle->name},{$vehicleAssign->vehicle->vin},DC";
                    fwriteWithCheck($file, $line, $allLineCheckValues);
                }

                // === Carrier Info ===
                $carrierLine = implode(',', [
                    $userInfo->carrer_us_dot_number ?? '',
                    $userInfo->career_name ?? '',
                    '8',
                    '000000',
                    '8',
                ]);
                fwriteWithCheck($file, $carrierLine, $allLineCheckValues);

                // === Third Party ===
                $thirdParty = "Thirdparty,0";
                fwriteWithCheck($file, $thirdParty, $allLineCheckValues);
                // fwrite($file, "Thirdparty,0,CC" . $eol);

                // === ELD ID Line ===
                if ($vehicleLogHistory && !empty($vehicleLogHistory->location)) {
                    $loc = json_decode($vehicleLogHistory->location);
                    $dt = Carbon::parse($vehicleLogHistory->event_date_time);
                    $logDate = $dt->format('mdy');
                    $logTime = $dt->format('His');
                    $engineHour = 0;

                    if (isset($loc->GeoLocation)) {
                        $geo = $loc->GeoLocation;
                        $geoLine = implode(',', [
                            $logDate,
                            $logTime,
                            number_format($geo->Latitude, 6),
                            number_format($geo->Longitude, 6),
                            (int) ($vehicleLogHistory->odometer ?? 0),
                            (int) ($engineHour)
                        ]);
                        fwriteWithCheck($file, $geoLine, $allLineCheckValues);
                    }
                }

                // === Software Info ===
                $softwareLine = "0007,MOTIVE,kLshN6Mag1cVj6iQC6BdlFfug3KKwyVF,chaubeyprashant498@gmail.com";
                fwriteWithCheck($file, $softwareLine, $allLineCheckValues);

                // === User List ===
                fwrite($file, "User List:" . $eol);
                foreach ($users as $i => $user) {

                    if ($user->id == $id) {
                        $eventUserId = $i + 1;
                    }
                    $lineData = implode(',', [
                        $i + 1,
                        'D',
                        strtoupper($user->last_name ?? ''),
                        ucfirst($user->first_name ?? ''),
                    ]);
                    fwriteWithCheck($file, $lineData, $allLineCheckValues);
                }

                // === CMV List ===
                fwrite($file, "CMV List:" . $eol);
                foreach ($cmvs as $i => $cmv) {
                    $lineData = implode(',', [
                        $i + 1,
                        $cmv->name,
                        $cmv->vin,
                    ]);
                    fwriteWithCheck($file, $lineData, $allLineCheckValues);
                }


                // === ELD Event List ===
                fwrite($file, "ELD Event List:" . $eol);

                foreach ($events as $i => $event) {
                    $eventID = strtoupper(str_pad(dechex($i + 1), 4, '0', STR_PAD_LEFT));
                    $eventDate = $currentTime->format('mdy');
                    $eventTime = $event[0] ?? ''; // Should be in "HHMMSS" format already or parsed
                    $lat = $lng = '';
                    $engineHour = $event[9] ?? 0;
                    $odometer = $event[7] ?? 0;

                    if (!empty($event[6]) && is_array($event[6])) {
                        $lat = number_format($event[6][0], 6, '.', '');
                        $lng = number_format($event[6][1], 6, '.', '');
                    }

                    $eventTitle = $event[1];

                    $listLog = ListOption::where(
                        "title",
                        $eventTitle
                    )
                        ->where("list_id", "driving_status")
                        ->first();

                    $optionId = $listLog->option_id;

                    $eventType = 3;

                    if ($optionId == 3) {
                        $eventType = 1;
                    } else if ($optionId == 4) {
                        $eventType = 2;
                    } else {
                        $eventType = 3;
                    }

                    $lineDataArray = [
                        $eventID,                   // Event record ID (hex)
                        1,                          // Record status
                        2,                          // Event Record Origin
                        $eventType,                          // Event Type
                        1,                          // Event code
                        $eventDate,                // Event date (MMDDYY)
                        str_replace(':', '', $eventTime), // Event time (HHMMSS)
                        (int) 0,                        // Accumulated vehicle miles
                        (int) $engineHour,                        // Engine hours
                        $lat == '' ? 0 : $lat,                       // Latitude
                        $lng == '' ? 0 : $lng,                       // Longitude
                        0,                          // CMV ( 1= power on, 0: power off)
                        6,                          // Trailer attached
                        $eventUserId,               // Event user ID
                        0,                          // Event sequence ID
                        ($optionId == 5 || $optionId == 6) ? 1 : 0, // PC or YM 1= yes, 0 = No
                        'AE',                       // Line checksum (optional - placeholder here)
                    ];

                    $lineData = implode(',', $lineDataArray);

                    // Compute checksum and write line
                    fwriteWithCheck($file, $lineData, $allLineCheckValues);
                }

                fwrite($file, "ELD Event Annotations or Comments:" . $eol);
                fwrite($file, "Driver's Certification/Recertification Actions:" . $eol);
                fwrite($file, "Malfunctions and Data Diagnostic Events:" . $eol);
                foreach ($malfunctions as $mal) {
                    $malID = strtoupper(str_pad(dechex($mal->id), 4, '0', STR_PAD_LEFT));
                    $malDate = Carbon::parse($mal->event_date_time)->format('mdy');
                    $malTime = Carbon::parse($mal->event_date_time)->format('His');
                    $rpm = $mal->obd_engine_rpm ?? 0;
                    $engHour = $rpm > 0 ? $rpm / 60 : 0;

                    $line = implode(',', [
                        $malID,
                        4,
                        1,
                        $malDate,
                        $malTime,
                        $mal->odometer ?? 0,
                        $engHour,
                        1,
                        '3A'
                    ]);
                    fwriteWithCheck($file, $line, $allLineCheckValues);
                }

                fwrite($file, "ELD Login/Logout Report:" . $eol);
                fwrite($file, "CMV Engine Power-Up and Shut Down Activity:" . $eol);
                foreach ($powerEvents as $pe) {
                    $id = strtoupper(str_pad(dechex($pe->id), 4, '0', STR_PAD_LEFT));
                    $date = Carbon::parse($pe->event_date_time)->format('mdy');
                    $time = Carbon::parse($pe->event_date_time)->format('His');
                    $rpm = $pe->obd_engine_rpm ?? 0;
                    $engHour = $rpm > 0 ? $rpm / 60 : 0;
                    $lat = $lng = '';
                    $loc = json_decode($pe->location);
                    if (isset($loc->GeoLocation)) {
                        $lat = number_format($loc->GeoLocation->Latitude, 6);
                        $lng = number_format($loc->GeoLocation->Longitude, 6);
                    }

                    $line = implode(',', [
                        $id,
                        1,
                        $date,
                        $time,
                        $pe->odometer ?? 0,
                        $engHour,
                        $lat,
                        $lng,
                        $pe->power_unit ?? '',
                        $pe->vin ?? '',
                        '',
                        '',
                        $pe->event_code ?? '',
                    ]);
                    fwriteWithCheck($file, $line, $allLineCheckValues);
                }

                fwrite($file, "Unidentified Driver Profile Records:" . $eol);
                fwrite($file, "End of File:" . $eol);

                // --- Compute the file data check value correctly ---
                // --- Compute the file data check value correctly ---
                $sumCheckValues = array_sum($allLineCheckValues);
                $val16 = $sumCheckValues & 0xFFFF;

                $lowByte = $val16 & 0xFF;
                $highByte = ($val16 >> 8) & 0xFF;

                $circularLeftShift3 = function ($byte) {
                    return (($byte << 3) & 0xFF) | ($byte >> 5);
                };

                $lowByteShifted = $circularLeftShift3($lowByte);
                $highByteShifted = $circularLeftShift3($highByte);

                $shiftedVal = ($highByteShifted << 8) | $lowByteShifted;
                $finalVal = $shiftedVal ^ 0x969C;

                $finalHex = strtoupper(str_pad(dechex($finalVal), 4, '0', STR_PAD_LEFT));
                fwrite($file, $finalHex . $eol); // ← Use computed value here

                fclose($file);

                return response()->download($filePath)->deleteFileAfterSend(true);
            }

            throw new \Exception("Unsupported file type requested: $type");
        } catch (\Exception $ex) {

            if ($file) {

                fclose($file);
            }

            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}
