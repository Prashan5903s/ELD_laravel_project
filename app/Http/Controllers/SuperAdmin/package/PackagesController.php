<?php

namespace App\Http\Controllers\SuperAdmin\package;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Package;
use App\Models\PackageModule;
use App\Models\PackageType;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Currency;
use Illuminate\Validation\Rule;
use App\Models\ListOption;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NotifyNotification;
use Illuminate\Support\Facades\Config;

class PackagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['packages'] = Package::with(['packageType', 'currency', 'duration'])
            ->whereHas('duration', function ($query) {
                $query->where('list_id', 'payment_date');
            })
            ->get();

        $data['durations'] = Config::get('app.durations');
        $data['modules'] = Module::all();
        return view('super-admin.packages.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['modules'] = Module::all();
        $data['option'] = ListOption::where('list_id', 'payment_date')->get();
        $data['currency'] = Currency::all();
        $data['packageType'] = PackageType::all();
        $data['durations'] = Config::get('app.durations');
        return view('super-admin.packages.create', $data);
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
            'package_name' => ['required'],
            'package_code' => ['required', 'unique:packages,package_code'],
            'duration_id' => ['required'],
            'package_type_id' => ['required'],
            'currency_id' => ['required'],
            'price' => ['required', 'max:10'], // Adding max validation here
            'module_permission_id' => ['required', 'array'],
        ]);


        // dump($request->all());
        // dump($request->module_permission_id);

        $package = Package::create([
            'name' => $request->package_name,
            'package_type_id' => $request->package_type_id,
            'currency_id' => $request->currency_id,
            'price' => $request->price,
            'duration_id' => $request->duration_id,
            'package_code' => $request->package_code,
            'description' => $request->description,
            'duration' => $request->duration,
        ]);

        if (isset($request->module_permission_id) && count($request->module_permission_id) > 0) {
            foreach ($request->module_permission_id as $module => $permissions) {
                foreach ($permissions as $permission) {
                    PackageModule::create([
                        'package_id' => $package->id,
                        'module_id' => $module,
                        'permission_id' => $permission,
                    ]);
                }
            }
        }

        $user = User::where('user_type', 'SA')->first();
        $message = "New package has been added " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('white-label.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        return response()->json(['success' => 'Package updated successfully.']);
        
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
        $package = Package::find($id);
        if (!isset($package)) {
            return response()->json(['error', 'Package not found.']);
        }
        $data['package'] = $package;
        $data['modules'] = Module::all();
        $data['option'] = ListOption::where('list_id', 'payment_date')->get();
        $data['currency'] = Currency::all();
        $data['packageType'] = PackageType::all();
        $data['durations'] = Config::get('app.durations');
        return view('super-admin.packages.edit', $data);
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
            'package_name' => ['required'],
            'package_code' => [
                'required',
                Rule::unique('packages')->ignore($id),
            ],
            'duration_id' => ['required'],
            'package_type_id' => ['required'],
            'currency_id' => ['required'],
            'price' => ['required', 'max:10'],
            'module_permission_id' => ['required', 'array'],
        ]);


        $package = Package::find($id);

        if (!isset($package)) {
            return response()->json(['error', 'Package not found.']);
        }

        $package->update([
            'name' => $request->package_name,
            'package_type_id' => $request->package_type_id,
            'currency_id' => $request->currency_id,
            'price' => $request->price,
            'duration_id' => $request->duration_id,
            'package_code' => $request->package_code,
            'description' => $request->description,
            'duration' => $request->duration,
        ]);

        // if(isset($request->module_id) && count($request->module_id) > 0){
        //     PackageModule::where('package_id',$id)->delete();
        //     foreach($request->module_id as $module){
        //         PackageModule::create([
        //             'package_id' => $id,
        //             'module_id' => $module,
        //         ]);
        //     }
        // }

        if (isset($request->module_permission_id) && count($request->module_permission_id) > 0) {
            PackageModule::where('package_id', $id)->delete();
            foreach ($request->module_permission_id as $module => $permissions) {
                foreach ($permissions as $permission) {
                    PackageModule::create([
                        'package_id' => $package->id,
                        'module_id' => $module,
                        'permission_id' => $permission,
                    ]);
                }
            }
        }

        $user = User::where('user_type', 'SA')->first();
        $message = "New package has been edited " . Auth::user()->first_name . " " . Auth::user()->last_name;
        $url = route('white-label.dashboard');
        $notification = new NotifyNotification($message, $url);
        $user->notify($notification);

        return response()->json(['success' => 'Package updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $package = Package::find($id);

        if (!isset($package)) {
            return response()->json(['error' => 'Package not found.']);
        }

        if ($package->status == "0") {

            $package->update([
                'status' => '1',
            ]);

            return response()->json(['success' => 'Package Activated successfully.']);

        } else {

            $package->update([
                'status' => '0',
            ]);

            return response()->json(['success' => 'Package De-activated successfully.']);
        }
    }
}
