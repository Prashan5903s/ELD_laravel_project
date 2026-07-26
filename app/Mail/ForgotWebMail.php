<?php

namespace App\Mail;

use HTMLPurifier;
use HTMLPurifier_Config;
use App\Models\EmailLogs;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Config;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgotWebMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Forgot Password Mail --DW ELD',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {

        // Initialize HTMLPurifier and disable caching
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null); // Disable caching
        $purifier = new HTMLPurifier($config);

        $template_text = $this->data[2]; // Get dynamic text

        $eldURL = Config::get('app.eld_web');

        // Replace placeholders with actual data
        // Replace placeholders with actual data
        $template_text = str_replace([
            '{{ $mail_url }}',
            '{{ $eld_url }}'
        ], [
            $eldURL . 'Auth/changePassword/' . $this->data[0],
            $eldURL
        ], $template_text);

        // Now purify the HTML content after replacement
        $safeHtml = $purifier->purify($template_text);

        $emailLog = EmailLogs::find($this->data[1]);

        $emailLog->update([
            'message_text' => $template_text,
        ]);

        return new Content(
            view: 'emails.forgot_mail',
            with: [
                'text' => $safeHtml,  // Use the sanitized HTML content
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
