<?php

namespace App\Http\Controllers\API\Transport\Assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleAssign;
use App\Models\User;
use App\Models\ListOption;
use App\Models\State;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;

class VehicleAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        $userIds = Auth::user()->master_id;
        $trans = User::where('master_id', $userIds)->get();
        $option = ListOption::where('list_id', 'fuel_type')->get();
        $make = ListOption::where('list_id', 'make')->get();
        $state = State::where('is_active', 1)->get();
        $throttle_wifi = Config::get('app.TH');
        $user = Auth::user()->id;

        $data['trans'] = $trans;
        $data['make'] = $make;
        $data['state'] = $state;
        $data['throttle_wifi'] = $throttle_wifi;
        $data['option'] = $option;
        $data['vehicles'] = Vehicle::where('created_by', Auth::user()->id)
            ->with(['devices', 'latestVehicleLogHistory', 'latestDriverShiftLog.driver'])
            ->get();
        $data['vehicle_year'] = Config::get('app.vehicle_year');

        return response()->json($data, 200);

    }

    public function create()
    {
        $userIds = Auth::user()->master_id;
        $trans = User::where('master_id', $userIds)->get();
        $option = ListOption::where('list_id', 'fuel_type')->get();
        $make = ListOption::where('list_id', 'make')->get();
        $state = State::where('is_active', 1)->get();
        $throttle_wifi = Config::get('app.TH');
        $user = Auth::user()->id;

        $data['trans'] = $trans;
        $data['make'] = $make;
        $data['state'] = $state;
        $data['throttle_wifi'] = $throttle_wifi;
        $data['option'] = $option;

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Vehicle::create([
            'name' => $request->name,
            'master_company_id' => Auth::user()->master_company_id,   // Company id
            'master_id' => Auth::user()->master_id,             // Group id
            'vin' => $request->vin,
            'throttle_wifi' => $request->throttle_wifi,
            'make' => $request->make,
            'model' => $request->model,
            'fuel_type' => $request->fuel_type,
            'year' => $request->year,
            'license_state' => $request->license_state,
            'license_plate' => $request->license_plate,
            'notes' => $request->notes,
            'fuel_tank_primary' => $request->fuel_tank_primary,
            'fuel_tank_secondary' => $request->fuel_tank_secondary,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        return response()->json(['success' => 'Vehicle is created successfully!'], 200);
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

    public function edit($id)
    {
        $data['vehicle'] = Vehicle::find($id);

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $vehicle = Vehicle::find($id);

        $vehicle->update([
            'name' => $request->name,
            'master_company_id' => Auth::user()->master_company_id,   // Company id
            'master_id' => Auth::user()->master_id,             // Group id
            'vin' => $request->vin,
            'throttle_wifi' => $request->throttle_wifi,
            'make' => $request->make,
            'fuel_type' => $request->fuel_type,
            'model' => $request->model,
            'year' => $request->year,
            'license_state' => $request->license_state,
            'license_plate' => $request->license_plate,
            'notes' => $request->notes,
            'fuel_tank_primary' => $request->fuel_tank_primary,
            'fuel_tank_secondary' => $request->fuel_tank_secondary,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $vehicle = Vehicle::where('id', $id)->where('created_by', Auth::user()->id)->first();

        if (!isset($vehicle)) {
            return response()->json(['error' => 'Vehicle not found.'], 401);
        }

        if ($vehicle->status == 0) {

            $vehicle->update([
                'status' => 1,
            ]);

            return response()->json(['success' => 'Vehicle activated successfully.'], 200);

        } else {

            $vehicle->update([
                'status' => 0,
            ]);

            return response()->json(['success' => 'Vehicle de-activated successfully.'], 200);
        }
    }
    
    public function check_unique_vin(Request $request, $vin = null, $id = null)
    {

        $query = Vehicle::where('vin', $vin);

        // If $id is provided, exclude the user with that ID
        if ($id) {
            $query->where('id', '!=', $id);
        }

        $userCount = $query->count();

        return response()->json($userCount);
    }
    
    public function vehicle_notify(Request $request)
    {
        $user = Auth::user();
        $notifications = $user->notifications;
        $unreadNotificationsCount = $user->unreadNotifications->count();
        $unreadMessage = $user->unreadNotifications->map(function ($notification) {
          // Include the notification ID along with other attributes if needed
          return [
            'id' => $notification->id,
            'data' => $notification->data, // Include other relevant data as needed
            'created_at' => $notification->created_at, // Include timestamps if needed
            // Add any other attributes you want to include here
          ];
        });
        $data = [$notifications, $unreadNotificationsCount, $unreadMessage];
        return response()->json($data);
    }

    public function vehicle_unnotify()
    {
        $user = Auth::user();
        $unreadNotificationsCount = $user->unreadNotifications->count();
 
        if($unreadNotificationsCount > 0){
 
         $user->unreadNotifications->markAsRead();
 
        }
        
        return response()->json('Notification read');
        
    }
    
    public function vehicle_notify_id(Request $request, $id)
    {
      $user = Auth::user();

      // Retrieve the notification by ID
      $notification = $user->unreadNotifications()->find($id);

      if ($notification) {
         // Mark the specific notification as read
         $notification->markAsRead();
        
         return response()->json(['message' => 'Notification marked as read']);
      }

      return response()->json(['message' => 'Notification not found'], 404);
   }

    
    public function assign_vehicle(Request $request, $id)
    {
        $data['vechile'] = VehicleAssign::with('vehicle')->where('driver_id', $id)->get();
        return response()->json($data, 200);
    }
    
}
