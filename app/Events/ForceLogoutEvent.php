<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ForceLogoutEvent implements ShouldBroadcast
{
    use SerializesModels;

    public string $token;
    public int $userId;

    public function __construct(int $userId, string $token)
    {
        $this->userId = $userId;
        $this->token  = $token;
    }

    public function broadcastOn()
    {
        // Channel name must match frontend subscription
        return new PrivateChannel("user-{$this->userId}");
    }

    public function broadcastAs()
    {
        // Alias for the event name
        return 'ForceLogoutEvent';
    }

    public function broadcastWith()
    {
        return [
            'type'  => 'LOGOUT',
            'token' => $this->token,
        ];
    }
}
