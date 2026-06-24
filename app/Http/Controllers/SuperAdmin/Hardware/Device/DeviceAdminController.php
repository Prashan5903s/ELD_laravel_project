<?php

namespace App\Http\Controllers\SuperAdmin\Hardware\Device;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DeviceAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $data['device'] = DeviceAdmin::where('created_by', $user->id)->get();
        return view('super-admin.hardware.device.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('super-admin.hardware.device.add');
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
            'serial_no' => ['required', 'unique:device_admin,serialNo'],
        ]);

        $user = Auth::user();

        DeviceAdmin::create([
            'serialNo' => $request->serial_no,
            'master_id' => $user->id,
            'master_company_id' => $user->id,
            'created_by' => $user->id,
        ]);

        $request->session()->flash('success', 'Device added successfully.');

        return redirect()->route('device.admin.data.index');

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
        $data['device'] = DeviceAdmin::find($id);
        return view('super-admin.hardware.device.edit', compact('data'));
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
            'serial_no' => [
                'required',
                Rule::unique('device_admin', 'serialNo')->ignore($id), // Exclude the current record
            ],
        ]);

        $user = Auth::user();

        $device = DeviceAdmin::find($id);

        $device->update([
            'serialNo' => $request->serial_no,
            'master_id' => $user->master_id,
            'master_company_id' => $user->master_company_id,
            'created_by' => $user->id,
        ]);

        $request->session()->flash('success', 'Device updated successfully.');

        return redirect()->route('device.admin.data.index');
        // Proceed with the update logic here
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
