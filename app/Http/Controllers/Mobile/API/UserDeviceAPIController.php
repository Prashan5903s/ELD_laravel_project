<?php

namespace App\Http\Controllers\Mobile\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\UserDeviceNotify;
use Exception;

class UserDeviceAPIController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'failure',
                'statusCode' => 401,
                'message' => 'User authentication failed',
            ], 401);
        }

        $user = Auth::user();

        try {

            $request->validate([
                'device_id' => 'required|string|max:255',
                'fcm_token' => 'required|string',
                'platform' => 'required|in:android,ios',
            ]);

        } catch (ValidationException $e) {

            return response()->json([
                'status' => 'failure',
                'statusCode' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        try {

            // Deactivate all devices for this user
            UserDeviceNotify::where('user_id', $user->id)
                ->update([
                    'is_active' => false
                ]);

            // Check if this device already exists
            $device = UserDeviceNotify::where('device_id', $request->device_id)
                ->first();

            if ($device) {

                // Update existing device
                $device->update([
                    'user_id' => $user->id,
                    'platform' => $request->platform,
                    'fcm_token' => $request->fcm_token,
                    'is_active' => true,
                    'updated_by' => $user->id,
                ]);

                $exist_device = $device;

            } else {

                // Create new device
                $exist_device = UserDeviceNotify::create([
                    'user_id' => $user->id,
                    'device_id' => $request->device_id,
                    'platform' => $request->platform,
                    'fcm_token' => $request->fcm_token,
                    'is_active' => true,
                    'created_by' => $user->id,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Device registered successfully.',
                'data' => $exist_device,
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'status' => 'failure',
                'statusCode' => 500,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}