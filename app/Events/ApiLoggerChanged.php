<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ApiLogger;

class ApiLoggerChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $action;
    public $apiLogger;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($action, ApiLogger $apiLogger)
    {
        // $this->action = $action;
        // $this->apiLogger = $apiLogger;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new Channel('api-logger');
    }
}

