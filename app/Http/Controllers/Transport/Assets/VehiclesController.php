<?php
namespace App\Http\Controllers\Transport\Assets;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Models\Language;
use App\Models\ListOption;
use App\Models\State;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\Vehicle;
use Illuminate\Http\Request;
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

        $id = 98;

        $date = "2026-07-07";

        $startTime = Carbon::parse($date)->startOfDay();

        $endTime = Carbon::parse($date)->endOfDay();

        $userInfo = UserInfo::where('user_id', $id)->first();

        $timezone = $userInfo->home_terminal_timezone;

        $currentTime = Carbon::parse()->setTimezone($timezone)->toDateTimeLocalString();

        $currentTime = Carbon::parse($currentTime);

        $data = graph_hos_chart($id, $startTime, $endTime, $currentTime);

        return response()->json($data);

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
