<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UnidentifiedNotification extends Notification
{
    use Queueable;

    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    // Only send via database
    public function via($notifiable)
    {
        return ['database'];
    }

    // Store the message in the database
    public function toArray($notifiable)
    {
        return [
            'title' => 'Unidentified Driving Detected',
            'message' => $this->message,
        ];
    }
}
