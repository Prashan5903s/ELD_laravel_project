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
                'log_file' => 'required|string|max:4294967295', // LONGTEXT max characters (theoretical)
            ]);

            if (!Auth::check()) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            $users = Auth::user();
            $userId = $users->id;

            // Save database record
            $errorLog = ErrorLog::create([
                'device_platform' => $request->device_platform,
                'log_data' => $request->log_file,
                'created_by' => $userId,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Log uploaded successfully.',
                'data' => [
                    'id' => $errorLog->id,
                    'device_platform' => $errorLog->device_platform,
                    'log_data' => $errorLog->log_data,
                ],
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
