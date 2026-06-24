<?php

namespace App\Http\Controllers\transport\Map;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Models\Language;
use App\Models\VehicleAssign;
use App\Models\ApiLogger;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Hardware;
use App\Models\User;
use App\Models\VehicleLogHistory;

class MapController extends Controller
{
    public function show_map(Request $request, $lang)
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

        $start_date = $request->input('start-date');

        $end_date = $request->input('end-date');

        $prev_date = $request->input('prev-day');

        if (!empty($start_date) && !empty($end_date)) {

            $vehicle_history = VehicleLogHistory::whereBetween('event_date_time', [$start_date, $end_date])->orderBy('event_date_time', 'asc')->get();

        } elseif (!empty($start_date)) {

            $date = Carbon::parse($start_date)->setTime(0, 0, 0);
            $startOfDay = (clone $date)->startOfDay();

            $vehicle_history = VehicleLogHistory::whereDate('event_date_time', '>=', $startOfDay)->orderBy('event_date_time', 'asc')->get();

        } elseif (!empty($end_date)) {

            $date = Carbon::parse($end_date)->setTime(0, 0, 0);
            $endOfDay = (clone $date)->endOfDay();

            $vehicle_history = VehicleLogHistory::whereDate('event_date_time', '<=', $endOfDay)->orderBy('event_date_time', 'asc')->get();

        } elseif (!empty($prev_date)) {

            $date = Carbon::parse($prev_date)->setTime(0, 0, 0);
            $startOfDay = (clone $date)->startOfDay();
            $endOfDay = (clone $date)->endOfDay();

            $vehicle_history = VehicleLogHistory::whereBetween('event_date_time', [$startOfDay, $endOfDay])->orderBy('event_date_time', 'asc')->get();

        } else {
            $startOfDay = Carbon::now()->startOfDay();
            $endOfDay = Carbon::now()->endOfDay();
            $vehicle_history = VehicleLogHistory::whereBetween('event_date_time', [$startOfDay, $endOfDay])
                ->orderBy('event_date_time', 'asc')
                ->get();


        }

        $routeData = [];

        foreach ($vehicle_history as $data) {

            $location = json_decode($data['location'], true);

            $lat = $location['GeoLocation']['Latitude'];

            $lang = $location['GeoLocation']['Longitude'];

            $direction = $data['direction_alpha'];

            $locationKey = $lat . '_' . $lang;

            if (!isset($routeData[$locationKey])) {

                $routeData[$locationKey] = [

                    'latitude' => $lat,

                    'longitude' => $lang,

                    'directionAlpha' => $direction,

                ];

            }

        }

        $routeData = array_values($routeData);

        $driver = User::where('master_id', Auth::user()->id)->where('is_active', 1)->get();

        return view('transport.map.index', compact('routeData', 'driver'));

    }

}
