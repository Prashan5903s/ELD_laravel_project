<?php

namespace App\Http\Controllers\Mobile\API;

use App\Http\Controllers\Controller;
use App\Models\UserInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardMobieAPIController extends Controller
{

    public function dashboard_data_index()
    {

        $auth = Auth::check();

        if ($auth) {

            $id = Auth::user()->id;

            $userInfo = UserInfo::where('user_id', $id)->select('home_terminal_timezone', 'licenseNumber', 'username', 'career_name', 'main_office_address', 'carrer_us_dot_number', 'home_terminal_name', 'home_terminal_address')->with(['homeTerminal:id,name,address,shapeData'])->first();

            $timeZone = $userInfo->home_terminal_timezone;

            //Current time of today
            $currentTime = Carbon::now()->setTimezone($timeZone)->toDateTimeLocalString();

            $currentTime = Carbon::parse($currentTime);

            $log_data = driver_log_time($id, $currentTime);

            $data = [
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Data has been fetched successfully',
                'user' => $log_data[0],
                'userInfo' => $log_data[3],
                'vehicle' => $log_data[1],
                'time_in_current_status' => $log_data[2],
                'latest_log' => $log_data[5],
                'time_left_in_shift' => $log_data[4],
                'time_left_in_cycle' => $log_data[6],
                'time_left_in_drive' => $log_data[7],
                'time_left_in_break' => $log_data[8]
            ];
        } else {

            $data = [
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'Not authenticated'
            ];
        }

        return response()->json($data, $data['statusCode']);
    }
}
