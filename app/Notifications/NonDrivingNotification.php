<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NonDrivingNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $url;
    protected $notifiable_id;

    /**
     * Create a new notification instance.
     *
     * @param string $message
     * @param string $url
     * @param int $notifiable_id
     * @return void
     */
    public function __construct($message, $url, $notifiable_id)
    {
        $this->message = $message;
        $this->url = $url;
        $this->notifiable_id = $notifiable_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'notifiable_id' => $this->notifiable_id,
            'message' => $this->message,
            'url' => $this->url,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        // Ensure array representation is similar to the database notification
        return [
            'notifiable_id' => $this->notifiable_id,
            'message' => $this->message,
            'url' => $this->url,
        ];
    }
}
