<?php

namespace App\Http\Controllers\API\Transport\Alert;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\UserAlert;
use App\Models\ListOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Hamcrest\Arrays\IsArray;

class AlertAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $userId = $user->id;

        $data = [];

        $userAlert = UserAlert::where('created_by', $userId)->orderBy('created_at', 'DESC')->select('id', 'alert_name', 'type_id', 'trigger_range', 'method', 'recipient_id', 'trigger_week_day', 'trigger_start_time', 'trigger_end_time', 'user_id', 'vehicle_id', 'is_priority', 'vehicle_id', 'frequency', 'schedule_time', 'schedule_day_of_month', 'schedule_day', 'status', 'created_at')->with('ListOption')->get();

        if ($userAlert && count($userAlert) > 0) {
            foreach ($userAlert as $alert) {

                $alertName = $alert->alert_name;
                $alertStatus = $alert->status;
                $alertId = $alert->id;
                $userID =  $alert->user_id;
                $vehicleId = $alert->vehicle_id;
                $method = $alert->method;
                $type = $alert->listOption->title;
                $dataType = $alert->listOption->type;

                $paragraph = $this->paragraph_build($alert);

                $typeSelected = $this->alert_type_created($alert, $dataType);

                $vehicleId = ($vehicleId == null && $vehicleId == 'null') ? [] : explode(',', $vehicleId);

                $userID = ($userID == null && $userID == 'null') ? [] : explode(',', $userID);

                $userInfo = User::whereIn('id', $userID)->select('id', 'first_name', 'last_name')->get();

                $vehicleInfo = Vehicle::whereIn('id', $vehicleId)->select('id', 'name')->get();

                $data[] = [
                    'id' => $alertId,
                    'method' => $method,
                    'name' => $alertName,
                    'user' => $userInfo,
                    'vehicle' => $vehicleInfo,
                    'type' => $type,
                    'status' => $alertStatus,
                    'paragraph' => $paragraph,
                    'selected' => $typeSelected
                ];
            }
        }

        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();

        $userId = $user->id;

        $data['alertType'] = ListOption::where('list_id', 'alert_type')->orderBy('title', 'ASC')->get();

        $data['driver'] = User::where('master_id', $userId)->select('id', 'first_name', 'last_name')->get();

        $data['fleetUser'] = User::where('user_type', 'FU')->where('master_id', $userId)->select('id', 'first_name', 'last_name')->get();

        $data['vehicle'] = Vehicle::where('created_by', $userId)->select('id', 'name')->get();

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

        $user = Auth::user();

        $userId = $user->id;

        $masterId = $user->master_id;
        $masterCompanyId = $user->master_company_id;

        $startTime = null;
        $endTime = null;

        $name = $request->name;
        $typeId = $request->Type;
        $type = $request->dataType;
        $driverId = $request->driver;
        $method = $request->method;
        $priority = $request->priority;
        $recipientId = $request->recipient;
        $triggerRange = $request->trigger_range;
        $vehicleId = $request->vehicle;
        $range = $request->Range;
        $dates = $request->dates;
        $recipientId = implode(',', $recipientId);

        $userAlertCheck = UserAlert::where('type_id', $typeId)->where('created_by', $userId)->first();

        if ($userAlertCheck) {
            return response()->json("Cannot make alert for same type", 403);
        }

        if ($type == 1) {
            $driverId = implode(',', $driverId);
            $vehicleId = null;
        } else {
            $driverId = null;
            $vehicleId = implode(',', $vehicleId);
        }

        if ($triggerRange == 4) {
            $dates = implode(',', $dates);
            $startTime = $range[0];
            $endTime = $range[1];
        }

        UserAlert::create([
            'alert_name' =>          $name,
            'type_id' =>             $typeId,
            'trigger_range' =>       $triggerRange,
            'trigger_week_day' =>    $dates,
            'trigger_start_time' =>  $startTime,
            'trigger_end_time' =>    $endTime,
            'user_id' =>             $driverId,
            'vehicle_id' =>          $vehicleId,
            'recipient_id' =>        $recipientId,
            'is_priority' => ($priority == true || $priority == 'true') ? 0 : 1,
            'method' =>              $method,
            'frequency' =>           1,
            'created_by' =>          $userId,
            'master_id' =>           $masterId,
            'master_company_id' =>   $masterCompanyId,
            'status' =>              1,
        ]);

        return response()->json("Data saved successfully", 200);
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
        $finalData = [];
        $user = Auth::user();
        $userId = $user->id;
        $data = UserAlert::where('id', $id)->where('created_by', $userId)->first();

        if ($data) {
            $paraGraph = $this->paragraph_build($data);
            $list = ListOption::where('list_id', 'alert_type')->where('option_id', $data->type_id)->first();
            $dataType = $list->type;
            $finalData = [
                'name' => $data->alert_name,
                'Type' => $data->type_id,
                'method' => $data->method,
                'trigger_range' => $data->trigger_range,
                'driver' => ($data->user_id != null && $data->user_id != 'null')  ? explode(',', $data->user_id) : $data->user_id,
                'vehicle' => ($data->vehicle_id != null || $data->vehicle_id != null) ? explode(',', $data->vehicle_id) : $data->vehicle_id,
                'recipient' => explode(',', $data->recipient_id),
                'priority' => $data->is_priority ? true : false,
                'range' => $data->trigger_range != 4 ? null : [$data->trigger_start_time, $data->trigger_end_time],
                'dates' => $data->trigger_range == 4 ? explode(',', $data->trigger_week_day) : $data->trigger_week_day,
                'paragraph' => $paraGraph,
                'dataType' => $dataType,
            ];
        }
        return response()->json($finalData);
    }

    public function paragraph_build($data)
    {

        $user = Auth::user();

        $userId = $user->id;

        $driver = User::where('user_type', 'U')->where('master_id', $userId)->pluck('id')->toArray();
        $fleet = User::where('user_type', 'FU')->where('master_id', $userId)->pluck('id')->toArray();

        $recipientId = explode(',', $data->recipient_id);

        // Count matching driver IDs
        $driverMatches = count(array_intersect($driver, $recipientId));

        // Count matching fleet IDs
        $fleetUserMatches = count(array_intersect($fleet, $recipientId));

        $paraGraph = '';

        if ($fleetUserMatches == 0 && $driverMatches == 0) {
            $paraGraph = "No recipient selected";
            return $paraGraph;
        }

        if (count($driver) > 0 && count($fleet) > 0) {
            if ($fleetUserMatches === count($fleet) && $driverMatches === count($driver)) {
                $paraGraph = "All fleet user and all driver";
            } elseif ($fleetUserMatches > 0 && $driverMatches > 0) {
                $paraGraph = "{$fleetUserMatches} fleet user and {$driverMatches} driver";
            } else {
                $paraGraph = $fleetUserMatches > 0 ? "{$fleetUserMatches} fleet user" : "{$driverMatches} driver";
            }
        } elseif (count($fleet) > 0) {
            $paraGraph = ($fleetUserMatches === count($fleet)) ? "All fleet user" : "{$fleetUserMatches} fleet user";
        } elseif (count($driver) > 0) {
            $paraGraph = ($driverMatches === count($driver)) ? "All driver" : "{$driverMatches} driver";
        }

        return $paraGraph;
    }

    public function alert_type_created($data, $dataType)
    {

        $user = Auth::user();

        $userId = $user->id;

        $driver = User::where('user_type', 'U')->where('master_id', $userId)->pluck('id')->toArray();
        $vehicle = Vehicle::where('created_by', $userId)->pluck('id')->toArray();

        if ($dataType == 1) {
            $userId = explode(',', $data->user_id);
            $driverMatches = count(array_intersect($driver, $userId));
            if ($driverMatches == count($driver)) {
                $paragraph = "All driver selected";
            } else {
                $paragraph = $driverMatches . " driver selected";
            }
        } else {
            $vehicleId = explode(',', $data->vehicle_id);
            $vehicleMatches = count(array_intersect($vehicle, $vehicleId));
            if ($vehicleMatches == count($vehicle)) {
                $paragraph = "All vehicle selected";
            } else {
                $paragraph = $vehicleMatches . " vehicle selected";
            }
        }

        return $paragraph;
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

        $user = Auth::user();

        $userId = $user->id;

        $masterId = $user->master_id;
        $masterCompanyId = $user->master_company_id;

        $startTime = null;
        $endTime = null;

        $UserAlert = UserAlert::find($id);

        $name = $request->name;
        $typeId = $request->Type;
        $type = $request->dataType;
        $driverId = $request->driver;
        $method = $request->method;
        $priority = $request->priority;
        $recipientId = $request->recipient;
        $triggerRange = $request->trigger_range;
        $vehicleId = $request->vehicle;
        $range = $request->Range;
        $dates = $request->dates;
        $recipientId = implode(',', $recipientId);

        $userAlertCheck = UserAlert::where('type_id', $typeId)->where('created_by', $userId)->whereNot('id', $id)->first();

        if ($userAlertCheck) {
            return response()->json("Cannot make alert for same type", 403);
        }

        if ($type == 1) {
            $driverId = implode(',', $driverId);
            $vehicleId = null;
        } else {
            $driverId = null;
            $vehicleId = implode(',', $vehicleId);
        }

        if ($triggerRange == 4) {
            $dates = implode(',', $dates);
            $startTime = $range[0];
            $endTime = $range[1];
        } else {
            $dates = null;
            $startTime = null;
            $endTime = null;
        }

        $UserAlert->update([
            'alert_name' =>          $name,
            'type_id' =>             $typeId,
            'trigger_range' =>       $triggerRange,
            'trigger_week_day' =>    $dates,
            'trigger_start_time' =>  $startTime,
            'trigger_end_time' =>    $endTime,
            'user_id' =>             $driverId,
            'vehicle_id' =>          $vehicleId,
            'recipient_id' =>        $recipientId,
            'is_priority' => ($priority == true || $priority == 'true') ? 0 : 1,
            'method' =>              $method,
            'frequency' =>           1,
            'created_by' =>          $userId,
            'master_id' =>           $masterId,
            'master_company_id' =>   $masterCompanyId,
        ]);

        return response()->json("Data updated successfully", 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $alert = UserAlert::where('id', $id)->where('created_by', Auth::user()->id)->first();

        if (!isset($alert)) {
            return response()->json(['error' => 'Vehicle not found.'], 401);
        }

        if ($alert->status == 0) {

            $alert->update([
                'status' => 1,
            ]);

            return response()->json(['success' => 'Location activated successfully.'], 200);
        } else {

            $alert->update([
                'status' => 0,
            ]);

            return response()->json(['success' => 'Location de-activated successfully.'], 200);
        }
    }
}
