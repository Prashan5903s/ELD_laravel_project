<?php

namespace App\Http\Controllers\Transport\Settings\Organization;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $data = [];
        $data['organization'] = User::find($request->user()->master_id);
        $data['locals'] = Country::get();
        $data['languages'] = Language::get();
        $timezones = [];

        foreach (timezone_identifiers_list() as $timezone) {
            $dt = new \DateTime('now', new \DateTimeZone($timezone)); // Use \DateTime and \DateTimeZone without namespace
            $offset = $dt->getOffset() / 3600;
            $offsetString = ($offset >= 0 ? '+' : '-') . sprintf('%02d', abs($offset)) . ':00';
            $timezones[$timezone] = "(GMT$offsetString) " . str_replace('_', ' ', $timezone);

        }

        $data['timezones'] = $timezones;

        return view('transport.settings.organization.general', $data);
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
        //
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
            'organization_name' => 'required',
        ]);
        $organization = User::find($request->user()->master_id);

        if(!isset($organization)){
            return response()->json(['error', 'Organization not found.']);
        }

        $logo = $organization->avatar_image;

        if($request->hasFile('avatar_image')) {
            $logo = time() . '.' . $request->avatar_image->extension();
            $request->avatar_image->move(public_path('companyss'), $logo);
        }

        $organization->update([
            'language_id' => $request->language_id,
            'comp_name' => $request->organization_name,
            'country_id'   => $request->local,
            'timezone'  => $request->timezone,
            'avatar_image' => $logo,
        ]);

        return response()->json(['success' => 'Organization updated successfully.']);

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
