<?php

namespace App\Http\Controllers\SuperAdmin\Hardware;

use App\Http\Controllers\Controller;
use App\Models\Hardware;
use Illuminate\Http\Request;
use App\Models\User;

class HardwareController extends Controller
{
    public function device_index(Request $request, $device)
    {
        $hardware = Hardware::where('hardware_name', $device)->where('is_active', 1)->first();
        if($hardware){
            $user = User::where('user_type', 'EC')->where('hardware_id', $hardware->id)->where('is_active', 1)->get();
            if (!$user) {
                return redirect()->route('admin.dashboard');
            } else {
                return view('super-admin.hardware.index', compact('user'));
            }
        } else {
            $request->session()->flash('error', "This hardware does not exist");
            return redirect()->route('admin.dashboard');
        }
    }
}
