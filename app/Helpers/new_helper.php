<?php

use App\Services\WhatsAppService;

if (!function_exists('send_message_whatsApp')) {

    function send_message_whatsApp(
        array $numbers,
        string $contentSid,
        array $variables = []
    ) {
        $whatsApp = app(App\Services\WhatsAppService::class);

        foreach ($numbers as $number) {

            if (empty($number)) {
                continue;
            }

            $whatsApp->sendTemplateMessage(
                $number,
                $contentSid,
                $variables
            );
        }
    }
}