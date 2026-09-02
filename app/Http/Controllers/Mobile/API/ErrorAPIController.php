<?php

namespace App\Http\Controllers\Mobile\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ErrorLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ErrorAPIController extends Controller
{
    public function error_data_save(Request $request)
    {
        try {

            // Validate Request
            $request->validate([
                'device_platform' => 'required|string|in:android,ios',
                'log_file' => 'required|file',
            ]);

            // Check uploaded file
            if (!$request->hasFile('log_file') || !$request->file('log_file')->isValid()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid log file uploaded.',
                ], 400);
            }

            Log::info([
                'auth_check' => Auth::check(),
                'user' => Auth::user(),
                'id' => Auth::id(),
            ]);

            if (!Auth::check()) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            $users = Auth::user();
            $userId = $users->id;
            

            $file = $request->file('log_file');

            // Generate safe filename
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $originalName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName);

            $extension = $file->getClientOriginalExtension() ?: 'txt';

            $fileName = time() . '_' . $originalName . '.' . $extension;

            // Store file
            $path = $file->storeAs('error_logs', $fileName, 'public');

            // Save database record
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
        } catch (ValidationException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {

            Log::error('Error Log Upload Failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while uploading the log file.',
                'error' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
