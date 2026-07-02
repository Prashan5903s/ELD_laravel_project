<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\ALertMail;
use App\Models\Template;
use App\Models\ListOption;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Notifications\AlertNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class AlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $datas;

    public function __construct($datas)
    {
        $this->datas = $datas;
    }

    public function handle()
    {

        $safety_type = ListOption::where('list_id', 'safety_type')
            ->pluck('short_name')
            ->toArray();

        foreach ($this->datas as $log) {

            $types = $log['type'];

            $reasons = $log['reason'] ?? null;

            $safetyExist = in_array($reasons, $safety_type);

            $idType = $types == 3
                ?
                ($safetyExist ? 1 : 3)
                :
                (
                    ($types == 2)
                    ?
                    4
                    :
                    2
                );

            $method = $log['method'];
            $email = $log['email'] ?? null;

            $whatsAppVariable = $log["whatsAppVariable"] ?? [];
            $contentId = $log["contentId"] ?? [];

            $masterId = $log['master_id'] ?? null;

            $masterEmail = $log['master_email'] ?? null;

            $recipientId = $log['recipientId'] ?? [];

            $recipientEmail = $log['recipientEmail'] ?? [];

            $recipientMobileNo = $log['recipientMobileNo'] ?? [];

            array_push($recipientEmail, 'keith@apnatelelink.us', 'a8018104141@gmail.com', $masterEmail);

            array_push($recipientId, $masterId);

            array_push($recipientMobileNo, "+916290975181");

            $mail_template = Template::where('template_id', $log['template_mail_id'])->first();
            $notify_template = Template::where('template_id', $log['template_notify_id'])->first();

            if (!$mail_template || !$notify_template) {
                Log::warning('Template not found', ['log' => $log]);
                continue;
            }

            $dataMail = [$log, $mail_template->template_text, $mail_template->template_subject];
            $dataNotify = [$log, $notify_template->template_text, $mail_template->template_subject];

            $mailAlert = new ALertMail($dataMail);
            $notifyAlert = new AlertNotification($dataNotify);

            try {
                switch ($method) {
                    case 1: // Both email and notification
                        if ($recipientEmail && count($recipientEmail) > 0) {
                            // Filter valid email addresses
                            $validEmails = array_filter(
                                $recipientEmail,
                                function ($email) {
                                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                                }
                            );

                            if (!empty($validEmails)) {
                                Mail::to($validEmails)->send($mailAlert);
                            } else {
                                Log::error('No valid email addresses for sending mail', ['emails' => [$email, $masterEmail]]);
                            }
                        }

                        if ($recipientId && count($recipientId) > 0) {
                            foreach ($recipientId as $rid) {
                                $user = User::find($rid);
                                if ($user) {
                                    $user->notify($notifyAlert);
                                    $notifyLog = $user->notifications()->latest('created_at')->first();

                                    if ($notifyLog) {
                                        $notifyLog->update(['type_id' => $idType]);
                                    }
                                }
                            }
                        }
                        break;
                    case 2: // Notification only
                        if ($recipientId && count($recipientId) > 0) {
                            foreach ($recipientId as $rid) {
                                $user = User::find($rid);
                                if ($user) {
                                    $user->notify($notifyAlert);
                                    $notifyLog = $user->notifications()->latest('created_at')->first();

                                    if ($notifyLog) {
                                        $notifyLog->update(['type_id' => $idType]);
                                    }
                                }
                            }
                        }
                        break;
                    case 3: // Email only
                        if ($recipientEmail && count($recipientEmail) > 0) {
                            // Filter valid email addresses
                            $validEmails = array_filter(
                                $recipientEmail,
                                function ($email) {
                                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                                }
                            );

                            if (!empty($validEmails)) {
                                Mail::to($validEmails)->send($mailAlert);
                            } else {
                                Log::error('No valid email addresses for sending mail', ['emails' => [$email, $masterEmail]]);
                            }
                        }
                        break;
                    case 4:
                        if ($recipientMobileNo && count($recipientMobileNo) > 0) {

                            Log::info('Sending WhatsApp message', ['recipientMobileNo' => $recipientMobileNo, 'contentId' => $contentId, 'whatsAppVariable' => $whatsAppVariable]);

                            send_message_whatsApp($recipientMobileNo, $contentId, $whatsAppVariable);
                        }

                        break;
                    default:
                        Log::warning('Unknown method type', ['method' => $method, 'log' => $log]);
                }
            } catch (\Exception $e) {
                Log::error('AlertJob Error: ' . $e->getMessage(), ['log' => $log]);
            }
        }
    }
}
