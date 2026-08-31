<?php

namespace App\Http\Controllers\Mobile\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ErrorLog;
use Illuminate\Support\Facades\Auth;

class ErrorAPIController extends Controller
{


    public function error_data_save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_platform' => 'required|string|in:android,ios',
            'log_file' => 'required|file|mimes:txt,log,text|max:10240', // 10 MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $userId = $user->id;

        $file = $request->file('log_file');

        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('error_logs', $fileName, 'public');

        $errorLog = ErrorLog::create([
            'device_platform' => $request->device_platform,
            'file_name' => $fileName,
            'file_path' => $path,
            'created_by' => $userId,
            'created_at' => now()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Log uploaded successfully.',
            'data' => $errorLog
        ]);
    }
}
