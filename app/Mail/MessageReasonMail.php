<?php

namespace App\Mail;

use HTMLPurifier;
use Carbon\Carbon;
use HTMLPurifier_Config;
use App\Models\Template;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class MessageReasonMail extends Mailable
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
        // Make sure data is received correctly
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        // Use dynamic subject
        return new Envelope(
            subject: $this->data['first_name'] . ' ' . $this->data['last_name'] . ' power cut received',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {

        $template = Template::where('template_id', 2)->first();        
        
        $template_text = $template->template_text;
        
        $location = json_decode($this->data['location']);
        
        $latitude = $location->GeoLocation->Latitude;

        $longitude = $location->GeoLocation->Longitude;
        
        $locationName = null;

        $key = config('app.Map_key');  // Fetch the Google Maps API key

        $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
            'latlng' => $latitude . ',' . $longitude,
            'key' => $key,
        ]);

        // Check if the response is successful
        if ($response->successful()) {
            $geocodeData = $response->json();

            if (!empty($geocodeData['results'])) {
                // Get the formatted address from the response
                $address = $geocodeData['results'][0]['formatted_address'];

                // Output the address
                $locationName = $address;
                
            }
            
        }

        // Replace placeholders with actual data
        $template_text = str_replace([
            '{{ $vName }}',
            '{{ $time }}',
            '{{ $lName }}',
            '{{ $first_name }}',
            '{{ $last_name }}',
            '{{ $eld_mail }}',
            '{{ $eld_url }}',
            '$eld_url',
            '{{ $url }}'
        ], [
            $this->data['vName'],
            Carbon::parse($this->data['time'])->format('j M, Y'),
            $locationName,
            $this->data['first_name'],
            $this->data['last_name'],
            $this->data['eld_mail'],
            $this->data['eld_url'],
            $this->data['eld_url'],
            url('assets/media/logos/custom-1.png'),
        ], $template_text);

        $this->data['mail']->update([
            'message_text' => $template_text 
        ]);
        
        $this->data['mail_master']->update([
            'message_text' => $template_text 
        ]);

        // Return the email content with purified HTML and other data
        return new Content(
            view: 'emails.messageReason',
            with: [
                'text' => $template_text,  // Pass sanitized HTML
                'data' => $this->data,  // Pass other data for email content
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
