<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class AlertNotification extends Notification
{
    use Queueable;

    /**
     * Template data for the notification.
     *
     * @var mixed
     */
    protected $template;

    /**
     * Create a new notification instance.
     *
     * @param mixed $template
     */
    public function __construct($template)
    {
        $this->template = $template;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // Store notifications in the database
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {

        $data = $this->template;

        $type = $data[0]['type'];

        $eld_url = Config::get('app.eld_web');

        $template_text = null;

        if ($type == 1) {

            $template_text = str_replace([
                '{{ $first_name }}',
                '{{ $last_name }}',
                '{{ $time }}',
                '{{ $name }}',
            ], [
                $data[0]['first_name'],
                $data[0]['last_name'],
                $data[0]['timess'],
                $data[0]['reason'],
            ], $data[1]);
        } else if ($type == 2) {

            $template_text = str_replace([
                '{{ $first_name }}',
                '{{ $last_name }}',
                '{{ $document_name }}',
                '{{ $dateTime }}',
            ], [
                $data[0]['first_name'],
                $data[0]['last_name'],
                $data[0]['document_name'],
                Carbon::parse($data[0]['timess'])->format('d M Y, h:i A'), // Example: 13 Dec 2024, 02:30 PM
            ], $data[1]);
        } else if ($type == 3) {
            $template_text = str_replace([
                '{{ $driver_name }}',
                '{{ $event }}',
                '{{ $dateTime }}',
                '{{ $veh }}',
            ], [
                $data[0]['driver_name'],
                $data[0]['reason'],
                Carbon::parse($data[0]['timess'])->format('d M Y, h:i A'), // Example: 13 Dec 2024, 02:30 PM
                $data[0]['vehicle_name'],
            ], $data[1]);
        }

        return [
            'notifiable_id' => 20,
            'message' => $template_text,
            'url' => $eld_url
        ];
    }
}
