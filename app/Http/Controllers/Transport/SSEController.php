<?php

namespace App\Http\Controllers\transport;

use App\Http\Controllers\Controller;
use App\Models\ApiLogger;
use App\Models\VehicleLogHistory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SSEController extends Controller
{
    public function index()
    {

        // Fetch the latest data from the database
        $vechile_history = VehicleLogHistory::whereDate('event_date_time', '=', now())->get();
        $routeData = [];

        foreach ($vechile_history as $data) {
            $location = json_decode($data['location'], true);
            $lat = $location['GeoLocation']['Latitude'];
            $lang = $location['GeoLocation']['Longitude'];
            $direction = $data['direction_alpha'];
            $locationKey = $lat . '_' . $lang;
            if (!isset($routeData[$locationKey])) {
                $routeData[$locationKey] = [
                    'latitude' => $lat,
                    'longitude' => $lang,
                    'directionAlpha' => $direction,
                ];
            }
        }
        $routeData = array_values($routeData);

        $response = new StreamedResponse(function () use ($routeData) {
            echo "data: " . json_encode($routeData) . "\n\n";
            ob_flush();
            flush();
        });
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Cach-Control', 'no-cache');
        return $response;
    }
}
