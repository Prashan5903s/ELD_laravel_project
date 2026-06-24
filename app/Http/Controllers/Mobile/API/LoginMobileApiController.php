<?php

namespace App\Http\Controllers\Mobile\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserInfo;
use Carbon\Carbon;
use App\Models\LogSession;
use Illuminate\Support\Facades\Log;
use App\Events\ForceLogoutEvent;
use Illuminate\Validation\ValidationException;

class LoginMobileApiController extends Controller
{

    public function mobile_login(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'email'],
                'password' => 'required',
            ], [
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'password.required' => 'Password is required.',
            ]);

            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'statusCode' => 401,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            $user = Auth::user();
            
            $users = User::select('first_name', 'last_name', 'email', 'country_code', 'mobile_no', 'pin_code', 'address', 'timezone', 'avatar_image')->find(Auth::user()->id);
            
            $userInfo = UserInfo::where('user_id', $user->id)->first();
            $master = $user->master_id ? User::find($user->master_id) : null;

            $userType = $user->user_type;

            if ($userType === 'U' && $master && $master->user_type === 'TR') {

                $activeSession = LogSession::where('user_id', $user->id)
                    ->where('log_status', 'i')
                    ->first();

                $timeZone = ($userInfo->home_terminal_timezone ?? $user->timezone ?? config('app.timezone'));
                $currTime = Carbon::now()->setTimezone($timeZone)->toDateTimeString();

                if ($activeSession) {

                    if ($request->input('force') == "1") {

                        $oldToken = $activeSession->user_token;

                        $activeSession->update([
                            'log_status' => 'o',
                            'logout_time' => $currTime,
                        ]);

                        try {
                            if ($oldToken) {

                                broadcast(new ForceLogoutEvent($user->id, $oldToken));

                            }
                        } catch (\Throwable $e) {

                            Log::error("Broadcast failed: " . $e->getMessage());

                        }

                        // Revoke old tokens
                        try {

                            $user->tokens()->delete();

                        } catch (\Throwable $e) {

                            Log::error("Token revoke failed: " . $e->getMessage());

                        }

                    } else {

                        return response()->json([
                            'success' => false,
                            'statusCode' => 409,
                            'message' => "You are already logged in on another device.",
                        ], 409);

                    }

                } else {

                    // Safety: clear stale tokens
                    $user->tokens()->delete();

                }

                // Generate new token
                $tokenResult = $user->createToken("Auth_token");
                $tokenString = $tokenResult->accessToken;
                $tokenId = $tokenResult->token->id;

                // Create new log session
                $logSession = LogSession::create([
                    'log_status' => 'i',
                    'login_time' => $currTime,
                    'ip' => $request->ip(),
                    'user_token' => $tokenString,
                    'token_id' => $tokenId,
                    'user_agent' => $request->userAgent(),
                    'user_id' => $user->id,
                ]);

                return response()->json([
                    'success' => true,
                    'statusCode' => 200,
                    'user_id' => $user->id,
                    'token' => $tokenString,
                    'user_type' => $userType,
                    'user_info' => $users,
                    "master_id"=> $master->id,
                    'master_user_type' => $master->user_type,
                    'log_session_id' => $logSession->id,
                ], 200);

            } else {

                return response()->json([
                    'success' => false,
                    'statusCode' => 401,
                    'message' => 'Unauthorized user type.',
                ], 401);

            }
        } catch (ValidationException $e) {

            return response()->json([
                'success' => false,
                'statusCode' => 422,
                'message' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {

            Log::error("Mobile login failed: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'statusCode' => 500,
                'message' => 'Something went wrong. Please try again.',
            ], 500);

        }

    }

    public function logout(Request $request, $log)
    {
        $user = Auth::user();

        $timeZone = null;

        function conTimezone($timezone, $time)
        {
            $convertedTime = Carbon::parse($time)->setTimezone($timezone);

            return $convertedTime->toDateTimeString();
        }

        $userInfo = UserInfo::where('user_id', $user->id)->first();

        $userType = $user->user_type;

        if ($userType == "TR") {
            
            $timeZone = $user->timezone;
            
        } else {

            $timeZone = $userInfo->home_terminal_timezone; // Fallback to UTC if null

        }

        $currentTime = Carbon::now()->setTimezone($timeZone)->toDateTimeString();

        LogSession::where('user_id', $user->id)
            ->update([
                'log_status' => 'o',
                'logout_time' => $currentTime,
            ]);

        $user->token()->delete();

        return response()->json([

            'status' => 'success',
            'statusCode' => 200,
            'message' => 'Successfully logged out'

        ]);
    }

}
