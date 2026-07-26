<?php

namespace App\Http\Controllers\Transport\Assets;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Location;
use App\Models\UserInfo;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class AddressesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $user = Auth::user();
        $userIds = Auth::user()->master_id;
        $trans = User::where('master_id', $userIds)->get();
        // $userInfo = UserInfo::where('user_id', $user->id)->first();
        // $lang = Language::where('id', $userInfo->language_id)->first();
        // $short = $lang->Short_name;
        // App::setLocale($short);

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
        $data['locations'] = Location::where('created_by', Auth::user()->id)->get();
        $data['address_types'] = Config::get('app.address_types');
        return view('transport.assets.addresses.index', $data);
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
            'address' => 'required',
            'type' => 'required',
        ]);

        Location::create([
            'name' => $request->name,
            'address' => $request->address,
            'type' => $request->type,
            'tags' => $request->tags,
            'notes' => $request->notes,
            'master_company_id' => Session::get('master_company_id'),   // Company id
            'master_id' => Session::get('master_id'),                   // Group id
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        return response()->json(['success' => 'Address created successfully.']);
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
            'address' => 'required',
            'type' => 'required',
        ]);

        $location = Location::find($id);

        if(!isset($location)){
            return response()->json(['error', 'Address not found.']);
        }

        $location->update([
            'name' => $request->name,
            'address' => $request->address,
            'type' => $request->type,
            'tags' => $request->tags,
            'notes' => $request->notes,
            'updated_by' => $request->user()->id,
        ]);

        return response()->json(['success' => 'Address updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($lang, $id)
    {
        $location = Location::find($id);

        if(!isset($location)){
            return response()->json(['error' => 'Address not found.']);
        }

        if($location->status == "0"){

            $location->update([
                'status' => '1',
            ]);

            return response()->json(['success' => 'Address activated successfully.']);

        }else{

            $location->update([
                'status' => '0',
            ]);

            return response()->json(['success' => 'Address de-activated successfully.']);
        }
    }
}
