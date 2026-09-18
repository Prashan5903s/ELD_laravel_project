<?php

namespace App\Http\Controllers\Mobile\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\UserGroup;
use App\Models\UserMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        try {

            $loggedUser = Auth::user();

            $userId = $loggedUser->id;
            $masterId = $loggedUser->master_id;

            $users = User::where(function ($query) use ($masterId, $userId) {
                $query->where('master_id', $masterId)
                    ->where('id', '!=', $userId);
            })
                ->orWhere('id', $masterId)
                ->select(
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'user_type',
                    'mobile_no',
                    'gender',
                    'avatar_image',
                    'timezone'
                )
                ->get();

            $userMessages = collect();

            foreach ($users as $user) {

                $latestMessage = UserMessage::where('type', 0)
                    ->where(function ($query) use ($userId, $user) {

                        // Current user -> other user
                        $query->where(function ($q) use ($userId, $user) {
                            $q->where('sender_id', $userId)
                                ->where('reciever_id', $user->id);
                        })

                            // Other user -> current user
                            ->orWhere(function ($q) use ($userId, $user) {
                                $q->where('sender_id', $user->id)
                                    ->where('reciever_id', $userId);
                            });
                    })
                    ->orderBy('sent_time', 'DESC')
                    ->select("id", "type", "group_id", "reciever_id", "sender_id", "message_text", "image_url", "sent_time", "is_read")
                    ->first();

                $unreadCount = UserMessage::where('type', 0)
                    ->where('sender_id', $user->id)
                    ->where('reciever_id', $userId)
                    ->where('is_read', 0)
                    ->select("id", "type", "group_id", "reciever_id", "sender_id", "message_text", "image_url", "sent_time", "is_read")
                    ->count();


                $user->unread_count = $unreadCount;

                if ($latestMessage) {

                    $userMessages->push([
                        'type' => 'user',
                        'type_id' => $user->id,
                        'user' => $user,
                        'group' => null,
                        'message' => $latestMessage,
                        'sent_time' => $latestMessage->sent_time,
                    ]);
                }
            }

            $groupIds = UserGroup::where('user_id', $userId)
                ->pluck('group_id')
                ->toArray();


            $groups = Group::whereIn('group_id', $groupIds)
                ->select(
                    'group_id',
                    'group_name',
                    'group_title',
                    'group_description'
                )
                ->get();

            $groupMessages = collect();

            foreach ($groups as $group) {

                $latestMessage = UserMessage::where('type', 1)
                    ->where('group_id', $group->group_id)
                    ->orderBy('sent_time', 'DESC')
                    ->select("id", "type", "group_id", "reciever_id", "sender_id", "message_text", "image_url", "sent_time", "is_read")
                    ->first();

                $unreadGroupMessages = UserMessage::where('type', 1)
                    ->where('group_id', $group->group_id)
                    // Never count messages the user sent themselves as unread —
                    // they don't (and shouldn't) appear in their own is_read list.
                    ->where('sender_id', '!=', $userId)
                    ->where(function ($query) use ($userId) {

                        $query->whereNull('is_read')
                            ->orWhere('is_read', '')
                            ->orWhereRaw(
                                "FIND_IN_SET(?, REPLACE(is_read, ' ', '')) = 0",
                                [$userId]
                            );
                    })
                    ->select("id", "type", "group_id", "reciever_id", "sender_id", "message_text", "image_url", "sent_time", "is_read")
                    ->count();


                $group->unread_count = $unreadGroupMessages;

                if ($latestMessage) {

                    $groupMessages->push([
                        'type' => 'group',
                        'type_id' => $group->group_id,
                        'user' => null,
                        'group' => $group,
                        'message' => $latestMessage,
                        'sent_time' => $latestMessage->sent_time,
                    ]);
                }
            }

            $conversation = $userMessages
                ->merge($groupMessages)
                ->sortByDesc(function ($conversation) {
                    return $conversation['sent_time'];
                })
                ->values();

            $finalData = [
                'user' => $users,
                'group' => $groups,
                'conversation' => $conversation,
            ];

            return response()->json([
                'status' => true,
                'message' => 'Conversation data fetched successfully.',
                'data' => $finalData
            ], 200);
        } catch (\Throwable $error) {

            Log::error('Conversation index error', [
                'message' => $error->getMessage(),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $error->getMessage(),
            ], 500);
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
    public function store(Request $request) {}

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
