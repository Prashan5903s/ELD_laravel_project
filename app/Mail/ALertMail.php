<?php

namespace App\Mail;

use HTMLPurifier;
use Carbon\Carbon;
use App\Models\User;
use HTMLPurifier_Config;
use App\Models\EmailLogs;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Support\Facades\Config;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class ALertMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void

     */

    protected $template;

    public function __construct($template)
    {
        $this->template = $template;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {

        $data = $this->template;

        $data[0]['reason'] = ucfirst(strtolower($data[0]['reason']));

        $type = $data[0]['type'];

        if ($type == 3) {

            $template_text = str_replace([
                '{{ $reason }}',
                '{{ $name }}',
            ], [
                $data[0]['reason'],
                $data[0]['reason'],
            ], $data[2]);

        } else {
        
            $template_text = str_replace([
                '{{ $reason }}',
                '{{ $first_name }}',
                '{{ $last_name }}',
                '{{ first_name }}',
                '{{ last_name }}',
            ], [
                $data[0]['reason'],
                $data[0]['first_name'],
                $data[0]['last_name'],
                $data[0]['first_name'],
                $data[0]['last_name'],
        
            ], $data[2]);
        }


        return new Envelope(
            subject: $template_text,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {

        $config = \HTMLPurifier_Config::createDefault();

        $config->set('Cache.DefinitionImpl', null); // Disable caching

        $purifier = new HTMLPurifier($config);

        $data = $this->template;

        $key = config('app.Map_key');

        $type = $data[0]['type'];

        $eld_url = Config::get('app.eld_web');

        $eld_mail = Config::get('app.eld_mail');

        $data[0]['reason'] = ucfirst(strtolower($data[0]['reason']));

        $lowerCase = Str::lower($data[0]['reason']);

        if ($type == 1) {

            $template_text = str_replace([
                '{{ $reason }}',
                '{{ $reason1 }}',
                '{{ $name }}',
                '{{ $comp_url }}',
                '{{ $comp_logo }}',
                '{{ $comp_name }}',
                '{{ $message }}',
                '{{ $url }}',
                '{{ $eurl }}',
                '{{ $veh }}',
                '{{ $time }}',
                '{{ $first_name }}',
                '{{ $last_name }}',
                '{{ $eld_mail }}',
                '{{ $eld_url }}',
                '{{ $url }}'
            ], [
                $data[0]['reason'],
                $lowerCase,
                $data[0]['reason'],
                $eld_url,
                url('assets/media/logos/custom-1.png'),
                'UAT ELD',
                $data[0]['reason'] . ' ' . 'violation message',
                url('assets/media/logos/custom-1.png'),  // Use the url() function in PHP
                $eld_url,
                $data[0]['vehicle_name'],
                $data[0]['timess'], // Example: 13 Dec 2024, 02:30 PM
                $data[0]['first_name'],
                $data[0]['last_name'],
                $eld_mail,
                $eld_url,
                url('assets/media/logos/custom-1.png'),  // Use the url() function in PHP
            ], $data[1]);
        } else if ($type == 2) {

            $template_text = str_replace([
                '{{ $first_name }}',
                '{{ $last_name }}',
                '{{ $document_name }}',
                '{{ $comp_name }}',
                '{{ $comp_logo }}',
                '{{ $time }}',
                '{{ $comp_url }}',
            ], [
                $data[0]['first_name'],
                $data[0]['last_name'],
                $data[0]['document_name'],
                'UAT ELD',
                url('assets/media/logos/custom-1.png'),  // Generate full URL for image
                Carbon::parse($data[0]['timess'])->format('d M Y, h:i A'), // Example: 13 Dec 2024, 02:30 PM
                $eld_url,
            ], $data[1]);
        } else if ($type == 3) {

            $location = $data[0]['location'];

            $locationName = null;

            $locationData = json_decode($location, true); // Convert JSON string to associative array

            if ($locationData && isset($locationData['GeoLocation'])) {
                // Fetch latitude and longitude from the GeoLocation object
                $latitude = $locationData['GeoLocation']['Latitude'];
                $longitude = $locationData['GeoLocation']['Longitude'];

                // Make a request to the Google Geocoding API to convert lat/long to an address
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
            }

            $template_text = str_replace([
                '{{ $lName }}',
                '{{ $driver_name }}',
                '{{ $reason }}',
                '{{ $reason1 }}',
                '{{ $name }}',
                '{{ $comp_url }}',
                '{{ $comp_logo }}',
                '{{ $comp_name }}',
                '{{ $message }}',
                '{{ $url }}',
                '{{ $eurl }}',
                '{{ $veh }}',
                '{{ $time }}',
                '{{ $eld_mail }}',
                '{{ $eld_url }}',
                '{{ $url }}'
            ], [
                $locationName,
                $data[0]['driver_name'],
                $data[0]['reason'],
                $lowerCase,
                $data[0]['reason'],
                $eld_url,
                url('assets/media/logos/custom-1.png'),
                'UAT ELD',
                $data[0]['reason'] . ' ' . 'violation message',
                url('assets/media/logos/custom-1.png'),  // Use the url() function in PHP
                $eld_url,
                $data[0]['vehicle_name'],
                $data[0]['timess'], // Example: 13 Dec 2024, 02:30 PM
                $eld_mail,
                $eld_url,
                url('assets/media/logos/custom-1.png'),  // Use the url() function in PHP
            ], $data[1]);
        }

        $safeHtml = $purifier->purify($template_text);

        $rEmail = $data[0]['recipientEmail'];

        if ($rEmail && count($rEmail) > 0) {

            foreach ($rEmail as $emails) {

                $user = User::Where('email', $emails)->first();

                $userId = $user ? $user->id : 0;

                $timess = Carbon::parse($data[0]['timeSlot']);

                EmailLogs::create([
                    'user_id' => $userId,
                    'message_text' => $template_text,
                    'reciever_email' => $emails,
                    'template_id' => $data[0]['template_mail_id'],
                    'send_time' => $timess,
                    'type' => 0,
                    'is_send' => 1,
                ]);
            }

        }

        return new Content(
            view: 'emails.alert_mail',
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
