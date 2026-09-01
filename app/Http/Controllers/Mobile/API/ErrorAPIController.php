<?php

namespace App\Http\Controllers\Mobile\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ErrorLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ErrorAPIController extends Controller
{

    public function error_data_save(Request $request)
    {



        try {
            $request->validate([
                'device_platform' => 'required|string|in:android,ios',
                'log_file' => 'required|file|max:10240|mimetypes:text/plain,text/x-log,application/octet-stream',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $userId = Auth::id();

        $file = $request->file('log_file');

        $extension = $file->getClientOriginalExtension() ?: 'txt';

        $fileName = time() . '_' . $file->getClientOriginalName();

        $path = $file->storeAs('error_logs', $fileName, 'public');

        $errorLog = ErrorLog::create([
            'device_platform' => $request->device_platform,
            'file_name' => $fileName,
            'file_path' => $path,
            'created_by' => $userId,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Log uploaded successfully.',
            'data' => $errorLog,
        ], 201);
    }
}
