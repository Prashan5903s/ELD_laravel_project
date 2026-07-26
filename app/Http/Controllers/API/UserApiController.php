<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\City;
use App\Models\User;
use App\Models\State;
use App\Models\Device;
use App\Models\Vehicle;
use App\Models\Template;
use Illuminate\Support\Str;
use App\Models\Country;
use App\Mail\ForgotWebMail;
use Carbon\CarbonPeriod;
use App\Models\UserInfo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\Language;
use App\Models\EmailLogs;
use App\Models\ListOption;
use App\Models\LogSession;
use Laravel\Passport\Token;
use Illuminate\Http\Request;
use App\Models\VehicleAssign;
use App\Models\DriverShiftLog;
use App\Models\VehicleLogHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

use App\Events\ForceLogoutEvent;
use Illuminate\Support\Facades\Log;



class UserApiController extends Controller
{
    public function add(Request $request)
    {
        $user = new User;
        $user->first_name = $request->firstname;
        $user->last_name = $request->lastname;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return $user;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid credentials',
            ], 401);
        }

        $userInfo = UserInfo::where('user_id', $user->id)->first();

        $userType = $user->user_type;
        $master = User::find($user->master_id);
        $masterUserType = $master?->user_type;

        if (
            !(
                $userType === "EC" ||
                $userType === "TR" ||
                ($userType === "U" && $masterUserType === "TR")
            )
        ) {
            return response()->json([
                'success' => false,
                'error' => 'Wrong credential',
            ], 401);
        }

        // Timezone
        $timeZone = $userType === "TR"
            ? $user->timezone
            : ($userInfo->home_terminal_timezone ?? $user->timezone);

        $currTime = now()->setTimezone($timeZone)->toDateTimeString();

        // Check for existing active session
        $activeSession = LogSession::where('user_id', $user->id)
            ->where('log_status', 'i')
            ->first();

        if ($userType == "U") {

            if ($activeSession) {

                if ($request->input('force') == "1") {

                    $oldToken = $activeSession->user_token;

                    // Mark old session as logged out
                    $activeSession->update([
                        'log_status' => 'o',
                        'logout_time' => $currTime,
                    ]);

                    // Broadcast force logout event
                    try {
                        if ($oldToken) {
                            broadcast(new ForceLogoutEvent(
                                $user->id,
                                $oldToken
                            ));
                        }
                    } catch (\Throwable $e) {
                        Log::error(
                            'Broadcast failed: ' . $e->getMessage()
                        );
                    }

                    // Revoke all previous tokens
                    try {
                        $user->tokens()->delete();
                    } catch (\Throwable $e) {
                        Log::error(
                            'Token revoke failed: ' . $e->getMessage()
                        );
                    }

                } else {

                    return response()->json([
                        'success' => false,
                        'status' => 401,
                        'error' => 'You are already logged in on another device.'
                    ], 401);

                }

            } else {

                // Cleanup stale tokens
                try {
                    $user->tokens()->delete();
                } catch (\Throwable $e) {
                    Log::error(
                        'Token cleanup failed: ' . $e->getMessage()
                    );
                }
            }
        }

        // Generate Passport token
        $tokenResult = $user->createToken('Auth_token');

        $tokenString = $tokenResult->accessToken;
        $tokenId = $tokenResult->token->id;

        // Create login session record
        $logSession = LogSession::create([
            'log_status' => 'i',
            'login_time' => $currTime,
            'ip' => $request->ip(),
            'user_token' => $tokenString,
            'token_id' => $tokenId,
            'user_agent' => $request->userAgent(),
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'id' => $user->id,
            'token' => $tokenString,
            'user_type' => $userType,
            'master_user_type' => $masterUserType,
            'user_id' => $user->id,
            'log_session' => $logSession->id,
        ], 200);
    }

    public function companyIndex(Request $request)
    {
        $user = Auth::user();

        $data = User::where('user_type', 'TR')->where('master_id', $user->id)->get();

        return response()->json($data);
    }

    public function transportIndex(Request $request)
    {
        return $request->session()->get('data_id');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {

            $timeZone = $user->timezone ?? config('app.timezone');

            $currTime = Carbon::now()->setTimezone($timeZone);

            LogSession::where('user_id', $user->id)
                ->update([
                    'log_status' => 'o',
                    'logout_time' => $currTime,
                ]);

            $user->token()->delete();
        }

        $log_session = LogSession::where('log_status', 'i')
            ->get();


        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
            'log_session' => $log_session
        ]);
    }

    public function editShow(Request $request, $id)
    {
        try {
            $data['user'] = User::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $data['userInfo'] = UserInfo::where('user_id', $id)->first();
        $data['countries'] = Country::where('is_active', 1)->get();
        $data['state'] = State::where('is_active', 1)->get();
        $data['city'] = City::where('is_active', 1)->get();
        $data['timezones'] = [];
        $data['lang'] = Language::where('is_active', 1)->get();

        foreach (timezone_identifiers_list() as $timezone) {
            $dt = new \DateTime('now', new \DateTimeZone($timezone));
            $offset = $dt->getOffset() / 3600;
            $offsetString = ($offset >= 0 ? '+' : '-') . sprintf('%02d', abs($offset)) . ':00';
            $data['timezones'][$timezone] = "(GMT$offsetString) " . str_replace('_', ' ', $timezone);
        }

        return response()->json($data, 200);
    }

    public function addShow()
    {
        $data['countries'] = Country::where('is_active', 1)->get();
        $data['state'] = State::where('is_active', 1)->get();
        $data['city'] = City::where('is_active', 1)->get();
        $data['timezones'] = [];
        $data['lang'] = Language::where('is_active', 1)->get();

        foreach (timezone_identifiers_list() as $timezone) {
            $dt = new \DateTime('now', new \DateTimeZone($timezone));
            $offset = $dt->getOffset() / 3600;
            $offsetString = ($offset >= 0 ? '+' : '-') . sprintf('%02d', abs($offset)) . ':00';
            $data['timezones'][$timezone] = "(GMT$offsetString) " . str_replace('_', ' ', $timezone);
        }

        return response()->json($data, 200);
    }

    public function postAdd(Request $request)
    {
        $userId = Auth::user()->id;
        $userMasterId = Auth::user()->master_company_id;
        $user = new User;
        $user->master_id = $userId;
        $user->master_company_id = $userMasterId;
        $user->first_name = $request->first_name;
        $user->user_type = "TR";
        $user->last_name = $request->last_name;
        $user->comp_name = $request->comp_name;
        $user->timezone = $request->timezone;
        $user->password = Hash::make($request->password);
        $user->email = $request->email;
        $user->save();
        return response()->json([
            'message' => 'User saved successfully',
        ], 200);

        // if ($userId) {
        //     $userMasterId = Auth::user()->master_company_id;

        //     $user = new User;

        //     $user->first_name = $request->first_name;
        //     $user->last_name = $request->last_name;
        //     $user->comp_name = $request->comp_name;
        //     $user->user_type = "TR";
        //     $user->email = $request->email;
        //     $user->password = Hash::make($request->password);
        //     $user->mobile_no = $request->mobile_no;
        //     $user->state_id = $request->state_id;
        //     $user->master_company_id = $userMasterId;
        //     $user->master_id = $request->$userId;
        //     $user->country_id = $request->country_id;
        //     $user->is_active = $request->status;
        //     $user->timezone = $request->timezone;
        //     $user->city_id = $request->city_id;
        //     $user->save();

        //     if ($user->save()) {
        //         $userInfo = new UserInfo;
        //         $userInfo->user_id = $userId;
        //         $userInfo->language_id = $request->langu;
        //         $userInfo->save();
        //         if ($userInfo->save()) {
        //             return response()->json([
        //                 'user' => $user,
        //                 'message' => 'Successfull',
        //             ], 200);
        //         }
        //     } else {
        //         return response()->json([
        //             'error' => "Not saved",
        //         ], 400);
        //     }
        // } else {
        //     return response()->json(
        //         [
        //             'Error' => "User id not fetching",
        //         ],
        //         400
        //     );
        // }

    }

    public function getStates(Request $request, $id)
    {
        $state = State::where('country_id', $id)->get();
        return response()->json($state);
    }

    public function getCities(Request $request, $id)
    {
        $city = City::where('state_id', $id)->get();
        return response()->json($city);
    }

    public function profile_user_data()
    {
        $user = Auth::user();

        // Eager load both userInfo and country relationships
        $user->load(['userInfo', 'country']);

        return response()->json($user, 200);
    }

    public function check_email_token($token)
    {

        $check_token = EmailLogs::where('sender_token', $token)->exists() ? 1 : 0;

        $email_data = EmailLogs::where('sender_token', $token)->first();

        $data_val = [];

        if ($email_data) {

            $start_time = $email_data->start_time;

            $end_time = $email_data->end_time;

            $start = Carbon::parse($start_time);

            $end = Carbon::parse($end_time);

            $period = CarbonPeriod::create($start, '1 day', $end);

            // Format the dates as strings
            $last7Days = [];
            foreach ($period as $date) {
                $last7Days[] = $date->format('Y-m-d H:i:s');
            }

            $senderId = $email_data->sender_id;

            $user_id = $email_data->user_id;

            $id = $senderId == 0 ? $user_id : $senderId;

            $user = User::select('id', 'first_name', 'last_name', 'email', 'mobile_no', 'pin_code', 'address', 'timezone')->find($id);

            $vehicleAssign = VehicleAssign::where('driver_id', $id)
                ->select('driver_id', 'vechile_id')
                ->with(['vehicle:id,name,model,year,vin,license_plate,status']) // Select specific fields for the vehicle
                ->get();

            $userInfo = UserInfo::where('user_id', $id)
                ->select('driver_id', 'career_name', 'main_office_address', 'carrer_us_dot_number', 'home_terminal_name', 'home_terminal_timezone')
                ->first();

            $timeZone = $userInfo->home_terminal_timezone;

            $timeNows = Carbon::parse()->setTimezone($timeZone)->toDateTimeString();

            $timeNow = Carbon::parse($end_time)->endOfDay();

            $currentTime = $timeNows;

            if ($last7Days) {

                for ($i = count($last7Days) - 1; $i >= 0; $i--) {

                    $data = $last7Days[$i];

                    $formattedDate = Carbon::parse($data)->format('Y-m-d');

                    $aboveTimess = null;

                    $start = Carbon::parse($data)->startOfDay();

                    $end = Carbon::parse($data)->endOfDay();

                    $create = $start;

                    $last = $end;

                    $eldData = check_eld_rules($id, $create, $last);

                    $logsData = DriverShiftLog::where('driver_id', $id)
                        ->where('is_add_approved', 1)
                        ->where(function ($query) use ($create, $last, $currentTime) {
                            $query->where(function ($subQuery) use ($create, $last, $currentTime) {
                                // Check if there is any overlap between the time range and the log times
                                $subQuery->where(function ($q) use ($create, $last, $currentTime) {
                                    // Check if the log's start time is within the range of create and last
                                    $q->where('start_log_time', '>=', $create)
                                        ->where('start_log_time', '<=', $last)
                                        // Check if the log's end time is within the range of create and last
                                        ->orWhere(function ($query) use ($create, $last, $currentTime) {
                                        $query->whereRaw('IFNULL(end_log_time, ?) >= ?', [$currentTime, $create])
                                            ->whereRaw('IFNULL(end_log_time, ?) <= ?', [$currentTime, $last]);
                                    })
                                        // Check if the log encompasses the range between create and last
                                        ->orWhere(function ($q2) use ($create, $last, $currentTime) {
                                        $q2->where('start_log_time', '<=', $create)
                                            ->whereRaw('IFNULL(end_log_time, ?) >= ?', [$currentTime, $last]);
                                    })
                                        // Check if end_log_time equals start_log_time or create
                                        ->orWhere(function ($q3) use ($create) {
                                        $q3->whereColumn('end_log_time', 'start_log_time')
                                            ->orWhereRaw('end_log_time = ?', [$create]);
                                    });
                                });
                            });
                        })
                        ->orderBy('start_log_time', 'asc')
                        ->get();

                    $datass = [];

                    if ($logsData && count($logsData) > 0) {

                        foreach ($logsData as $datai) {

                            $rowTimess = $datai->start_log_time;

                            $aboveTimess = $datai->end_log_time;

                            if ($rowTimess < $create) {

                                $rowTimess = $create;
                            }

                            if ($aboveTimess == null) {

                                $aboveTimess = $currentTime;
                            }

                            if ($aboveTimess > $last) {

                                $aboveTimess = $last;
                            }

                            $odoMeter = null;

                            $rowTimess = Carbon::parse($rowTimess);

                            $aboveTimess = Carbon::parse($aboveTimess);

                            $time_start = $rowTimess->format('h:i A');

                            $listLog = ListOption::where('option_id', $datai->current_shift_status)->where('list_id', 'driving_status')->first();

                            $status = $listLog->title;

                            $message = $datai->message_reason;

                            $vehicle_id = $datai->vehicle_id;

                            $vehicle = Vehicle::find($vehicle_id)->name;

                            $timeStartLog = $rowTimess;

                            $timeEndLog = $aboveTimess;

                            $locationStart = [];

                            $startLog = $datai;

                            if ($startLog) {

                                $startVehicle = $startLog->vehicle_id;

                                $startDevice = Device::where('vehicle_id', $startVehicle)->first();

                                if ($startDevice) {

                                    $startVehicleLog = VehicleLogHistory::where('identifier', $startDevice->serial_number)
                                        ->whereBetween('event_date_time', [$timeStartLog, $timeEndLog])
                                        ->first();

                                    if (!$startVehicleLog) {

                                        $startVehicleLog = VehicleLogHistory::where('identifier', $startDevice->serial_number)
                                            ->orderBy('event_date_time', 'desc')
                                            ->where('event_date_time', '<', $timeStartLog)
                                            ->first();
                                    }

                                    if ($startVehicleLog && isset($startVehicleLog->location)) {

                                        $odoMeter = $startVehicleLog->odometer;

                                        $location = json_decode($startVehicleLog->location);

                                        if (isset($location->GeoLocation->Latitude) && isset($location->GeoLocation->Longitude)) {

                                            $latitude = $location->GeoLocation->Latitude;

                                            $longitude = $location->GeoLocation->Longitude;

                                            $locationStart = [$latitude, $longitude];
                                        }
                                    }
                                }
                            }

                            $time_end = Carbon::parse($aboveTimess)->format('h:i A');

                            $timeInSeconds = Carbon::parse($aboveTimess)->diffInSeconds(Carbon::parse($rowTimess));

                            $duration = secondsToTime($timeInSeconds);

                            $datass[] = [
                                'Duration' => $duration,
                                'Log' => $status,
                                'message_reason' => $message,
                                'Vehicle' => $vehicle,
                                'start_time' => $time_start,
                                'end_time' => $time_end,
                                'location' => $locationStart,
                                'odometer' => $odoMeter,
                            ];
                        }
                    }

                    $datas[] = [

                        $formattedDate => [
                            'log_data' => $datass,
                            'eld_data' => $eldData,
                        ],

                    ];
                }
            }

            $last7Days = collect($last7Days);

            $last = $last7Days->first();

            $first = $last7Days->last();

            $data_val = [

                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data fetched successfully',
                'token_exist' => $check_token,
                'user' => $user,
                'first_day' => $first,
                'last_day' => $last,
                'user_info' => $userInfo,
                'vehicle_assign' => $vehicleAssign,
                // 'data' => $datas,
            ];
        }

        return response()->json([

            'status' => 'success',
            'statusCode' => 200,
            'token_exist' => $check_token,
            'data' => $data_val,

        ]);
    }

    public function forgot_password($email)
    {
        // Validate email format
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email',  // Ensure the email is provided and in a valid format
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Invalid email format',
                'code' => 400,
            ], 400);
        }

        // Check if the email exists in the User table
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'failure',
                'message' => 'User email does not exist',
                'code' => 401,
            ], 401);
        }

        $userInfo = UserInfo::where('user_id', $user->id)->first();

        // Get user details
        $timezone = $userInfo->home_terminal_timezone; // Default to app timezone if not set

        $name = $user->first_name . ' ' . $user->last_name;

        $currentTime = Carbon::now()->setTimezone($timezone)->toDateTimeLocalString();

        $currentTime = Carbon::parse($currentTime);

        // Set start time as the current time
        $startTime = $currentTime;

        // Set end time as the next day at the same time
        $endTime = $currentTime->copy()->addDay()->toDateTimeLocalString();

        $endTime = Carbon::parse($endTime);

        function generateUniqueToken()
        {
            do {
                // Generate a random 60 character alphanumeric string
                $token = Str::random(60);
            } while (EmailLogs::where('sender_token', $token)->exists()); // Check if token already exists in the database

            return $token;
        }

        // Retrieve the email template
        $template = Template::find(8);

        if (!$template) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Email template not found',
                'code' => 500,
            ], 500);
        }

        $senderToken = generateUniqueToken();

        $mail = EmailLogs::create([
            'template_id' => 8,
            'sender_id' => $user->id,
            'reciever_email' => $email,
            'send_time' => $startTime,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'sender_token' => $senderToken,
            'type' => 1,
            'is_send' => 1,
        ]);

        $data = [$senderToken, $mail->id, $template->template_text];

        try {
            // Send the email
            Mail::to($email)->send(
                (new ForgotWebMail($data))->from(env('MAIL_FROM_ADDRESS'), $name)
            );

            return response()->json([
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Email sent successfully',
            ], 200);
        } catch (\Exception $e) {
            // Handle email sending failure
            return response()->json([
                'status' => 'failure',
                'message' => 'Failed to send email: ' . $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }


    public function check_token_forgot_password($token)
    {

        $tokenExist = EmailLogs::where('sender_token', $token)->where('is_used', 0)->exists();

        if ($tokenExist) {
            $userEmail = EmailLogs::where('sender_token', $token)->where('is_used', 0)->first();

            if ($userEmail) {
                $userId = $userEmail->sender_id;

                $userInfo = UserInfo::where('user_id', $userId)->first();

                // Get user details
                $timezone = $userInfo->home_terminal_timezone; // Default to app timezone if not set

                $currentTime = Carbon::now()->setTimezone($timezone)->toDateTimeLocalString(); // No need to convert to string here

                $currentTime = Carbon::parse($currentTime);

                $startTime = Carbon::parse($userEmail->start_time); // Parse start_time into a Carbon instance
                $endTime = Carbon::parse($userEmail->end_time);     // Parse end_time into a Carbon instance

                // Check if current time is between start_time and end_time
                if ($currentTime->between($startTime, $endTime)) {

                    // Current time is within the valid range
                    return response()->json($tokenExist);
                } else {

                    return response()->json(!$tokenExist);
                }
            }
        }

        return response()->json($tokenExist);
    }

    public function check_reset_password(Request $request, $token)
    {
        // Retrieve the email log based on the token
        $emailLog = EmailLogs::where('sender_token', $token)->first();

        if (!$emailLog) {
            return response()->json([
                'data' => 'Token does not exist',
            ], 404);
        }

        $emailLog->update([
            'is_used' => 1,
        ]);

        // Find the user based on the sender_id from the email log
        $user = User::find($emailLog->sender_id);

        if (!$user) {
            return response()->json([
                'data' => 'User not found',
            ], 404);
        }

        // Validate the password input and ensure password and change_password are the same
        $request->validate([
            'password' => 'required|min:8', // Assuming confirmation is required
            // 'confirm_password' => 'required|min:8|same:password', // Ensure change_password matches password
        ]);

        // Update the user's password (ensure it's hashed)
        $user->update([
            'password' => Hash::make($request->password), // bcrypt to hash the password
        ]);

        return response()->json([
            'message' => 'Password updated successfully',
        ], 200);
    }
}
