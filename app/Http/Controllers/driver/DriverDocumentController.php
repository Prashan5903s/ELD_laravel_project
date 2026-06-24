<?php

namespace App\Http\Controllers\driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Document;
use App\Models\UserInfo;
use App\Models\Language;
use App\Models\ListOption;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class DriverDocumentController extends Controller
{
    public function index(Request $request)
    {

        $data = [];

        $userIds = Auth::user()->id;

        $trans = User::find($userIds);

        $userId = Auth::user()->id;

        $drivers = User::select('id', 'first_name', 'last_name', 'country_code', 'mobile_no')->where('id', $userId)->get();

        $data['documents'] = Document::where('created_by', $userId)->get();

        // $data['document_types'] = Config::get('app.driver_document_types');
        $document_types = ListOption::getOptions("document_type", [], "1");

        $data['document_types'] = $document_types;

        $data['trans'] = $trans;

        $data['drivers'] = $drivers;

        return view('driver.documents.index', $data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
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
            'document_type' => 'required',
            'image' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'status' => 'required',
        ]);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('documents'), $imageName);
        } else {
            $imageName = null;
        }

        $user = Auth::user();

        Document::create([
            'driver_id' => $user->id,
            'document_type' => $request->document_type,
            'image' => $imageName,
            'status' => $request->status,
            'master_id' => $user->master_id,   // Company id
            'master_company_id' => $user->master_company_id,     // Group id
            'note' => $request->note,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        return response()->json(['success' => 'Document created successfully.']);
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
    public function edit(Request $request, $lang, $id)
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
            'document_type' => 'required',
            'status' => 'required',
        ]);

        $document = Document::find($id);

        if(!isset($document)){
            return response()->json(['error', 'Document not found.']);
        }

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('documents'), $imageName);
        } else {
            $imageName = $document->image;
        }

        $user = Auth::user();

        $document->update([
            'driver_id' => $user->id,
            'document_type' => $request->document_type,
            'image' => $imageName,
            'status' => $request->status,
            'note' => $request->note,
            'master_id' => $user->master_id,   // Company id
            'master_company_id' => $user->master_company_id,     // Group id
            'updated_by' => $user->id,
        ]);

        return response()->json(['success' => 'Document updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($lang, $id)
    {
        $document = Document::find($id);

        if(!isset($document)){
            return response()->json(['error' => 'Document not found.']);
        }

        if($document->status == "0"){

            $document->update([
                'status' => '1',
            ]);

            return response()->json(['success' => 'Document activated successfully.']);

        }else{

            $document->update([
                'status' => '0',
            ]);

            return response()->json(['success' => 'Document de-activated successfully.']);
        }
    }

}
