<?php

namespace App\Http\Controllers\Mobile\API;

use App\Models\User;
use App\Models\Template;
use App\Models\EmailLogs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Mail\ForgotPasswordMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserMobileAPIController extends Controller
{
    public function index($email)
    {
        // Validate email format
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email',  // Ensure the email is provided and in a valid format
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Invalid email format',
                'code' => 400,
            ]);
        }

        // Check if the email exists in the User table
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'failure',
                'message' => 'User email does not exist',
                'code' => 401,
            ]);
        }

        // Get user details
        $timezone = $user->timezone ?? config('app.timezone'); // Default to app timezone if not set
        $name = $user->first_name . ' ' . $user->last_name;

        // Retrieve the email template
        $template = Template::find(7);
        if (!$template) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Email template not found',
                'code' => 500,
            ]);
        }

        // Generate OTP
        $otp = rand(1000, 9999);

        // Get the current time in the user's timezone
        $currentTime = Carbon::now($timezone)->toDateTimeString();

        // Log email details in the database
        $emailLog = EmailLogs::create([
            'template_id' => $template->id,
            'sender_id' => $user->id,
            'reciever_email' => $email,
            'send_time' => $currentTime,
            'sender_token' => $otp,
            'type' => 1,
            'is_send' => 1,
        ]);

        // Prepare the email data
        $data = [$otp, $template->template_text, $emailLog->id];

        try {
            // Send the email
            Mail::to($email)->send(
                (new ForgotPasswordMail($data))->from(env('MAIL_FROM_ADDRESS'), $name)
            );

            return response()->json([
                'status' => 'success',
                'statusCode' => 200,
                'message' => 'Email sent successfully',
                'otp' => $otp,
                'mailLogId' => $emailLog->id,
            ]);
        } catch (\Exception $e) {
            // Handle email sending failure
            return response()->json([
                'status' => 'failure',
                'message' => 'Failed to send email: ' . $e->getMessage(),
                'code' => 500,
            ]);
        }
    }

    public function store(Request $request, $email)
    {
        // Validate email format
        $emailValidator = Validator::make(['email' => $email], [
            'email' => 'required|email',  // Ensure the email is provided and in a valid format
        ]);

        // Check if the email validation fails
        if ($emailValidator->fails()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Invalid email format',
                'code' => 400,
            ], 400);
        }

        // Fetch user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'failure',
                'message' => 'User email does not exist',
                'code' => 404, // Not Found status
            ], 404);
        }

        // Validate input for password and confirm_password
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8'],
            'confirm_password' => ['required', 'same:password'], // Ensure confirm_password matches password
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
                'code' => 422,
            ], 422);
        }

        // Update the user's password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password has been updated',
            'code' => 200,
        ], 200);
    }

}
