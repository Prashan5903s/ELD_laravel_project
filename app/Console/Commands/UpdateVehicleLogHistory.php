<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use App\Models\Device;
use App\Models\ApiLogger;
use Illuminate\Console\Command;
use App\Models\VehicleLogHistory;

class UpdateVehicleLogHistory extends Command
{
    /**

     * The name and signature of the console command.

     *

     * @var string

     */

    protected $signature = 'vehicle_log:update';
    /**

     * The console command description.

     *

     * @var string

     */

    protected $description = 'Update vehicle_log_history with new entries from api_logger';
    /**

     * Execute the console command.

     *

     * @return int

     */

    public function handle()
    {

        // Fetch new entries from api_logger where is_sync is 0 and processed is false

        $newEntries = ApiLogger::where('is_sync', 0)
            ->where('processed', 0)
            ->get();

        foreach ($newEntries as $entry) {

            $data = json_decode($entry->request_json, true);

            // Ensure $data is an array and not null

            if (is_array($data)) {

                $deviceId = null;

                // Check if 'Identifier' exists and is not null
                if (isset($data['Identifier'])) {

                    $device = Device::where('serial_number', $data['Identifier'])->first();
                    if ($device) {

                        $deviceId = $device->id;
                    }
                }

                // Convert 'event_date_time' from ISO 8601 to MySQL DATETIME format
                $eventDateTime = isset($data['EventDateTime']) ? $this->convertIso8601ToMySqlDateTime($data['EventDateTime']) : null;

                if ($data) {

                    // Check if an entry with the same identifier and event_date_time already exists

                    $existingEntry = VehicleLogHistory::where('identifier', $data['Identifier'] ?? null)
                        ->where('event_date_time', $eventDateTime)
                        ->first();

                    if (!$existingEntry) {

                        // Provide default values for missing keys and insert entry into vehicle_log_history

                        VehicleLogHistory::create([
                            'measurements' => $data['Measurements'] ?? null,
                            'device_id' => $deviceId,
                            'format' => $data['Format'] ?? null,
                            'identifier' => $data['Identifier'] ?? null,
                            'message_reason' => $data['MessageReason'] ?? null,
                            'event_time' => $data['EventTime'] ?? null,
                            'event_date_time' => $eventDateTime,
                            'location' => isset($data['Location']) ? json_encode($data['Location']) : null,
                            'unique_id' => $data['UniqueId'] ?? null,
                            'location_age_min' => $data['LocationAgeMin'] ?? null,
                            'operating_states' => isset($data['OperatingStates']) ? json_encode($data['OperatingStates']) : null,
                            'duration' => $data['Duration'] ?? null,
                            'speed' => $data['Speed'] ?? null,
                            'direction_alpha' => $data['DirectionAlpha'] ?? null,
                            'odometer' => $data['Odometer'] ?? null,
                            'num_satellites' => $data['NumSatellites'] ?? null,
                            'idle_duration' => $data['IdleDuration'] ?? null,
                            'obd_engine_rpm' => $data['OBDEngineRPM'] ?? null,
                            'obd_coolant' => $data['OBDCoolant'] ?? null,
                            'obd_speed' => $data['OBDSpeed'] ?? null,
                            'obd_odometer' => $data['OBDOdometer'] ?? null,
                            'obd_vin' => $data['OBDVIN'] ?? null,
                            'obd_fuel' => $data['OBDFuel'] ?? null,
                            'obd_throttle' => $data['OBDThrottle'] ?? null,
                            'obd_mpg' => $data['OBDMPG'] ?? null,
                            'obd_trip_mpg' => $data['OBDTripMPG'] ?? null,
                            'obd_instant_mpg' => $data['OBDInstantMPG'] ?? null,
                            'EngineLight' => isset($data['EngineLight']) ? ($data['EngineLight'] ?? null) : null,
                            'DiagnosticCodes' => isset($data['DiagnosticCodes']) ? json_encode($data['DiagnosticCodes']) : null,
                        ]);

                        // Mark entry as processed
                        $entry->processed = 1;

                        $entry->is_sync = 1;

                        $entry->save();
                    }
                } else {

                    // Handle the case where $data is not valid

                    // For example, log an error or handle it accordingly

                    // Log::error('Failed to decode JSON or JSON is invalid', ['request_json' => $entry->request_json]);

                }
            }
        }



        $this->info("Successfully updated vehicle_log_history.");



        return Command::SUCCESS;
    }





    // Helper method to convert ISO 8601 datetime to MySQL DATETIME format

    private function convertIso8601ToMySqlDateTime($iso8601DateTime)
    {

        try {

            // Create a DateTime object from the ISO 8601 string

            $dateTime = Carbon::parse($iso8601DateTime);

            // Format the DateTime object to MySQL DATETIME format

            return $dateTime->format('Y-m-d H:i:s');
        } catch (Exception $e) {

            // Handle date conversion errors

            // Log::error('Date conversion failed', ['iso8601_date_time' => $iso8601DateTime]);

            return null; // Or return a default value if needed

        }
    }
}
