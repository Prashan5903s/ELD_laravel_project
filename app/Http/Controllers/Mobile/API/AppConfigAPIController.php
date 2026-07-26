<?php

namespace App\Http\Controllers\mobile\API;

use Carbon\Carbon;
use App\Models\AppLabel;
use App\Models\Timezone;
use App\Models\Language;
use App\Models\UserInfo;
use App\Models\ListOption;
use Illuminate\Http\Request;
use App\Models\NavigationMenu;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AppConfigAPIController extends Controller
{
    public function app_config_data()
    {

        $auth = Auth::check();

        $dashboard_menu = AppLabel::select('label_id', 'language_id', 'label_name', 'label_text')
            // ->where('is_active', 1)
            ->get();

        $font_data = ListOption::select('list_id', 'title', 'short_name', 'language_id', 'seq')
            ->where('list_id', 'font_mode')
            ->get();
            
        $dutyStatus = ListOption::select('option_id', 'title', 'short_name', 'language_id', 'seq')
            ->where('list_id', 'driving_status')
            ->get();

        $lang = Language::select('id', 'short_name', 'language_name')->get();

        $navigationMenu = NavigationMenu::where('is_parent', 1)
            ->where('is_active', 1)
            ->select('menu_id', 'app_menu_id', 'language_id', 'menu_name', 'menu_link', 'view_type', 'display_leftmenu')
            ->with([
                'childMenus' => function ($query) {
                    $query->select('menu_id', 'app_menu_id', 'language_id', 'menu_name', 'menu_link', 'view_type', 'display_leftmenu');
                }
            ])
            ->get();
            
        $timezone = Timezone::select('timezone_key', 'timezone_value')->get();
        
        $odometer = ListOption::select('option_id', 'title', 'short_name', 'language_id', 'seq')
          ->where('list_id', 'odometer')
          ->get();
          
        $safety_type = ListOption::select('title', 'short_name')
            ->where('list_id', 'safety_type')
            ->get();

        if ($auth) {
            
            $userID = Auth::user()->id;

            $userInfo = UserInfo::where('user_id', $userID)->first();

            $timezone = $userInfo->home_terminal_timezone;

            $currentTime = Carbon::now($timezone)->toDateTimeLocalString();

            $currentTime = Carbon::parse($currentTime)->format('Y-m-d');

            if ($navigationMenu && $font_data && $dashboard_menu && $lang) {

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'All config data',
                    'main_menu_data' => $navigationMenu,
                    'dashboard_module_data' => $dashboard_menu,
                    'lang_data' => $lang,
                    'font_mode_data' => $font_data,
                    'duty_status' => $dutyStatus,
                    'timezone' => $timezone,
                    'odometer' => $odometer,
                    'safety_type' => $safety_type,
                    'currentTime' => $currentTime,
                ];

            } else {

                $data = [
                    'status' => 'failure',
                    'code' => 500,
                    'message' => 'Data retrieval failed'
                ];

            }

        } else {

            $data = [
                'status' => 'failure',
                'code' => 401,
                'message' => 'Not authenticated'
            ];

        }

        return response()->json($data, $data['code']);

    }
}
