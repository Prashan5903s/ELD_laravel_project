<?php

namespace App\Http\Controllers\SuperAdmin\package;

use App\Http\Controllers\Controller;
use App\Models\ListOption;
use App\Models\PackageAssign;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Package;
use Carbon\Carbon;

class PackageAssignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $packageAssign = PackageAssign::with(['user', 'package'])->get();

        return view('super-admin.packageAssign.index', compact('packageAssign'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $user = User::where('user_type', 'TR')->get();

        $package = Package::where('status', 1)->get();

        return view('super-admin.packageAssign.create', compact('user', 'package'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $days = 0;

        $request->validate([
            'user_id' => 'required',
            'package_id' => 'required',
            'start_date' => 'required',
        ]);

        $package = Package::find($request->package_id);

        $option = ListOption::where('list_id', 'payment_date')->where('id', $package->duration_id)->first();

        if ($option->title == 'Monthly') {
            $days = 30;
        } elseif ($option->title == 'Yearly') {
            $days = 365;
        } else {
            $days = 90;
        }

        // Parse the start date from the request using Carbon
        $startDate = Carbon::parse($request->start_date);

        // Clone the start date to create an end date
        $endDate = $startDate->copy()->addDays($days)->setTime(23, 59, 59);

        // Create a new record in the PackageAssign table with the start and end dates
        PackageAssign::create([
            'user_id' => $request->user_id,
            'package_id' => $request->package_id,
            'start_date' => $startDate->format('Y-m-d H:i:s'),  // Format start date as a string
            'end_date' => $endDate->format('Y-m-d H:i:s')       // Format end date as a string
        ]);

        return redirect()->route('package.assign.index')->with('message', 'Package assigned successfully!');
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
        $packAssgn = PackageAssign::find($id);
        $user = User::where('user_type', 'TR')->get();
        $package = Package::where('status', 1)->get();

        return view('super-admin.packageAssign.edit', compact('user', 'package', 'packAssgn'));
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
            'user_id' => 'required',
            'package_id' => 'required',
            'start_date' => 'required',
        ]);

        $packAssgn = PackageAssign::find($id);

        $days = 0;

        $package = Package::find($packAssgn->package_id);

        $option = ListOption::where('list_id', 'payment_date')->where('id', $package->duration_id)->first();

        if ($option->title == 'Monthly') {
            $days = 30;
        } elseif ($option->title == 'Yearly') {
            $days = 365;
        } else {
            $days = 90;
        }

        // Parse the start date from the request using Carbon
        $startDate = Carbon::parse($request->start_date);

        // Clone the start date to create an end date
        $endDate = $startDate->copy()->addDays($days)->setTime(23, 59, 59);

        $packAssgn->update([
            'user_id' => $request->user_id,
            'package_id' => $request->package_id,
            'start_date' => $startDate->format('Y-m-d H:i:s'),  // Format start date as a string
            'end_date' => $endDate->format('Y-m-d H:i:s')       // Format end date as a string

        ]);

        return redirect()->route('package.assign.index')->with('message', 'Package assigned successfully!');

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
