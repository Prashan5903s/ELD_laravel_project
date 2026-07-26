<?php

namespace App\Mail;

use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DOTInspectionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->emailData['user']->first_name . ' ' . $this->emailData['user']->last_name . ' ' . 'has shared driver logs with you'
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
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null); // Disable caching
        $purifier = new HTMLPurifier($config);

        // Sanitize the text
        $template_text = $this->emailData['text']; // Get dynamic text

        // Replace placeholders with actual data
        // Replace placeholders with actual data
        $template_text = str_replace([
            '{{ $first_name }}',
            '{{ $last_name }}',
            '{{ $eld_mail }}',
            '{{ $eld_url }}',
            '{{ $first_day }}',
            '{{ $last_day }}',
            '$token',
            '$eld_url',
            '{{ $url }}'
        ], [
            $this->emailData['first_name'],
            $this->emailData['last_name'],
            $this->emailData['eld_mail'],
            $this->emailData['eld_url'],
            $this->emailData['first_day'],
            $this->emailData['last_day'],
            $this->emailData['token'],
            $this->emailData['eld_url'],
            url('assets/media/logos/custom-1.png'),  // Use the url() function in PHP
        ], $template_text);

        $this->emailData['mail']->update([
            'message_text' => $template_text,
        ]);

        // Now purify the HTML content after replacement
        $safeHtml = $purifier->purify($template_text);

        // Return the email content
        return new Content(
            view: 'emails.dot_inspection',
            with: [
                'text' => $safeHtml,  // Use the sanitized HTML content
                'data' => $this->emailData,  // Pass other data for email content
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
