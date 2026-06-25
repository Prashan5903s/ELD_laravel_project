<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client(
            env('TWILIO_ACCOUNT_SID'),
            env('TWILIO_AUTH_TOKEN')
        );
    }

    public function sendTemplateMessage(
        string $to,
        string $contentSid,
        array $variables = []
    ) {

        Log::info([
            'from' => env('TWILIO_WHATSAPP_FROM'),
            'to' => $to,
            'contentSid' => $contentSid,
        ]);

        return $this->client->messages->create(
            "whatsapp:$to",
            [
                'from' => env('TWILIO_WHATSAPP_FROM'),
                'contentSid' => $contentSid,
                'contentVariables' => json_encode($variables),
            ]
        );
    }
}