<?php

namespace App\Http\Controllers\SuperAdmin\Hardware\Device;

use App\Http\Controllers\Controller;
use App\Models\DeviceAdmin;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DeviceAssignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['device'] = UserDevice::with('user')->get();
        return view('super-admin.hardware.device_user.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();

        // Fetch WC users created by the current user
        $wcUsers = $user->wcUsers;

        // Prepare an array to hold EC and TR users
        $ecUsers = [];
        $trUsers = [];

        // For each WC user, fetch EC and TR users
        foreach ($wcUsers as $wcUser) {
            // Fetch EC users created by this WC user
            $ecUsers[$wcUser->id] = $wcUser->ecUsers;

            // For each EC user, fetch TR users
            foreach ($ecUsers[$wcUser->id] as $ecUser) {
                $trUsers[] = $ecUser->trUsers;
            }
        }


        $data['user'] = $trUsers;

        $data['device'] = DeviceAdmin::where('created_by', $user->id)->get();

        // Now you have WC users, and their associated EC and TR users

        return view('super-admin.hardware.device_user.add', compact('data'));
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
            'serialNo' => [
                'required',
                'unique:user_device,serialNo', // Ensure to replace 'table_name' with the actual name of your database table
            ],
            'user_id' => 'required',
        ]);


        $user = Auth::user();

        UserDevice::create([
            'user_id' => $request->user_id,
            'serialNo' => $request->serialNo,
            'master_id' => $user->id,
            'master_company_id' => $user->id,
            'created_by' => $user->id,
        ]);

        $request->session()->flash('success', 'Device assign added successfully.');

        return redirect()->route('device.assign.index');

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
        $data['userDevice'] = UserDevice::find($id);

        $user = Auth::user();

        // Fetch WC users created by the current user
        $wcUsers = $user->wcUsers;

        // Prepare an array to hold EC and TR users
        $ecUsers = [];
        $trUsers = [];

        // For each WC user, fetch EC and TR users
        foreach ($wcUsers as $wcUser) {
            // Fetch EC users created by this WC user
            $ecUsers[$wcUser->id] = $wcUser->ecUsers;

            // For each EC user, fetch TR users
            foreach ($ecUsers[$wcUser->id] as $ecUser) {
                $trUsers[] = $ecUser->trUsers;
            }
        }


        $data['user'] = $trUsers;

        $data['device'] = DeviceAdmin::where('created_by', $user->id)->get();


        return view('super-admin.hardware.device_user.edit', compact('data'));
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
        $request->validate([
            'serialNo' => [
                'required',
                Rule::unique('user_device', 'serialNo')->ignore($id), // Exclude the current record by ID
            ],
            'user_id' => 'required',
        ]);

        $userDevice = UserDevice::find($id);

        $user = Auth::user();

        $userDevice->update([
            'user_id' => $request->user_id,
            'serialNo' => $request->serialNo,
            'updated_by' => $user->id,
        ]);

        $request->session()->flash('success', 'Device assign updated successfully.');

        return redirect()->route('device.assign.index');

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
