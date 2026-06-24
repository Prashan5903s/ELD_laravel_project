<?php

namespace App\Http\Controllers\API\Driver\DOTInspection;

use Carbon\Carbon;
use App\Models\Device;
use App\Models\Template;
use App\Models\UserInfo;
use App\Models\EmailLogs;
use Illuminate\Support\Str;
use App\Models\VehicleAssign;
use App\Models\DriverShiftLog;
use App\Models\HOSUnsignedLog;
use App\Mail\DOTInspectionMail;
use App\Models\VehicleLogHistory;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class DOTInspectionAPIController extends Controller
{
    public function index()
    {

        $auth = Auth::check();

        if ($auth) {

            $users = Auth::user();

            $id = $users->id;

            $eld_comp_name = Config::get('app.eld__comp_name');

            $eld_mail = Config::get('app.eld_mail');

            $eld_url = Config::get('app.eld_web');

            $eld_mobileNo = Config::get('app.eld_mobileNo');

            $vId = VehicleAssign::where('driver_id', $id)->pluck('vechile_id')->toArray();

            $vehicleAssign = VehicleAssign::where('driver_id', $id)
                ->select('driver_id', 'vechile_id')
                ->with(['vehicle:id,name,model,year,vin,license_plate,status']) // Select specific fields for the vehicle
                ->get();

            $userInfo = UserInfo::where('user_id', $id)
                ->select('driver_id', 'career_name', 'main_office_address', 'carrer_us_dot_number', 'home_terminal_name', 'home_terminal_timezone')
                ->first();

            $timeZone = $userInfo->home_terminal_timezone;

            $timeNows = Carbon::parse()->setTimezone($timeZone)->toDateTimeString();

            $currentTime = Carbon::parse($timeNows);

            $datas = [];

            $timeNow = Carbon::parse($timeNows)->endOfDay();

            $last7Days = [];

            for ($i = 0; $i < 9; $i++) {

                $last7Days[] = $timeNow->copy()->subDays($i)->toDateTimeString();
            }

            $last7Days = array_reverse($last7Days);

            if ($last7Days) {

                for ($i = count($last7Days) - 1; $i >= 0; $i--) {

                    $data = $last7Days[$i];

                    $formattedDate = Carbon::parse($data)->format('Y-m-d');

                    $unsignedLog = HOSUnsignedLog::where('created_by', $id)->where('timeData', $formattedDate)->select('signature')->first();

                    $start = Carbon::parse($data)->startOfDay();

                    $end = Carbon::parse($data)->endOfDay();

                    $create = $start;

                    $last = $end;

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

                    $startlocation = [];

                    $endlocation = [];

                    if ($logsData) {

                        $startLog = $logsData->first();

                        $lastLog = $logsData->last();

                        if ($startLog && $lastLog) {

                            $startVehicle = $startLog->vehicle_id;

                            $lastVehicle = $lastLog->vehicle_id;

                            $startDevice = Device::where('vehicle_id', $startVehicle)->first();

                            $lastDevice = Device::where('vehicle_id', $lastVehicle)->first();

                            if ($startDevice) {

                                $startVehicleLog = VehicleLogHistory::where('identifier', $startDevice->serial_number)
                                    ->where('event_date_time', $start)
                                    ->first();

                                if (!$startVehicleLog) {

                                    $startVehicleLog = VehicleLogHistory::where('identifier', $startDevice->serial_number)
                                        ->orderBy('event_date_time', 'desc')
                                        ->where('event_date_time', '>', $start)
                                        ->first();
                                }


                                if ($startVehicleLog && isset($startVehicleLog->location)) {

                                    $location = json_decode($startVehicleLog->location);

                                    if (isset($location->GeoLocation->Latitude) && isset($location->GeoLocation->Longitude)) {

                                        $latitude = $location->GeoLocation->Latitude;

                                        $longitude = $location->GeoLocation->Longitude;

                                        $startlocation = [$latitude, $longitude];
                                    }
                                }
                            }

                            if ($lastDevice) {

                                $lastVehicleLog = VehicleLogHistory::where('identifier', $lastDevice->serial_number)
                                    ->where('event_date_time', $end)
                                    ->first();

                                if (!$lastVehicleLog) {

                                    $lastVehicleLog = VehicleLogHistory::where('identifier', $lastDevice->serial_number)
                                        ->orderBy('event_date_time', 'desc')
                                        ->where('event_date_time', '<', $end)
                                        ->first();
                                }

                                if ($lastVehicleLog && isset($lastVehicleLog->location)) {

                                    $location = json_decode($lastVehicleLog->location);

                                    if (isset($location->GeoLocation->Latitude) && isset($location->GeoLocation->Longitude)) {

                                        $latitude = $location->GeoLocation->Latitude;

                                        $longitude = $location->GeoLocation->Longitude;

                                        $endlocation = [$latitude, $longitude];
                                    }
                                }
                            }
                        }
                    }

                    $graphData = graph_hos_chart($id, $create, $last, $currentTime);

                    $finalData['start_loc'] = $startlocation;
                    $finalData['end_loc'] = $endlocation;

                    $finalData['hos_data'] = hos_date_data($id, $create, $last);

                    $finalData['dot_graph'] = $graphData;

                    $finalData['unsigned_log'] = $unsignedLog;

                    $finalData['malfun_check'] = malfunction_vehicle_check_data($vId, $data);

                    $finalData['vehicle_data'] = vehicle_distance_odometer_data($create, $last, $vehicleAssign);

                    $datas[] = [

                        $formattedDate => $finalData

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
                'first_day' => $first,
                'last_day' => $last,
                'currentTime' => $currentTime,
                'vehicle_assign' => $vehicleAssign,
                'dot_data' => $datas,
                'eld_comp_name' => $eld_comp_name,
                'eld_mail' => $eld_mail,
                'eld_url' => $eld_url,
                'eld_mobileNo' => $eld_mobileNo
            ];
        } else {

            $data_val = [

                'status' => "Success",
                'statusCode' => 401,
                'message' => 'Not authenticated',

            ];
        }

        return response()->json($data_val, $data_val['statusCode']);
    }

    public function send_mail($email)
    {

        $auth = Auth::check();

        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email',
        ]);

        if (!$auth) {

            return response()->json([
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'Not authenticated successfully',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 422,
                'message' => 'Invalid email address',
            ]);
        }

        $user = Auth::user();

        $userInfo = UserInfo::where('user_id', $user->id)->first();

        $timeZone = $userInfo->home_terminal_timezone;

        $timeNow = Carbon::now()->setTimezone($timeZone)->toDateTimeLocalString();

        $currentTime = Carbon::parse($timeNow);

        $data = $this->index()->getData(true); // true returns as array

        $template = Template::where('template_id', 1)->first();

        $template_subject = $template->template_subject;

        $template_text = $template->template_text;

        $firstDay = $data['first_day'];

        $lastDay = $data['last_day'];

        $first_day = Carbon::parse($firstDay)->format('M d');

        $last_day = Carbon::parse($lastDay)->format('M d');

        function generateUniqueToken()
        {
            do {
                // Generate a random 15 character alphanumeric string
                $token = Str::random(60);
            } while (EmailLogs::where('sender_token', $token)->exists()); // Check if token already exists in the database

            return $token;
        }

        $mail = EmailLogs::create([
            'template_id' => 1,
            'sender_id' => $user->id,
            'reciever_email' => $email,
            'send_time' => $currentTime,
            'start_time' => $data['first_day'],
            'end_time' => $data['last_day'],
            'sender_token' => generateUniqueToken(),
            'type' => 1,
            'is_send' => 1,
        ]);

        $emailData = [
            'user' => $user,
            'mail' => $mail,
            'subject' => $template_subject,
            'text' => $template_text,
            'eld_mail' => $data['eld_mail'],
            'eld_url' => $data['eld_url'],
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'first_day' => $first_day,
            'last_day' => $last_day,
            'token' => $mail->sender_token,
            'from_name' => "{$user->first_name} {$user->last_name}", // Dynamic name
        ];


        $mail = Mail::to($email)->send(
            (new DOTInspectionMail($emailData))->from(env('MAIL_FROM_ADDRESS'), $emailData['from_name'])
        );

        return response()->json([
            'status' => 'success',
            'statusCode' => 200,
            'message' => 'Email sent successfully',
        ]);
    }
}
