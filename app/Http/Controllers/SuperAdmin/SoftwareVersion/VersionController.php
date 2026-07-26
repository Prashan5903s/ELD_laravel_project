<?php

namespace App\Http\Controllers\SuperAdmin\SoftwareVersion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppVersion;

class VersionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = AppVersion::select('version_id', 'type', 'app_version_id')->get();

        return view('super-admin.SoftwareVersion.index', compact('data'));
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
    public function update(Request $request, $id)
    {
        $appVersion = AppVersion::find($id);

        if (!$appVersion) {
            return response()->json("Data does not exist", 404);
        }

        $version = $appVersion->version_id;

        [$major, $minor, $patch] = explode('.', $version);

        $type = $request->version_type;

        switch ($type) {
            case 'major':
                $major++;
                $minor = 0;
                $patch = 0;
                break;
            case 'minor':
                $minor++;
                $patch = 0;
                break;
            case 'patches':
                $patch++;
                break;
        }

        $newVersion = "$major.$minor.$patch";

        $appVersion->update([
            'version_id' => $newVersion
        ]);

        return response()->json("Successfully updated", 200);
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
