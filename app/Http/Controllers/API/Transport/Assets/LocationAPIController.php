<?php

namespace App\Http\Controllers\API\Transport\Assets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Location;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;

class LocationAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $user = Auth::user();
        $userIds = Auth::user()->master_id;
        $trans = User::where('master_id', $userIds)->get();

        $data['trans'] = $trans;
        $data['locations'] = Location::where('created_by', Auth::user()->id)->get();
        $data['address_types'] = Config::get('app.address_types');

        return response()->json($data);

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
        Location::create([
            'name' => $request->name,
            'address' => $request->address,
            'type' => $request->address_type,
            'tags' => $request->tags,
            'shapeData' => $request->shapeData,
            'notes' => $request->note,
            'master_company_id' => Auth::user()->master_company_id,   // Company id
            'master_id' => Auth::user()->master_id,                   // Group id
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        return response()->json(['success' => 'Location created successfully.']);
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
        $data['location'] = Location::find($id);
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
        $location = Location::find($id);

        $location->update([
            'name' => $request->name,
            'address' => $request->address,
            'type' => $request->address_type,
            'tags' => $request->tags,
            'notes' => $request->note,
            'shapeData' => $request->shapeData,
            'master_company_id' => Auth::user()->master_company_id,   // Company id
            'master_id' => Auth::user()->master_id,                   // Group id
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        return response()->json(['success' => 'Location updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $location = Location::where('id', $id)->where('created_by', Auth::user()->id)->first();

        if (!isset($location)) {
            return response()->json(['error' => 'Vehicle not found.'], 401);
        }

        if ($location->status == 0) {

            $location->update([
                'status' => 1,
            ]);

            return response()->json(['success' => 'Location activated successfully.'], 200);

        } else {

            $location->update([
                'status' => 0,
            ]);

            return response()->json(['success' => 'Location de-activated successfully.'], 200);
        }
    }
}
