<?php

namespace App\Http\Controllers\API\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Document;
use App\Models\ListOption;

class DocumentAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        $userIds = Auth::user()->master_id;
        $trans = User::where('master_id', $userIds)->get();
        $userId = Auth::user()->id;
        $drivers = User::select('id', 'first_name', 'last_name', 'country_code', 'mobile_no')->where('master_id', $userId)->get();
        $data['documents'] = Document::where('created_by', $userId)->get();

        // $data['document_types'] = Config::get('app.driver_document_types');
        $document_types = ListOption::getOptions("document_type", [], "1");

        $data['document_types'] = $document_types;
        $data['trans'] = $trans;
        $data['drivers'] = $drivers;

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

        if ($request->hasFile('file')) {
            $imageName = time() . '.' . $request->file->extension();
            $request->file->move(public_path('documents'), $imageName);
        } else {
            $imageName = null;
        }

        Document::create([
            'driver_id' => $request->driver_id,
            'document_type' => $request->document_type,
            'image' => $imageName,
            'status' => $request->status,
            'master_id' => Auth::user()->master_id,   // Company id
            'master_company_id' => Auth::user()->master_company_id,     // Group id
            'note' => $request->notes,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
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
    public function edit($id)
    {
        $data['doc'] = Document::find($id);

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

        return response()->json($request->document_type);

        $document = Document::find($id);

        if ($request->hasFile('file')) {
            $imageName = time() . '.' . $request->file->extension();
            $request->file->move(public_path('documents'), $imageName);
        } else {
            $imageName = $document->image;
        }

        $document->update([
            'driver_id' => $request->driver_id,
            'document_type' => $request->document_type,
            'image' => $imageName,
            'status' => $request->status,
            'master_id' => Auth::user()->master_id,   // Company id
            'master_company_id' => Auth::user()->master_company_id,     // Group id
            'note' => $request->notes,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);

        return response()->json($document, 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $document = Document::where('id', $id)->where('created_by', Auth::user()->id)->first();

        if (!isset($document)) {
            return response()->json(['error' => 'Vehicle not found.'], 401);
        }

        if ($document->status == 1) {

            $document->update([
                'status' => 2,
            ]);

            return response()->json(['success' => 'Document activated successfully.'], 200);

        } else {

            $document->update([
                'status' => 1,
            ]);

            return response()->json(['success' => 'Document de-activated successfully.'], 200);
        }
    }

    public function document_post(Request $request, $id)
    {

        $document = Document::find($id);

        if ($request->hasFile('file')) {
            $imageName = time() . '.' . $request->file->extension();
            $request->file->move(public_path('documents'), $imageName);
        } else {
            $imageName = $document->image;
        }


        $document->update([
            'driver_id' => $request->driver_id,
            'document_type' => $request->document_type,
            'image' => $imageName,
            'status' => $request->status,
            'master_id' => Auth::user()->master_id,   // Company id
            'master_company_id' => Auth::user()->master_company_id,     // Group id
            'note' => $request->notes,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);

        return response()->json($document, 200);

    }
}
