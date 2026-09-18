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

            // =====================================================
            // USERS (output shape)
            // =====================================================

            $usersOutput = $users->map(function ($user) {
                return [
                    'user_id'  => 'user_' . $user->id,
                    'name'     => trim($user->first_name . ' ' . $user->last_name),
                    'avatar'   => $user->avatar_image,
                    'role'     => $user->user_type === 'TR' ? 'admin' : 'driver',
                ];
            })->values();

            $conversations = collect();

            foreach ($users as $user) {

                $latestMessage = UserMessage::where('type', 0)
                    ->where(function ($query) use ($userId, $user) {

                        $query->where(function ($q) use ($userId, $user) {
                            $q->where('sender_id', $userId)
                                ->where('reciever_id', $user->id);
                        })
                            ->orWhere(function ($q) use ($userId, $user) {
                                $q->where('sender_id', $user->id)
                                    ->where('reciever_id', $userId);
                            });
                    })
                    ->orderBy('sent_time', 'DESC')
                    ->select("id", "type", "group_id", "reciever_id", "sender_id", "message_text", "image_url", "sent_time", "is_read")
                    ->first();

                if (!$latestMessage) {
                    continue;
                }

                $unreadCount = UserMessage::where('type', 0)
                    ->where('sender_id', $user->id)
                    ->where('reciever_id', $userId)
                    ->where('is_read', 0)
                    ->count();

                $conversations->push([
                    'conversation_id' => 'conv_' . $latestMessage->id,
                    'type'            => 'direct',
                    'user_id'         => 'user_' . $user->id,
                    'last_message'    => [
                        'message_id' => 'msg_' . $latestMessage->id,
                        'type'       => $latestMessage->image_url ? 'image' : 'text',
                        'text'       => $latestMessage->message_text,
                        'sender_id'  => 'user_' . $latestMessage->sender_id,
                        'created_at' => Carbon::parse($latestMessage->sent_time)->toISOString(),
                    ],
                    'unread_count' => $unreadCount,
                    'updated_at'   => Carbon::parse($latestMessage->sent_time)->toISOString(),
                    '_sort_time'   => $latestMessage->sent_time,
                ]);
            }

            $groupIds = UserGroup::where('user_id', $userId)
                ->pluck('group_id')
                ->toArray();

            $groups = Group::whereIn('group_id', $groupIds)
                ->select(
                    'group_id',
                    'group_name',
                    'group_title',
                    'group_description',
                    'created_by',
                    'created_at'
                )
                ->get();

            // =====================================================
            // GROUPS (output shape)
            // =====================================================

            $groupsOutput = $groups->map(function ($group) {
                $memberCount = UserGroup::where('group_id', $group->group_id)
                    ->where('is_active', 1)
                    ->count();

                return [
                    'group_id'     => 'group_' . $group->group_id,
                    'name'         => $group->group_name,
                    'avatar'       => $group->group_title ?? null,
                    'created_by'   => 'user_' . $group->created_by,
                    'member_count' => $memberCount,
                    'createdAt'    => Carbon::parse($group->created_at)->toISOString(),
                ];
            })->values();

            foreach ($groups as $group) {

                $latestMessage = UserMessage::where('type', 1)
                    ->where('group_id', $group->group_id)
                    ->orderBy('sent_time', 'DESC')
                    ->select("id", "type", "group_id", "reciever_id", "sender_id", "message_text", "image_url", "sent_time", "is_read")
                    ->first();

                if (!$latestMessage) {
                    continue;
                }

                $unreadGroupMessages = UserMessage::where('type', 1)
                    ->where('group_id', $group->group_id)
                    ->where('sender_id', '!=', $userId)
                    ->where(function ($query) use ($userId) {

                        $query->whereNull('is_read')
                            ->orWhere('is_read', '')
                            ->orWhereRaw(
                                "FIND_IN_SET(?, REPLACE(is_read, ' ', '')) = 0",
                                [$userId]
                            );
                    })
                    ->count();

                $sender = User::find($latestMessage->sender_id);

                $conversations->push([
                    'conversation_id' => 'conv_' . $latestMessage->id,
                    'type'            => 'group',
                    'group_id'        => 'group_' . $group->group_id,
                    'last_message'    => [
                        'message_id'  => 'msg_' . $latestMessage->id,
                        'type'        => $latestMessage->image_url ? 'image' : 'text',
                        'text'        => $latestMessage->message_text,
                        'sender_id'   => 'user_' . $latestMessage->sender_id,
                        'sender_name' => $sender
                            ? trim($sender->first_name . ' ' . $sender->last_name)
                            : 'Unknown',
                        'created_at'  => Carbon::parse($latestMessage->sent_time)->toISOString(),
                    ],
                    'unread_count' => $unreadGroupMessages,
                    'updated_at'   => Carbon::parse($latestMessage->sent_time)->toISOString(),
                    '_sort_time'   => $latestMessage->sent_time,
                ]);
            }

            $conversationsOutput = $conversations
                ->sortByDesc('_sort_time')
                ->values()
                ->map(function ($conversation) {
                    unset($conversation['_sort_time']);
                    return $conversation;
                });

            return response()->json([
                'status'  => true,
                'message' => 'Conversation data fetched successfully.',
                'data'    => [
                    'users'         => $usersOutput,
                    'groups'        => $groupsOutput,
                    'conversations' => $conversationsOutput,
                ],
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
