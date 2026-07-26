<?php

namespace App\Http\Controllers\Transport\Device;

use App\Http\Controllers\Controller;
use App\Models\Hardware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Language;
use App\Models\Vehicle;
use App\Models\Device;
use App\Models\DeviceType;

class DeviceController extends Controller
{
    public function index()
    {
        $device = Device::with('deviceType', 'vehicle', 'hardware')->where('created_by', Auth::user()->id)->get();

        return view('transport.settings.devices.devices.index', compact('device'));

    }
    public function create()
    {
        $device_type = DeviceType::where('is_active', 1)->get();
        $vehicle = Vehicle::where('status', 1)->get();
        $hardware = Hardware::all();
        return view('transport.settings.devices.devices.add', compact('device_type', 'vehicle', 'hardware'));
    }
    public function store(Request $request, $lang)
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
        // Validate the request data

        $request->validate([
            'vehicle_id' => 'unique:devices,vehicle_id',
            'hardware_id' => 'required',
            'serial_number' => 'required|unique:devices,serial_number',
            'gateway_serial' => 'required|string|max:50',
            'gateway' => 'required|string',
            'is_active' => 'required|integer',
            'device_type' => 'required|integer' // Add validation for device_type if it's required

        ]);

        // Create a new device with the validated data
        Device::create([
            'hardware_id' => $request->hardware_id,
            'serial_number' => $request->serial_number,
            'device_type_id' => $request->device_type,
            'master_id' => Auth::user()->id,
            'master_company_id' => Auth::user()->master_company_id,
            'vehicle_id' => $request->vehicle_id,
            'gateway_serial' => $request->gateway_serial,
            'gateway' => $request->gateway,
            'created_by' => Auth::user()->id,
            'is_active' => $request->is_active
        ]);

        // Redirect to the index route with a success message
        return redirect()->route('setting.device.index', [$lang])->with('success', 'Device created successfully.');
    }

    public function edit(Request $request, $lang, $id)
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
        $device = Device::find($id);
        $device_type = DeviceType::all();
        $vehicle = Vehicle::where('status', 1)->get();
        $hardware = Hardware::all();
        return view('transport.settings.devices.devices.edit', compact('device', 'device_type', 'vehicle', 'hardware'));
    }

    public function update(Request $request, $lang, $id)
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

        $device = Device::find($id);

        $request->validate([
            'hardware_id' => 'required',
            'vehicle_id' => [
                Rule::unique('devices', 'vehicle_id')->ignore($device->id),
            ],
            'serial_number' => [
                'required',
                Rule::unique('devices', 'serial_number')->ignore($device->id),
            ],
            'gateway_serial' => 'required|string|max:50',
            'gateway' => 'required|string',
            'is_active' => 'required|integer',
            'device_type' => 'required|integer', // Add validation for device_type if it's required
        ]);

        $device->update([
            'hardware_id' => $request->hardware_id,
            'serial_number' => $request->serial_number,
            'device_type_id' => $request->device_type,
            'master_id' => Auth::user()->id,
            'master_company_id' => Auth::user()->master_company_id,
            'vehicle_id' => $request->vehicle_id,
            'gateway_serial' => $request->gateway_serial,
            'gateway' => $request->gateway,
            'updated_by' => Auth::user()->id,
            'is_active' => $request->is_active
        ]);

        return redirect()->route('setting.device.index', [$lang])->with('success', 'Device updated successfully.');
    }

}
