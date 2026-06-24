<?php

namespace App\Http\Controllers\Mobile\API;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ListOption;
use Illuminate\Validation\ValidationException;

class DocumentMobileAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $auth = Auth::check();

        if ($auth) {

            $user = Auth::user();

            if ($user) {

                $documents = Document::select('id', 'document_type', 'image', 'status')
                 ->where('driver_id', $user->id)
                 ->with('listOption') // Eager load the ListOption relationship
                 ->get();
                 

                $data = [
                    'status' => 'success',
                    'statusCode' => 200,
                    'message' => 'Data fetched successfully',
                    'data' => $documents,

                ];

            } else {

                $data = [
                    'status' => 'failure',
                    'statusCode' => 403,
                    'message' => 'User does not exist',

                ];

            }

        } else {

            $data = [
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'Not uthenticated',

            ];


        }

        return response()->json($data, $data['statusCode']);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $document_types = ListOption::getOptions("document_type", [], "1");

        $data['document_types'] = $document_types;

        $data['status'] = [
            1 => 'Active',
            0 => "Inactive",
        ];

        $auth = Auth::check();

        if ($auth) {

            $datas = [
                'status' => "success",
                'statusCode' => 200,
                'message' => "Data fetched successfully",
                'data' => $data
            ];

        } else {

            $datas = [
                'status' => "failure",
                'statusCode' => 401,
                'message' => "Not authenticated",
            ];

        }

        return response()->json($datas, $datas['statusCode']);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        try {
            $request->validate([
                'document_file' => 'required|image', // Ensure the file is an image
                'document_type' => 'required|string',
                'status' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(), // Include validation error messages
            ], 422);
        }

        if (!Auth::check()) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'Not authenticated',
            ], 401);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 403,
                'message' => 'User does not exist',
            ], 403);
        }

        if ($request->hasFile('document_file')) {
            $imageName = time() . '.' . $request->document_file->extension();
            $request->document_file->move(public_path('documents'), $imageName);
        } else {
            $imageName = null;
        }

        $document = Document::create([
            'driver_id' => $user->id,
            'image' => $imageName,
            'document_type' => $request->document_type,
            'status' => $request->status,
            'note' => $request->note ?? null,
            'master_id' => $user->master_id,
            'master_company_id' => $user->master_company_id,
            'created_by' => $user->id,
        ]);

        if ($document) {
            return response()->json([
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Document uploaded successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'failure',
            'statusCode' => 500,
            'message' => 'Failed to create document',
        ], 500);
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
        $data = Document::select('id', 'document_type', 'image', 'note', 'status')->find($id);

        $auth = Auth::check();

        if ($auth) {

            $datas = [
                'status' => "success",
                'statusCode' => 200,
                'message' => "Data fetched successfully",
                'data' => $data
            ];

        } else {

            $datas = [
                'status' => "failure",
                'statusCode' => 401,
                'message' => "Not authenticated",
            ];

        }

        return response()->json($datas, $datas['statusCode']);

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

    }

    public function docs_update(Request $request, $id)
    {

        try {
            $request->validate([
                'document_file' => 'required|image|max:2048', // Ensure the file is an image and max size is 2 MB
                'document_type' => 'required|string',
                'status' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(), // Include validation error messages
            ], 422);
        }


        if (!Auth::check()) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'Not authenticated',
            ], 401);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 403,
                'message' => 'User does not exist',
            ], 403);
        }

        // Find the document
        $document = Document::find($id);

        if (!$document) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 404,
                'message' => 'Document not found',
            ], 404);
        }

        // Handle file upload if present
        $imageName = $document->image; // Preserve the existing image if no new one is uploaded

        if ($request->hasFile('document_file')) {
            $uploadedFile = $request->file('document_file');
            $imageName = time() . '.' . $uploadedFile->getClientOriginalExtension();
            $uploadedFile->move(public_path('documents'), $imageName);
        }

        // Update the document
        $documentUpdate = $document->update([
            'driver_id' => $user->id,
            'image' => $imageName,
            'document_type' => $request->document_type,
            'status' => $request->status,
            'note' => $request->note ?? $document->note, // Preserve the existing note if not provided
            'master_id' => $user->master_id,
            'master_company_id' => $user->master_company_id,
            'updated_by' => $user->id,
        ]);

        if ($documentUpdate) {
            return response()->json([
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Document updated successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'failure',
            'statusCode' => 500,
            'message' => 'Failed to update document',
        ], 500);

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
