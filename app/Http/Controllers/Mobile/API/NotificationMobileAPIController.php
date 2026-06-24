<?php
namespace App\Http\Controllers\Mobile\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationMobileAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $auth = Auth::check();

        if (! $auth) {
            return response()->json([
                'status'     => 'failure',
                'statusCode' => 401,
                'message'    => 'Not authenticated',
            ], 401);
        }

        $user = Auth::user();

        if ($user) {

            $notifications = $user->notifications->map(function ($notification) {
                return [
                    'id'         => $notification->id,
                    'type'       => $notification->type_id ?? null,
                    'data'       => $notification->data,
                    'read_at'    => $notification->read_at,
                    'created_at' => $notification->created_at,
                ];
            });

            // Fetch only unread notifications
            $unreadNotifications = $user->unreadNotifications->map(function ($notification) {
                return [
                    'id'         => $notification->id,
                    'type'       => $notification->type_id ?? null,
                    'data'       => $notification->data,
                    'read_at'    => $notification->read_at,
                    'created_at' => $notification->created_at,
                ];
            });

            // Fetch only read notifications
            $readNotifications = $user->readNotifications->map(function ($notification) {
                return [
                    'id'         => $notification->id,
                    'type'       => $notification->type_id ?? null,
                    'data'       => $notification->data,
                    'read_at'    => $notification->read_at,
                    'created_at' => $notification->created_at,
                ];
            });

            $response = [
                'all_notifications'          => $notifications,
                'unread_notifications'       => $unreadNotifications,
                'unread_notifications_count' => $unreadNotifications->count(),
                'read_notifications'         => $readNotifications,
            ];

            return response()->json([
                'status'     => 'success',
                'statusCode' => 200,
                'message'    => 'Data fetched successfully!',
                'data'       => $response,
            ], 200);

        } else {
            return response()->json([
                'status'     => 'failure',
                'statusCode' => 403,
                'message'    => 'User does not exist',
            ], 403);
        }

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
        $auth = Auth::check();

        if (! $auth) {
            return response()->json([
                'status'     => 'failure',
                'statusCode' => 401,
                'message'    => 'Not authenticated',
            ], 401);
        }

        $user = Auth::user();

        if ($user) {

            $unreadNotificationsCount = $user->unreadNotifications->count();

            if ($unreadNotificationsCount > 0) {

                $user->unreadNotifications->markAsRead();

            }

            return response()->json([
                'status'     => 'success',
                'statusCode' => 200,
                'message'    => 'Notification read successfully!',
            ], 200);

        } else {
            return response()->json([
                'status'     => 'failure',
                'statusCode' => 403,
                'message'    => 'User does not exist',
            ], 403);
        }
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
        //
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
        //
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
