<?php

namespace App\Http\Controllers\Transport\Document;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Models\Location;
use App\Models\UserInfo;
use App\Models\Language;
use App\Models\ListOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $userIds = Auth::user()->master_id;
        $trans = User::where('master_id', $userIds)->get();
        $userId = Auth::user()->id;
        $drivers = User::select('id', 'first_name', 'last_name', 'country_code', 'mobile_no')->where('master_id', $userId)->get();
        $data['documents'] = Document::where('created_by', $userId)->get();
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

        // $data['document_types'] = Config::get('app.driver_document_types');
        $document_types = ListOption::getOptions("document_type", [], "1");

        $data['document_types'] = $document_types;
        $data['trans'] = $trans;
        $data['drivers'] = $drivers;
        return view('transport.document.index', $data);
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
            'driver_id' => ['required',
                            function ($attribute, $value, $fail) {
                                $driver = User::find($value);

                                if (!$driver || $driver->master_id !== auth()->id()) {
                                    $fail('The selected driver is invalid.');
                                }
                            }
                        ],
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

        Document::create([
            'driver_id' => $request->driver_id,
            'document_type' => $request->document_type,
            'image' => $imageName,
            'status' => $request->status,
            'master_id' => Session::get('master_id'),   // Company id
            'master_company_id' => Session::get('master_company_id'),     // Group id
            'note' => $request->note,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
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
            'driver_id' => ['required',
                            function ($attribute, $value, $fail) {
                                $driver = User::find($value);

                                if (!$driver || $driver->master_id !== auth()->id()) {
                                    $fail('The selected driver is invalid.');
                                }
                            }
                        ],
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

        $document->update([
            'driver_id' => $request->driver_id,
            'document_type' => $request->document_type,
            'image' => $imageName,
            'status' => $request->status,
            'note' => $request->note,
            'master_id' => Session::get('master_id'),   // Company id
            'master_company_id' => Session::get('master_company_id'),     // Group id
            'updated_by' => $request->user()->id,
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
